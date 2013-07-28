<?php

namespace Boyhagemann\Crud\FormBuilder;

use App;

class ModelElement extends CheckableElement
{
	/**
	 * @var string|\Illuminate\Database\Eloquent\Model
	 */
	protected $model;

	protected $key;

	protected $field;

	protected $callback;
        
        /**
	 * @param $model
	 * @return $this
	 */
	public function model($model)
	{                
		if($this->modelBuilder) {
                    
                    if($this->getOption('multiple')) {
                        $this->modelBuilder->createRelation($this->name, 'hasMany', $model);
                    }
                    else {
                        $this->modelBuilder->createRelation($this->name, 'belongsTo', $model);
                    }

		}
                
		$this->model = $model;
		return $this;
	}

	/**
	 * @param $key
	 * @return $this
	 */
	public function key($key)
	{
		$this->key = $key;
		return $this;
	}

	/**
	 * @param $field
	 * @return $this
	 */
	public function field($field)
	{
		$this->field = $field;
		return $this;
	}

	/**
	 * @param Closure $callback
	 */
	public function query(Closure $callback)
	{
		$this->callback = $callback;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
            if(!$this->hasOption('choices')) {
                $this->options['choices'] = $this->buildChoices();
            }
            
            return parent::getOptions();
	}
        
        protected function buildChoices()
        {                
            if(is_string($this->model)) {
                    $this->model = App::make($this->model);
            }

            $q = $this->model->query();

            if($this->callback) {
                    $this->callback($q);
            }

            $key = $this->key ? $this->key : 'id';
            $field = $this->field ? $this->field : "title";

            return $q->lists($field, $key);            
        }
        
        /**
         * 
         * @param string $name
         * @return bool
         */
        public function hasOption($name)
        {
            return isset($this->options[$name]);            
        }
        
        /**
         * 
         * @param string $name
         * @return mixed
         */
        public function getOption($name)
        {            
            if(!$this->hasOption($name)) {
                return;
            }
            
            return $this->options[$name];
        }

}