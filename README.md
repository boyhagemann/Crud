Crud
====
With this package you can:

* Build a form dynamically with the [Form Builder] (http://github.com/boyhagemann/Form) and have that form reflect your model. 
* Generate Eloquent models with your form using the [Model Builder] (http://github.com/boyhagemann/Model).
* Have an admin interface for your models using the [Overview Builder] (http://github.com/boyhagemann/Overview).
* Point a resource route to a CrudController instance and you are ready to rock!


## Install
Use [Composer] (http://getcomposer.org) to install the package into your application
```json
require {
    "boyhagemann/crud": "dev-master"
}
```

Then add the following line in app/config/app.php:
```php
...
"Boyhagemann\Crud\CrudServiceProvider"
...
```

## Example usage
The first thing we need to do is create a controller that extends from the CrudController.
This CrudController expects 3 methods to be implemented, just like the example below.

```php
<?php

use Boyhagemann\Crud\CrudController;
use Boyhagemann\Crud\FormBuilder;
use Boyhagemann\Crud\ModelBuilder;
use Boyhagemann\Crud\OverviewBuilder;

class NewsController extends CrudController
{

    public function buildForm(FormBuilder $fb)
    {
        $fb->text('title')->label('Title')->rules('required|alpha');
        $fb->textarea('body')->label('Body');
        $fb->radio('online')->choices(array('no', 'yes'))->label('Show online?');
        
        // You can use a fluent typing style
        $fb->modelSelect('category_id')
           ->model('Category')
           ->label('Choose a category')
           ->query(function($q) {
             $q->orderBy('title');
           });
           
        // Change an element
        $fb->get('title')->label('What is the title?');
    }
    
    public function buildModel(ModelBuilder $mb)
    {
        $mb->name('Article');
        $mb->table('articles');
        
        // Other options
        $mb->folder('app/models');
    }
    
    public function buildOverview(OverviewBuilder $ob)
    {
        $ob->fields(array('title', 'body');
        $ob->order('title');
    }

}
```

After you have created your controller, just create a resourceful route pointing to your controller.
```php
Route::resource('admin/news', 'NewsController');
```

That's it. Open your browser and enter the route you just created.

## Auto-generating models
You might have noticed that your Eloquent model does not exist yet. 
So have the hell is this baby working one might ask.
Well, the package checks if the model exists yet in the IoC container.
If it doesn't, then the Eloquent model file is written and the database table is created.


If you wanna skip the auto-generating part in your application, just set autoGenerate to 'false' in yout ModelBuilder like this:
```php

class My\Fancy\ArticleController extends CrudController
{
    public function buildModel(ModelBuilder $mb)
    {
        $mb->autoGenerate(false); // defaults to true;
    }
}

```

## Auto-updating models
During development it may be handy to keep updating your database the moment you changed your FormBuilder configuration.
There is an auto-updating property in the CrudController that can be set to 'true'.
```php

class My\Fancy\ArticleController extends CrudController
{
    public function buildModel(ModelBuilder $mb)
    {
        $mb->autoUpdate(true); // defaults to false;
    }
}
```


# Manage your controllers
This package comes with a handy manager interface. 
It lets you generate new crud controllers with a simple form.
You can also copy crud controllers from existing packages and put them in your application folder.
That way you have total control of the crud controller without changing the original package.

The manager sits under this url:
```
http://{yourdomain}/crud
```
From there yout can generate a fresh controller file or convert an existing one from a package.


