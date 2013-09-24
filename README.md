Crud
====

With this package you can:

* Build a form dynamically with the [Form Builder] (http://github.com/boyhagemann/Form) and have that form reflect your model. 
* Generate Eloquent models with your form using the [Model Builder] (http://github.com/boyhagemann/Model).
* Have an admin interface for your models, including all the CRUD routes automatically using the [Overview Builder] (http://github.com/boyhagemann/Overview).


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

# The Crud Manager interface
This package comes with a handy manager interface. 
It lets you generate new crud controllers with a simple form.
You can also copy crud controllers from existing packages and put them in your application folder.
That way you have total control of the crud controller without changing the original package.

The manager sits under this url:
```
http://{yourdomain}/crud
```
From there yout can generate a fresh controller file or convert an existing one from a package.


