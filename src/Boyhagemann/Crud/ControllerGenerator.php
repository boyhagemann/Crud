<?php

namespace Boyhagemann\Crud;

use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Boyhagemann\Form\Element\InputElement;

class ControllerGenerator
{
	/**
	 * @var FileGenerator
	 */
	protected $generator;
        
        protected $controller;

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

	public function generate()
	{
            $modelBuilder = $this->controller->getModelBuilder();
		$className = $modelBuilder->getName() . 'Controller';

		$class = new ClassGenerator();
		$class->setName($className);
		$class->setExtendedClass('CrudController');

		$param = new ParameterGenerator();
		$param->setName('fb')->setType('FormBuilder');
		$body = $this->generateFormBuilderBody();
		$docblock = '@param FormBuilder $fb';
		$class->addMethod('buildForm', array($param), MethodGenerator::FLAG_PUBLIC, $body, $docblock);
                
		$param = new ParameterGenerator();
		$param->setName('mb')->setType('ModelBuilder');
		$body = '';
		$docblock = '@param ModelBuilder $mb';
		$class->addMethod('buildModel', array($param), MethodGenerator::FLAG_PUBLIC, $body, $docblock);
                                
		$param = new ParameterGenerator();
		$param->setName('ob')->setType('OverviewBuilder');
		$body = '';
		$docblock = '@param OverviewBuilder $ob';
		$class->addMethod('buildOverview', array($param), MethodGenerator::FLAG_PUBLIC, $body, $docblock);


		$this->generator->setClass($class);
                $this->generator->setUses(array(
                    'Boyhagemann\Crud\CrudController',
                    'Boyhagemann\Form\FormBuilder',
                    'Boyhagemann\Model\ModelBuilder',
                    'Boyhagemann\Overview\OverviewBuilder',
                ));

		return $this->generator->generate();
	}

	/**
	 * @return string
	 */
	protected function generateFormBuilderBody()
	{
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