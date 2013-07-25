Crud
====

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
