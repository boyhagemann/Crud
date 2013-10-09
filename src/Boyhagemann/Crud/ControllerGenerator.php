<?php

namespace Boyhagemann\Crud;

use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Boyhagemann\Form\Element\InputElement;
use Str;

class ControllerGenerator
{
	/**
	 * @var FileGenerator
	 */
	protected $generator;
        
    protected $controller;

	protected $class;

        /**
	 * @param FileGenerator $generator
	 */
	public function __construct(FileGenerator $generator)
	{
		$this->generator = $generator;
	}

	public function setController(CrudController $controller)
	{
		$this->controller = $controller;
	}

	public function setClassName($class)
	{
		$this->class = $class;
	}

	public function generate()
	{
		if($this->controller) {
			$modelBuilder = $this->controller->getModelBuilder();
			$className = $modelBuilder->getName();
		}
		else {
			$className = $this->class;
		}

		$class = new ClassGenerator();
		$class->setName($className . 'Controller');
		$class->setExtendedClass('CrudController');
		$class->addUse('Boyhagemann\Crud\CrudController');
		$class->addUse('Boyhagemann\Form\FormBuilder');
		$class->addUse('Boyhagemann\Model\ModelBuilder');
		$class->addUse('Boyhagemann\Overview\OverviewBuilder');

		$param = new ParameterGenerator();
		$param->setName('fb')->setType('FormBuilder');
		$body = $this->generateFormBuilderBody();
		$docblock = '@param FormBuilder $fb';
		$class->addMethod('buildForm', array($param), MethodGenerator::FLAG_PUBLIC, $body, $docblock);
                
		$param = new ParameterGenerator();
		$param->setName('mb')->setType('ModelBuilder');
		$body = sprintf('$mb->name(\'%s\')->table(\'%s\');' . PHP_EOL, $className, strtolower(str_replace('\\', '_', $className)));
		$body .= '$mb->autoGenerate();' . PHP_EOL;
		$docblock = '@param ModelBuilder $mb';
		$class->addMethod('buildModel', array($param), MethodGenerator::FLAG_PUBLIC, $body, $docblock);
                                
		$param = new ParameterGenerator();
		$param->setName('ob')->setType('OverviewBuilder');
		$body = '';
		$docblock = '@param OverviewBuilder $ob';
		$class->addMethod('buildOverview', array($param), MethodGenerator::FLAG_PUBLIC, $body, $docblock);


		$this->generator->setClass($class);

		return $this->generator->generate();
	}

	/**
	 * @return string
	 */
	protected function generateFormBuilderBody()
	{
		if(!$this->controller) {
			return '';
		}

		$formBuilder = $this->controller->getFormBuilder();
		$parts = array();

		foreach($formBuilder->elements as $element) {
			$parts[] = '$fb->' . $this->generateFormBuilderChain($element);
		}

		return implode(PHP_EOL, $parts);
	}

                
        
	/**
	 * @param InputElement $element
	 * @return string
	 */
	protected function generateFormBuilderChain(InputElement $element)
	{
		$parts = array();
		$data = $element->toArray();

		$parts[] = sprintf('%s(\'%s\')', $element->getType(), $element->getName());
		unset($data['type']);
		unset($data['name']);

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

		return implode('->', $parts) . ';';
	}

}