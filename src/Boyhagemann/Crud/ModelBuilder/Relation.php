<?php

namespace Boyhagemann\Crud\ModelBuilder;

use Boyhagemann\Crud\ModelBuilder;
use DB;

class Relation extends ModelBuilder
{    
    protected $type;
    
    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getName()
    {
        return $this->name;
    }
}