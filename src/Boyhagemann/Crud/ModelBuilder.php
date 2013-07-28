<?php

namespace Boyhagemann\Crud;

use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Illuminate\Database\Schema\Blueprint;
use DB,
    Schema,
    App,
    Str;

class ModelBuilder
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $modelPath = 'app/models';

    /**
     * @var Blueprint
     */
    protected $blueprint;

    /**
     * @var ClassGenerator
     */
    protected $generator;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var array
     */
    protected $relations = array();

    /**
     * @var array
     */
    public $rules = array();

    /**
     * @param FileGenerator $generator
     */
    public function __construct(FileGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * 
     * @param sting $table
     * @return $this
     */
    public function table($table)
    {
        $this->table = $table;
        $this->blueprint = new Blueprint($table);

        if (!Schema::hasTable($table)) {
            $this->blueprint->create();
            $this->blueprint->increments('id');
            $this->blueprint->timestamps();
        }
        
        return $this;
    }
    /**
     * 
     * @param string $alias
     * @return ModelBuilder
     */
    public function relation($alias)
    {  
        return $this->relations[$alias];        
    }

    /**
     * 
     * @param string $name
     * @param string $type
     * @param string|Model $model
     * @return ModelBuilder
     */
    public function createRelation($alias, $type, $model)
    {                      
        if(is_string($model)) {
            $model = App::make($model);
        }
        
        $table = $this->table . '_' . $model->getTable();
        
        $field = $this->buildNameFromClass($this->name) . '_id';        
        $field2 = $this->buildNameFromClass(get_class($model)) . '_id';
                
        $relation = App::make('Boyhagemann\Crud\ModelBuilder\Relation');
        $relation->setType($type);
        $relation->name(get_class($model));
        
        switch($type) {

            case 'hasMany':
                $relation->table($table);
                $relation->column($field, 'integer');        
                $relation->column($field2, 'integer');
//                $relation->getBlueprint()->unique(array($name, $field));
                break;
            
        }

        $this->relations[$alias] = $relation;
        
        return $this->relations[$alias];
    }
    
    protected function buildNameFromClass($class)
    {
        $nameParts = explode('\\', $class);
        return strtolower(end($nameParts));
    }


    /**
     * @return Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }

    /**
     * @param $field
     * @param $rules
     */
    public function validate($field, $rules)
    {
        $this->rules[$field] = $rules;
    }

    /**
     * @param string $name
     * @param string $type
     * @return $this
     */
    public function column($name, $type)
    {
        $this->columns[$name] = $type;

        if (!Schema::hasColumn($this->table, $name)) {
            $column = $this->getBlueprint()->$type($name);
            if (!isset($this->rules[$name]) || false !== strpos('required', $this->rules[$name])) {
                $column->nullable();
            }
        }
        
        return $this;
    }

    /**
     * Build the columns to the database
     */
    public function export()
    {        
        $this->getBlueprint()->build(DB::connection(), DB::connection()->getSchemaGrammar());

        // When there is no class name, no file has to be written to disk
        if(!$this->name) {
            return;
        }
        
        $parts = explode('\\', $this->name);
        $filename = '../' . $this->modelPath;
        for ($i = 0; $i < count($parts); $i++) {
            $filename .= '/' . $parts[$i];
            if ($i < count($parts) - 1) {
                @mkdir($filename);
            }
        }
        $filename .= '.php';

        $contents = $this->buildFile();
        file_put_contents($filename, $contents);
        require_once $filename;

        foreach ($this->relations as $relation) {
            $relation->export();
        }
    }

    /**
     * @return Model
     */
    public function build()
    {
        return App::make($this->name);
    }

    /**
     * @return string
     */
    public function buildFile()
    {
        $this->generator->setClass($this->name);
        $class = current($this->generator->getClasses());
        $class->setExtendedClass('\Eloquent');


        // Set the table name
        $class->addProperty('table', $this->table, PropertyGenerator::FLAG_PROTECTED);

        // Set the rules
        $class->addProperty('rules', $this->rules);

        $class->addProperty('guarded', array('id'), PropertyGenerator::FLAG_PROTECTED);

        $fillable = array_keys($this->columns);
        $class->addProperty('fillable', $fillable, PropertyGenerator::FLAG_PROTECTED);

        // Add elements, only for relationships
        foreach ($this->relations as $alias => $relation) {

            $body = sprintf('return $this->%s(\'%s\');', $relation->getType(), $relation->getName());
            
            $class->addMethod($alias, array(), null, $body);
        }

        return $this->generator->generate();
    }

}