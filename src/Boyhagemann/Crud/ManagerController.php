<?php

namespace Boyhagemann\Crud;
use Boyhagemann\Form\FormBuilder;
use View;

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
        $fb->action(\URL::action(get_called_class() . '@createController'));
        $fb->text('original')->label('Original controller')->value(get_class($controller));
        $fb->text('controller')->label('Controller name')->value($model . 'Controller');
        $fb->text('path')->label('Path')->value('../app/controllers');
        $form = $fb->build();
                
        return View::make('crud::manager/manage', compact('form'));
    }
    
    public function create()
    {
        $controller = \App::make(\Input::get('original'));        
        
        $this->generator->setController($controller);
        
        $filename = \Input::get('path') . '/' . \Input::get('controller') . '.php';
        
        file_put_contents($filename, $this->generator->generate());  
        
        return Redirect::route('crud');
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