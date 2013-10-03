<?php

namespace Boyhagemann\Crud;
use Boyhagemann\Form\FormBuilder;
use View, Input, URL, App, Redirect;

class ManagerController extends \BaseController
{
    protected $scanner;
    protected $generator;
    protected $formBuilder;

    public function __construct(Scanner $scanner, ControllerGenerator $generator, FormBuilder $formBuilder)
    {
        $this->scanner = $scanner;
        $this->generator = $generator;
        $this->formBuilder = $formBuilder;
    }
    
    public function index()
    {
        $controllers = $this->scanner->scanForControllers(array('../app/controllers'));
        
        return View::make('crud::manager/index', compact('controllers'));
    }
    
    public function scan()
    {
        $controllers = $this->scanner->scanForControllers(array('../workbench', '../vendor'));
        
        return View::make('crud::manager/scan', compact('controllers'));
    }

    public function manage($class)
    {
        $controller = $this->getController($class);
        $model =  $controller->getModelBuilder()->getName();
        
        $fb = $this->formBuilder;
        $fb->action(URL::action(get_called_class() . '@store'));
        $fb->text('original')->label('Original controller')->value(get_class($controller));
        $fb->text('controller')->label('Controller name')->value($model . 'Controller');
        $fb->text('path')->label('Path')->value('../app/controllers');
        $form = $fb->build();
                
        return View::make('crud::manager/manage', compact('form'));
    }

	public function create()
	{
		$fb = $this->formBuilder;
		$fb->action(URL::action(get_called_class() . '@store'));
		$fb->text('class')->label('Model class name');
		$fb->text('url')->label('Url to the model overview');
		$fb->text('path')->label('Path')->value('../app/controllers');
		$form = $fb->build();

		return View::make('crud::manager/create', compact('form'));
	}
    
    public function store()
    {
		if(Input::has('original')) {
			$controller = App::make(Input::get('original'));
			$this->generator->setController($controller);
			$class = Input::get('controller');
			$filename = \Input::get('path') . '/' . $class . '.php';
		}
		else {
			$this->generator->setClassName(Input::get('class'));
			$class = Input::get('class') . 'Controller';
			$filename = \Input::get('path') . '/' . $class . '.php';
		}

		// Write the new controller file to the controller folder
        file_put_contents($filename, $this->generator->generate());

		// Add resource route to routes.php
		$line = sprintf(PHP_EOL . 'Route::resource(\'%s\', \'%s\');', Input::get('url'), $class);
		file_put_contents(app_path() . '/routes.php', $line, FILE_APPEND);

		// Redirect to the resource url overview
        return Redirect::to(Input::get('url'));
    }
    
    /**
     * 
     * @param string $key
     * @return CrudController
     */
    protected function getController($key)
    {
        $class = str_replace('/', '\\', $key);
        return \App::make($class);        
    }

    public function fromJson()
    {
        
    }

    public function toJson($filename)
    {
        var_dump($filename); exit;
    }

}