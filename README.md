Crud
====

## What you can do

* Extend the CrudController and use all the methods according to a resourceful Laravel route.
* Build a form dynamically
* Use this form to generate an Eloquent model and save it in your application
* Create and update database table and columns needed for the model
* Use data from other models as form elements
* Included are some handy form macros to automatically render the whole form


##

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
