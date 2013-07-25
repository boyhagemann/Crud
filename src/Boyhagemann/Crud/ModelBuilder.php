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
	protected $relations = array();

	/**
	 * @var array
	 */
	protected $rules = array();

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
		if(!Schema::hasColumn($this->table, $name)) {
			$this->getBlueprint()->$type($name);
		}
	}

	/**
	 * Build the columns to the database
	 */
	public function export()
	{
		$this->getBlueprint()->build(DB::connection(), DB::connection()->getSchemaGrammar());
	}

	/**
	 * @return Model
	 */
	public function build()
	{
		if(!class_exists($this->name)) {
			$this->writeModel();
		}

		$this->export();

		return App::make($this->name);
	}

	/**
	 *
	 */
	public function writeModel()
	{
		$filename = '../' . $this->modelPath . '/' . $this->name . '.php';
		$contents = $this->buildFile();
		file_put_contents($filename, $contents);
		require_once $filename;
	}

	/**
	 * @return string
	 */
	public function buildFile()
	{
		$this->generator->setClass($this->name);
		$class = $this->generator->getClass($this->name);

		// Set the table name
		$class->addProperty('table', $this->table, PropertyGenerator::FLAG_PROTECTED);

		// Set the rules
		$class->addProperty('rules', $this->rules);

		// Add elements, only for relationships
		foreach($this->relations as $name => $relation) {

			$body = sprintf('return $this->%s(\'%s\');', $relation['type'], $relation['model']);
			$class->addMethod($name, array(), null, $body);
		}

		return $this->generator->generate();
	}

}