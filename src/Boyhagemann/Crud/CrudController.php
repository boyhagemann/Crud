<?php

namespace Boyhagemann\Crud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use View,
    BaseController,
    Validator,
    Input,
    Redirect,
    Session;

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
     * 
     * @return ModelBuilder
     */
    public function getModelBuilder()
    {
        return $this->modelBuilder;
    }
    
    /**
     * 
     * @return FormBuilder
     */
    public function getFormBuilder()
    {
        return $this->formBuilder;
    }
    
    /**
     * 
     * @return OverviewBuilder
     */
    public function getOverviewBuilder()
    {
        return $this->overviewBuilder;
    }    

    /**
     * @return mixed
     */
    public function index()
    {
		$this->getModelBuilder();

        $model = $this->modelBuilder->build();
        $form = $this->formBuilder->build();
        $overviewBuilder = $this->overviewBuilder;

        $overviewBuilder->setForm($form);
        $overviewBuilder->setModel($model);
        $this->buildOverview($overviewBuilder);

        $overview = $overviewBuilder->build();
		$route = $this->getBaseRoute();

        return View::make('crud::crud/index', compact('overview', 'route'));
    }

    /**
     *
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
		$this->getBaseRoute();

        $form = $this->getForm();
        $model = $this->getModel();
        $errors = Session::get('errors');
		$route = $this->getBaseRoute();

        return View::make('crud::crud/create', compact('form', 'model', 'route', 'errors'));
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
		$route = $this->getBaseRoute();

        $v = Validator::make(Input::all(), $model->rules);

        if ($v->fails()) {
            Input::flash();
            return Redirect::route($route . '.create')->withErrors($v->messages());
        }

        $this->prepare($model);

        $model->save();

        $this->saveRelations($model);

        return Redirect::route($route . '.index');
    }

    /**
     *
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $model = $this->getModelWithRelations()->findOrFail($id);
        $form = $this->getForm($model->toArray());
		$route = $this->getBaseRoute();
        $errors = Session::get('errors');

        return View::make('crud::crud/edit', compact('form', 'model', 'route', 'errors'));
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
		$route = $this->getBaseRoute();

        $v = Validator::make(Input::all(), $model->rules);

        if ($v->fails()) {
            Input::flash();
            return Redirect::route($route . '.edit', array($model->id))->withErrors($v->messages());
        }

        $this->prepare($model);

        $model->save();

        $this->saveRelations($model);

        return Redirect::route($route . '.index');
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
		$route = $this->getBaseRoute();

        $model->delete();

        return Redirect::route($route . '.index');
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->modelBuilder->build();
    }

    /**
     * 
     * @return Model
     */
    public function getModelWithRelations()
    {
        $model = $this->getModel();
        foreach ($this->modelBuilder->getRelations() as $alias => $relation) {
            if ($relation->getType() == 'belongsToMany') {
                $model = $model->with($alias);
            }
        }

        return $model;
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    public function getForm($values = null)
    {
        if (!$values) {
            $values = Input::old();
        }

        $this->formBuilder->defaults($values);
        return $this->formBuilder->build();
    }

    /**
     * 
     * @param Model $model
     */
    protected function prepare(Model $model)
    {
        foreach (Input::all() as $name => $value) {

            if (in_array($name, $model->getFillable())) {
                $model->$name = $value;
            }
        }
    }

    /**
     * 
     * @param Model $model
     */
    protected function saveRelations(Model $model)
    {
        foreach (Input::all() as $name => $value) {

            if (method_exists($model, $name) && $model->$name() instanceof Relations\BelongsToMany) {
                $model->$name()->sync($value);
            }
        }
    }

	/**
	 * @return string
	 */
	public function getBaseRoute()
	{
		$resourceDefaults = array('index', 'create', 'store', 'show', 'edit', 'update', 'destroy');
		$routeName = \Route::currentRouteName();

		if(strpos('', $routeName)) {
			throw new \Exception('Route must be a resource');
		}

		foreach($resourceDefaults as $default) {
			$routeName = str_replace('.' . $default, '', $routeName);
		}

		return $routeName;
	}

}