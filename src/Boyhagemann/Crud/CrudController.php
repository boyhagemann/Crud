<?php

namespace Boyhagemann\Crud;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Form\FormView;
use View, BaseController, Form, Validator, Input, Redirect, Session;

abstract class CrudController extends BaseController
{
	/**
	 * @var FormBuilder
	 */
	protected $formBuilder;

	/**
	 * @var OverviewBuilder
	 */
	protected $overviewBuilder;

	/**
	 * @var ModelBuilder
	 */
	protected $modelBuilder;

	/**
	 * @param FormBuilder     $formBuilder
	 * @param OverviewBuilder $overviewBuilder
	 * @param ModelBuilder 	  $modelBuilder
	 */
	public function __construct(FormBuilder $formBuilder, OverviewBuilder $overviewBuilder, ModelBuilder $modelBuilder)
	{
		$this->formBuilder = $formBuilder;
		$this->modelBuilder = $modelBuilder;
		$this->overviewBuilder = $overviewBuilder;

		$this->buildModel($modelBuilder);

		$formBuilder->setModelBuilder($modelBuilder);
		$this->buildForm($formBuilder);

		$formBuilder->build();
		$modelBuilder->export();
	}

	/**
	 * @param FormBuilder $formBuilder
	 */
	abstract public function buildForm(FormBuilder $formBuilder);

	/**
	 * @param OverviewBuilder $overviewBuilder
	 */
	abstract public function buildOverview(OverviewBuilder $overviewBuilder);

	/**
	 * @param OverviewBuilder $overviewBuilder
	 */
	abstract public function buildModel(ModelBuilder $modelBuilder);

	/**
	 * @return Model
	 */
	public function getModel()
	{
		return $this->modelBuilder->build();
	}

	/**
	 * @return FormView
	 */
	public function getForm()
	{
		return $this->formBuilder->build();
	}

	/**
	 * @return mixed
	 */
	public function index()
	{
		$model = $this->modelBuilder->build();
		$form = $this->formBuilder->build();

		$overviewBuilder = $this->overviewBuilder;
		$overviewBuilder->setForm($form);
		$overviewBuilder->setModel($model);

		$this->buildOverview($overviewBuilder);


		$overview = $overviewBuilder->build();

		$class = get_called_class();

		return View::make('crud::crud/index', compact('overview', 'class'));
	}

	/**
	 *
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		$form = $this->getForm();
		$model = $this->getModel();
		$action = get_called_class() . '@store';
		$errors = Session::get('errors');

		return View::make('crud::crud/create', compact('form', 'model', 'action', 'errors'));
	}

	/**
	 *
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		$form = $this->getForm();
		$model = $this->getModel();

		$v = Validator::make(Input::all(), $model->rules);

		if($v->fails()) {
			return Redirect::action(get_called_class() . '@create')->withErrors($v->messages());
		}

		foreach($model->getFillable() as $field) {
			$model->$field = Input::get($field);
		}
		$model->save();

		return Redirect::action(get_called_class() . '@index');
	}

	/**
	 *
	 *
	 * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$form = $this->getForm();
		$model = $this->getModel()->findOrFail($id);

		$action = get_called_class() . '@update';
		$errors = Session::get('errors');

		return View::make('crud::crud/edit', compact('form', 'model', 'action', 'errors'));
	}

	/**
	 *
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		$form = $this->getForm();
		$model = $this->getModel()->findOrFail($id);

		$v = Validator::make(Input::all(), $model->rules);

		if($v->fails()) {
			return Redirect::action(get_called_class() . '@edit', array($model->id))->withErrors($v->messages());
		}

		foreach($model->getFillable() as $field) {
			$model->$field = Input::get($field);
		}
		$model->save();

		return Redirect::action(get_called_class() . '@index');
	}

	/**
	 *
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy($id)
	{
		$form = $this->getForm();
		$model = $this->getModel()->findOrFail($id);

		$model->delete();

		return Redirect::action(get_called_class() . '@index');
	}
}