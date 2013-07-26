<?php

namespace Boyhagemann\Crud;

use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Illuminate\Database\Schema\Blueprint;
use DB, Schema, App;

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
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param string $table
	 */
	public function setTable($table)
	{
		$this->table = $table;
		$this->blueprint = new Blueprint($table);

		if(!Schema::hasTable($table)) {
			$this->blueprint->create();
			$this->blueprint->increments('id');
			$this->blueprint->timestamps();
		}
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
	 */
	public function column($name, $type)
	{
		$this->columns[$name] = $type;

		if(!Schema::hasColumn($this->table, $name)) {
			$column = $this->getBlueprint()->$type($name);
			if(!isset($this->rules[$name]) || false !== strpos('required', $this->rules[$name])) {
				$column->nullable();
			}
		}
	}

	/**
	 * Build the columns to the database
	 */
	public function export()
	{
		$this->getBlueprint()->build(DB::connection(), DB::connection()->getSchemaGrammar());

		$parts = explode('\\', $this->name);
		$filename = '../' . $this->modelPath;
		for($i = 0; $i < count($parts); $i++) {
			$filename .= '/' . $parts[$i];
			if($i < count($parts) - 1) {
				@mkdir($filename);
			}
		}
		$filename .= '.php';

		$contents = $this->buildFile();
		file_put_contents($filename, $contents);
		require_once $filename;
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
		foreach($this->relations as $name => $relation) {

			$body = sprintf('return $this->%s(\'%s\');', $relation['type'], $relation['model']);
			$class->addMethod($name, array(), null, $body);
		}

		return $this->generator->generate();
	}

}