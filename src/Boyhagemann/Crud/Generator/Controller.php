<?php

namespace Boyhagemann\Crud\Generator;

use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\ParameterGenerator;

use Boyhagemann\Crud\FormBuilder;
use Boyhagemann\Crud\ModelBuilder;

class Controller
{
	/**
	 * @var ClassGenerator
	 */
	protected $generator;

	protected $formBuilder;

	protected $modelBuilder;

	/**
	 * @param FileGenerator $generator
	 */
	public function __construct(FileGenerator $generator)
	{
		$this->generator = $generator;
	}

	/**
	 * @param FormBuilder $formBuilder
	 */
	public function setFormBuilder(FormBuilder $formBuilder) {
		$this->formBuilder = $formBuilder;
	}

	/**
	 * @return FormBuilder
	 */
	public function getFormBuilder() {
		return $this->formBuilder;
	}

	/**
	 * @param ModelBuilder $modelBuilder
	 */
	public function setModelBuilder(ModelBuilder $modelBuilder) {
		$this->modelBuilder = $modelBuilder;
	}

	/**
	 * @return ModelBuilder
	 */
	public function getModelBuilder() {
		return $this->modelBuilder;
	}



	public function generate()
	{
		$className = $this->modelBuilder->getName() . 'Controller';

		$class = new ClassGenerator();
		$class->setName($className);
		$class->setExtendedClass('CrudController');

		$param = new ParameterGenerator();
		$param->setName('fb')->setType('FormBuilder');
		$body = $this->generateFormBuilderBody();
		$docblock = '@param FormBuilder $fb';
		$class->addMethod('buildForm', array($param), MethodGenerator::FLAG_PUBLIC, $body, $docblock);


		$this->generator->setUse('Boyhagemann\Crud\CrudController');
		$this->generator->setClass($class);

		var_dump($this->generator->generate()); exit;
	}

	/**
	 * @return string
	 */
	protected function generateFormBuilderBody()
	{
		$parts = array();

		foreach($this->formBuilder->elements as $element) {
			$parts[] = '$fb->' . $this->generateFormBuilderChain($element);
		}

		return implode(PHP_EOL, $parts);
	}

	/**
	 * @param FormBuilder\InputElement $element
	 * @return string
	 */
	protected function generateFormBuilderChain(FormBuilder\InputElement $element)
	{
		$parts = array();
		$data = $element->toArray();

		$parts[] = sprintf('%s(\'%s\')', $element->getType(), $element->getName());
		unset($data['type']);

		foreach($data as $name => $value) {

			if(!$value) {
				continue;
			}

			if(is_numeric($value)) {
				$part = sprintf('%s(%s)', $name, $value);
			}
			else {
				$part = sprintf('%s(\'%s\')', $name, $value);
			}
			$parts[] = $part;
		}

		return implode('->', $parts);
	}

}