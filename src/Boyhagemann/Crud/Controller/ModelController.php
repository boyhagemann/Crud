<?php

namespace Boyhagemann\Crud\Controller;

use Boyhagemann\Crud\FormBuilder;
use Boyhagemann\Crud\ModelBuilder;
use Boyhagemann\Crud\CrudController;
use Zend\Code\Generator\FileGenerator;
use View, BaseController, App;

class ModelController extends BaseController
{
	/**
	 * @var ModelBuilder
	 */
	protected $modelBuilder;
	/**
	 * @var FormBuilder
	 */
	protected $formBuilder;

	/**
	 * @var FileGenerator
	 */
	protected $generator;

	/**
	 * @param ModelBuilder $modelBuilder
	 */
	public function __construct(ModelBuilder $modelBuilder, FileGenerator $generator, FormBuilder $formBuilder)
	{
		$this->modelBuilder = $modelBuilder;
		$this->formBuilder = $formBuilder;
		$this->generator = $generator;
	}

	/**
	 * @param $class
	 * @throws Exception
	 */
	public function create($class)
	{
		$class = str_replace('/', '\\', $class);

		$controller = App::make($class);

		if(!$controller instanceof CrudController) {
			throw new \Exception('class must extend Boyhagemann\Crud\CrudController');
		}

		$formBuilder = $controller->getFormBuilder();
		$this->modelBuilder->setFormBuilder($formBuilder);

		$model = $this->modelBuilder->build();
//		var_dump($model);

		$this->formBuilder->text('class');
		$this->formBuilder->text('location');
		$this->formBuilder->submit('create', 'Create');
		$form = $this->formBuilder->build();
		$form = $this->formBuilder->render();

		return View::make('crud::model/create', compact('form'));
	}

	public function store()
	{

	}
}