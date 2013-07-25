<?php

namespace Boyhagemann\Crud;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Form\FormView;
use View, BaseController, Event, Form;

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
		return View::make('crud::crud/index', compact('overview'));
	}

	/**
	 * @return mixed
	 */
	public function create()
	{
		$form = $this->getForm();
		$model = $this->getModel();

		return View::make('crud::crud/create', compact('form'));
	}

	public function store()
	{
		$model = $this->getModel();
	}
}