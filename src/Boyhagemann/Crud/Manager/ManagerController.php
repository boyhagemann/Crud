<?php

namespace Boyhagemann\Crud\Manager;

use View;

class ManagerController extends \BaseController
{
    protected $scanner;
    
    public function __construct(Scanner $scanner)
    {
        $this->scanner = $scanner;
    }
    
    public function managed()
    {
        $controllers = $this->scanner->scanForControllers();
        var_dump($controllers);
    }
    
    public function unmanaged()
    {
        $controllers = $this->scanner->scanForControllers();
        
        return View::make('crud::manager/unmanaged', compact('controllers'));
    }
    
    public function manage($class)
    {
        $controller = $this->getController($class);        
        $model =  $controller->getModelBuilder()->getName();
        
        $fb = \App::make('Boyhagemann\Crud\FormBuilder');
        $fb->action(\URL::action(get_called_class() . '@createController'));
        $fb->text('original')->label('Original controller')->value(get_class($controller));
        $fb->text('controller')->label('Controller name')->value($model . 'Controller');
        $fb->text('path')->label('Path')->value('../app/controllers');
        $form = $fb->build();
                
        return View::make('crud::manager/manage', compact('form'));
    }
    
    public function createController()
    {
        $controller = \App::make(\Input::get('original'));        
        
        $generator = \App::make('Boyhagemann\Crud\Generator\Controller');
        $generator->setController($controller);
        
        $filename = \Input::get('path') . '/' . \Input::get('controller') . '.php';
        
        file_put_contents($filename, $generator->generate());        
    }
    
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