<?php

namespace Boyhagemann\Crud;

use Symfony\Component\Form\FormBuilder as FormFactory;

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
	 * @param FormRenderer $renderer
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
	 * @param $name
	 * @return FormBuilder\Element
	 */
	public function text($name)
	{
		$this->factory->add($name, 'text');

		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'string');
		}

		return new FormBuilder\Element($name, $this->factory);
	}

	public function textarea($name)
	{
		$this->factory->add($name, 'textarea');

		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'text');
		}
	}

	public function integer($name)
	{
		$this->factory->add($name, 'integer');

		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'integer');
		}
	}

	public function percentage($name)
	{
		$this->factory->add($name, 'percent');

		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'integer');
		}
	}

	public function select($name, $choices, Array $options = array())
	{
		$this->factory->add($name, 'choice', $options + array(
			'choices' => $choices,
			'multiple' => false,
			'expanded' => false,
		));

		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'integer');
		}
	}

	public function multiselect($name, $choices)
	{
		$this->factory->add($name, 'choice', array(
			'choices' => $choices,
			'multiple' => true,
			'expanded' => false,
		));
	}

	public function radio($name, $choices)
	{
		$this->factory->add($name, 'choice', array(
			'choices' => $choices,
			'multiple' => false,
			'expanded' => true,
		));

		if($this->getModelBuilder()) {
			$this->getModelBuilder()->column($name, 'string');
		}
	}

	public function checkbox($name, $choices)
	{
		$this->factory->add($name, 'choice', array(
			'choices' => $choices,
			'multiple' => true,
			'expanded' => true,
		));
	}

	public function submit($name, $label)
	{
		$this->factory->add($name, 'submit', array(
			'label' => $label,
		));
	}






	public function modelSelect($name, $model)
	{
		$choices = array(
				'test2222',
		);
		$this->select($name, $choices);

		$this->relations[$name] = array(
			'type' => 'belongsTo',
			'model' => $model
		);
	}

}