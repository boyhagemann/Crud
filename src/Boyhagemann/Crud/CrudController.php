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
	 */
	public function __construct(FormBuilder $formBuilder, OverviewBuilder $overviewBuilder, ModelBuilder $modelBuilder)
	{
		$this->formBuilder = $formBuilder;
		$this->overviewBuilder = $overviewBuilder;
		$this->modelBuilder = $modelBuilder;

		$this->buildModel($modelBuilder);
		$formBuilder->setModelBuilder($modelBuilder);

		$this->buildForm($formBuilder);
		$this->buildOverview($overviewBuilder);
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
	 * @return FormBuilder
	 */
	public function getFormBuilder()
	{
		return $this->formBuilder;
	}

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
		return $this->formBuilder->build()->createView();
	}

	/**
	 * @return mixed
	 */
	public function index()
	{
		$overview = $this->overviewBuilder->render();
		$class = get_called_class();

		return View::make('crud::crud/index', compact('overview', 'class'));
	}

	/**
	 * @return mixed
	 */
	public function create()
	{
		$form = $this->getForm();
		$model = $this->getModel();
		$action = get_called_class() . '@store';
		$errors = Session::get('errors');

		return View::make('crud::crud/create', compact('form', 'model', 'action', 'errors'));
	}

	public function store()
	{
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
}