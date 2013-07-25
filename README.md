Crud
====

## What you can do

* Extend the CrudController and use all the methods according to a resourceful Laravel route.
* Build a form dynamically
* Use this form to generate an Eloquent model and save it in your application
* Create and update database table and columns needed for the model

## Example usage

```php
<?php

use Boyhagemann\Crud\CrudController;
use Boyhagemann\Crud\FormBuilderl;
use Boyhagemann\Crud\ModelBuilder;
use Boyhagemann\Crud\OverviewBuilder;

class NewsController extends CrudController
{

    public function buildForm(FormBuilder $fb)
    {
        $fb->text('title')->label('Title')->rules('required|alpha');
        $fb->textarea('body')->label('Body');
    }
    
    public function buildModel(ModelBuilder $mb)
    {
        $mb->name('Article');
        $mb->table('articles');
    }
    
    public function buildOverview(OverviewBuilder $ob)
    {
        $ob->fields(array('title', 'body');
        $ob->order('title');
    }

}
```
