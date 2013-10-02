<?php

namespace Boyhagemann\Crud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Boyhagemann\Form\FormBuilder;
use Boyhagemann\Model\ModelBuilder;
use Boyhagemann\Overview\OverviewBuilder;
use View,
    BaseController,
    Validator,
    Input,
    Redirect,
	Config,
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
	 * @var Config
	 */
	protected $config;

    /**
     * @param FormBuilder     $formBuilder
     * @param OverviewBuilder $overviewBuilder
     * @param ModelBuilder 	  $modelBuilder
     */
    public function __construct(FormBuilder $formBuilder, OverviewBuilder $overviewBuilder, ModelBuilder $modelBuilder)
    {
		$formBuilder->setName(get_called_class());

        $this->formBuilder = $formBuilder;
        $this->modelBuilder = $modelBuilder;
        $this->overviewBuilder = $overviewBuilder;

        $this->buildModel($modelBuilder);
        $this->buildForm($formBuilder);

        $modelBuilder->setFormBuilder($formBuilder);

        $model  = $modelBuilder->build();
        $form   = $formBuilder->build();

        $overviewBuilder->setForm($form);
        $overviewBuilder->setModel($model);
        $this->buildOverview($overviewBuilder);

		$this->buildConfig();
    }


    /**
     * @param FormBuilder $fb
     */
    abstract public function buildForm(FormBuilder $fb);

    /**
     * @param OverviewBuilder $ob
     */
    abstract public function buildOverview(OverviewBuilder $ob);

    /**
     * @param OverviewBuilder $mb
     */
    abstract public function buildModel(ModelBuilder $mb);

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
	 * Override this method to provide a custom config
	 *
	 * @return array
	 */
	public function config()
	{
		return array();
	}

	/**
	 *
	 */
	public function buildConfig()
	{
		Config::set('crud::config.redirects.success.store', 	$this->getBaseRoute() . '.index');
		Config::set('crud::config.redirects.success.update', 	$this->getBaseRoute() . '.index');
		Config::set('crud::config.redirects.success.destroy', 	$this->getBaseRoute() . '.index');
		Config::set('crud::config.redirects.error.store', 		$this->getBaseRoute() . '.create');
		Config::set('crud::config.redirects.error.update', 		$this->getBaseRoute() . '.edit');

		Config::set('crud::config', array_replace_recursive(Config::get('crud::config'), $this->config()));
	}

    /**
     * @return mixed
     */
    public function index()
    {
        $overview = $this->getOverview();
        $route = $this->getBaseRoute();
		$title = Config::get('crud::config.title');
		$view = Config::get('crud::config.view.index');

        return View::make($view, compact('title', 'overview', 'route'));
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
		$title = Config::get('crud::config.title');
		$view = Config::get('crud::config.view.create');

        return View::make($view, compact('title', 'form', 'model', 'route', 'errors'));
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
		$success = Config::get('crud::config.redirects.success.store');
		$error = Config::get('crud::config.redirects.error.store');

        $v = Validator::make(Input::all(), $model->rules);

        if ($v->fails()) {
            Input::flash();
            return Redirect::route($error)->withErrors($v->messages());
        }

        $this->prepare($model);

        $model->save();

        $this->saveRelations($model);

        return Redirect::route($success);
    }

    /**
     *
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
		$config = Config::get('crud::config');
        $model = $this->getModelWithRelations()->findOrFail($id);
        $form = $this->getForm($model->toArray());
        $route = $this->getBaseRoute();
        $errors = Session::get('errors');
		$title = Config::get('crud::config.title');
		$view = Config::get('crud::config.view.edit');

        return View::make($view, compact('title', 'form', 'model', 'route', 'errors'));
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
		$success = Config::get('crud::redirects.success.update');
		$error = Config::get('crud::redirects.error.update');

        $v = Validator::make(Input::all(), $model->rules);

        if ($v->fails()) {
            Input::flash();
            return Redirect::route($error, array($model->id))->withErrors($v->messages());
        }

        $this->prepare($model);

        $model->save();

        $this->saveRelations($model);

        return Redirect::route($success);
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
		$success = Config::get('crud::redirects.success.destroy');

        $model->delete();

        return Redirect::route($success);
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->modelBuilder->build();
    }

    /**
     * @return Model
     */
    public function getOverview()
    {
        return $this->overviewBuilder->build();
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
	 * Get the base route where the crud controller is working from. This is needed for
	 * redirecting after saving the model.
	 *
	 * The routes can be changed thru a config file or thru the config() method.
	 *
     * @return string
     */
    public function getBaseRoute()
    {
		// If there is a base route, simply return it
		if(Config::has('crud::config.baseroute')) {
			return Config::get('crud::config.baseroute');
		}

        $resourceDefaults = array('index', 'create', 'store', 'show', 'edit', 'update', 'destroy');
        $routeName = \Route::currentRouteName();

		// Just to be safe here, make sure the route is a resource. A resource has dots in
		// route name, where normal routes have a different structure. They start with
		// 'GET /news' for instance.
        if(strpos(' ', $routeName)) {
            throw new \Exception('Route must be a resource');
        }

		// Remove the action part of the route, so we get our base route
        foreach ($resourceDefaults as $default) {
            $routeName = str_replace('.' . $default, '', $routeName);
        }

		// Set it in the config, so we don't have to process the base route again
		Config::set('crud::config.baseroute', $routeName);

        return $routeName;
    }

}