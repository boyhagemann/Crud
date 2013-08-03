<?php

namespace Boyhagemann\Crud;

use Illuminate\Support\ServiceProvider;
use Route, Config;

class CrudServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->package('crud', 'crud');
                
        $this->app->register('DeSmart\Layout\LayoutServiceProvider');
        $this->app->register('Boyhagemann\Form\FormServiceProvider');
        $this->app->register('Boyhagemann\Model\ModelServiceProvider');
        $this->app->register('Boyhagemann\Overview\OverviewServiceProvider');
    }

    public function boot()
    {
        Route::get('crud',                  'Boyhagemann\Crud\ManagerController@index');
        Route::get('crud/scan',             'Boyhagemann\Crud\ManagerController@scan');
        Route::get('crud/manage/{class}',   'Boyhagemann\Crud\ManagerController@manage')->where('class', '(.*)');
        Route::post('crud/create',          'Boyhagemann\Crud\ManagerController@createController');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
    }

}