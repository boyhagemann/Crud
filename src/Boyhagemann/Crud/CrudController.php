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
    Session,
	Event;

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
	 * @var string
	 */
	protected $viewMode;

    /**
	 *
     * @param FormBuilder     $fb
     * @param ModelBuilder 	  $mb
     * @param OverviewBuilder $ob
     */
    public function __construct(FormBuilder $fb, ModelBuilder $mb, OverviewBuilder $ob)
    {
		$this->formBuilder = $fb;
		$this->modelBuilder = $mb;
		$this->overviewBuilder = $ob;
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
		Config::set('crud::config.title', 						$this->getModelBuilder()->getName());
		Config::set('crud::config.redirects.success.store', 	$this->getBaseRoute() . '.index');
		Config::set('crud::config.redirects.success.update', 	$this->getBaseRoute() . '.index');
		Config::set('crud::config.redirects.success.destroy', 	$this->getBaseRoute() . '.index');
		Config::set('crud::config.redirects.error.store', 		$this->getBaseRoute() . '.create');
		Config::set('crud::config.redirects.error.update', 		$this->getBaseRoute() . '.edit');

		Config::set('crud::config', array_replace_recursive(Config::get('crud::config'), $this->config()));
	}

	/**
	 * @param string $viewMode
	 */
	public function init($method)
	{
		$this->viewMode = $method;

		$fb = $this->formBuilder;
		$mb = $this->modelBuilder;
		$ob = $this->overviewBuilder;

		// Let's have the ModelBuilder interact with the FormBuilder.
		Event::listen('formBuilder.buildElement.post', array($this, 'buildFormElement'));

		// Use a unique name for the FormBuilder instance. This helps identifying the
		// right FormBuilder instance in event listeners.
		$fb->setName(get_called_class());

		// Extend the buildModel method to add columns and relations to your model.
		$this->buildModel($mb);

		// Extend the buildForm method to add form elements. These form elements are
		// translated to database columns using the event mentioned above.
		$this->buildForm($fb);

		// Setup the OverviewBuilder.
		$ob->setForm($fb->build());
		$ob->setModel($mb->build());

		// Extend the buildOverview method to configure the overview
		$this->buildOverview($ob);

		// There are several configuration options that you can set.
		// If they are not set yet, then we define some defaults.
		$this->buildConfig();

		// Now that everything is configured, let's trigger an event so
		// we can hook into this controller from the outside.
		Event::fire('crudController.init', array($this));

		Event::listen('crud::store.pre', array($this, 'onCreate'));
		Event::listen('crud::update.pre', array($this, 'onUpdate'));
	}

    /**
     * @return mixed
     */
    public function index()
    {
		$this->init(__METHOD__);

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
		$this->init(__METHOD__);

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
		$this->init(__METHOD__);

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

		Event::fire('crud::store.pre', $model);

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
		$this->init(__METHOD__);

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
		$this->init(__METHOD__);

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

		Event::fire('crud::update.pre', $model);

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
		$this->init(__METHOD__);

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

	/**
	 * @param \Boyhagemann\Form\Element\ElementInterface $element
	 */
	public function buildFormElement(\Boyhagemann\Form\Element\ElementInterface $element)
	{
		$mb = $this->getModelBuilder();
		$name = $element->getName();
		$options = $element->getOptions();
		$type = $element->getType();
		$rules = $element->getRules();


		switch($type) {

			case 'text':
				$mb->column($name)->type('string');
				break;

			case 'textarea':
				$mb->column($name)->type('text');
				break;

			case 'checkbox':
			case 'percent':
			case 'integer':
				$mb->column($name)->type('integer');
				break;

			case 'select':
				if($this->hasRule($name)->type('integer')) {
					$this->column($name)->type('integer');
				}
				else {
					$this->column($name)->type('string');
				}
				break;

			case 'modelSelect':
				$mb->column($name)->type('integer');
//				$mb->hasMany($element->getAlias());
				break;

		}

		if ($element->getRules()) {
			$mb->get($name)->validate($element->getRules());
		}

	}

	/**
	 * @return bool
	 */
	public function isOverview()
	{
		return $this->viewMode == __CLASS__ . '::index';
	}

	/**
	 * @return bool
	 */
	public function isCreate()
	{
		return $this->viewMode == __CLASS__ . '::create' || $this->viewMode == __CLASS__ . '::store';
	}

	/**
	 * @return bool
	 */
	public function isEdit()
	{
		return $this->viewMode == __CLASS__ . '::edit' || $this->viewMode == __CLASS__ . '::update';
	}

	/**
	 * @return bool
	 */
	public function isDelete()
	{
		return $this->viewMode == __CLASS__ . '::destroy';
	}

}