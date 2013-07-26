<?php

namespace Boyhagemann\Crud;

use Symfony\Component\Form\FormBuilder as FormFactory;

use Boyhagemann\Crud\FormBuilder\InputElement;
use Boyhagemann\Crud\FormBuilder\CheckableElement;
use Boyhagemann\Crud\FormBuilder\ModelElement;

class FormBuilder
{
	/**
	 * @var ModelBuilder
	 */
	protected $modelBuilder;

	/**
	 * @var FormFactory
	 */
	protected $factory;

	/**
	 * @param FormFactory $factory
	 */
	public function __construct(FormFactory $factory)
	{
		$this->factory = $factory;
	}

	/**
	 * @param ModelBuilder $modelBuilder
	 */
	public function setModelBuilder(ModelBuilder $modelBuilder)
	{
		$this->modelBuilder = $modelBuilder;
	}

	/**
	 * @return ModelBuilder
	 */
	public function getModelBuilder()
	{
		return $this->modelBuilder;
	}

	/**
	 * @return \Symfony\Component\Form\Form
	 */
	public function build()
	{
		foreach($this->elements as $name => $element) {
			$this->factory->add($name, $element->getType(), $element->getOptions());

			if($this->getModelBuilder() && $element->getRules()) {
				$this->getModelBuilder()->validate($name, $element->getRules());
			}
		}

		return $this->getFactory()->getForm();
	}

	/**
	 * @return mixed
	 */
	public function getFactory()
	{
		return $this->factory;
	}

	/**
	 * @param       $name
	 * @param       $element
	 * @param       $type
	 * @param array $options
	 * @return InputElement|CheckableElement|ModelElement
	 */
	protected function addElement($name, $element, $type, Array $options = array())
	{
		switch($element) {

			case 'input':
				$element = new InputElement($name, $type, $options);
				break;

			case 'checkable':
				$element = new CheckableElement($name, $type, $options);
				break;

			case 'model':
				$element = new ModelElement($name, $type, $options);
				break;
		}

		$this->elements[$name] = $element;
		return $element;
	}

	/**
	 * @param string $name
	 * @return InputElement
	 */
	public function text($name)
	{
		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'string');
		}

		return $this->addElement($name, 'input', 'text');
	}

	/**
	 * @param $name
	 * @return InputElement
	 */
	public function textarea($name)
	{
		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'text');
		}

		return $this->addElement($name, 'input', 'textarea');
	}

	/**
	 * @param $name
	 * @return InputElement
	 */
	public function integer($name)
	{
		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'integer');
		}

		return $this->addElement($name, 'input', 'integer');
	}

	/**
	 * @param $name
	 * @return InputElement
	 */
	public function percentage($name)
	{
		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'integer');
		}

		return $this->addElement($name, 'input', 'percent');
	}

	/**
	 * @param $name
	 * @return CheckableElement
	 */
	public function select($name)
	{
		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'integer');
		}

		return $this->addElement($name, 'checkable', 'choice', array(
			'multiple' => false,
			'expanded' => false,
		));
	}

	/**
	 * @param $name
	 * @return CheckableElement
	 */
	public function multiselect($name)
	{
		return $this->addElement($name, 'checkable', 'choice', array(
			'multiple' => true,
			'expanded' => false,
		));
	}

	/**
	 * @param $name
	 * @return CheckableElement
	 */
	public function radio($name)
	{
		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'string');
		}

		return $this->addElement($name, 'checkable', 'choice', array(
			'multiple' => false,
			'expanded' => true,
		));
	}

	/**
	 * @param $name
	 * @return CheckableElement
	 */
	public function checkbox($name)
	{
		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'string');
		}

		return $this->addElement($name, 'checkable', 'choice', array(
			'multiple' => true,
			'expanded' => true,
		));
	}

	/**
	 * @param $name
	 * @return InputElement
	 */
	public function submit($name)
	{
		return $this->addElement($name, 'input', 'submit');
	}


	/**
	 * @param $name
	 * @return ModelElement
	 */
	public function modelSelect($name)
	{
		return $this->addElement($name, 'model', 'choice');
	}

	/**
	 * @param $name
	 * @return ModelElement
	 */
	public function modelRadio($name)
	{
		return $this->addElement($name, 'model', 'choice', array(
			'multiple' => true,
			'expanded' => true,
		));
	}

	/**
	 * @param $name
	 * @return ModelElement
	 */
	public function modelCheckbox($name)
	{
		return $this->addElement($name, 'model', 'choice', array(
			'multiple' => true,
			'expanded' => false,
		));
	}

}