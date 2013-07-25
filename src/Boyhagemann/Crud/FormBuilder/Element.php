<?php

namespace Boyhagemann\Crud\FormBuilder;

class Element
{
	protected $factory;

	protected $name;

	public function __construct($name, $factory)
	{
		$this->name = $name;
		$this->factory = $factory;
	}

	/**
	 * @param string $label
	 * @return Element
	 */
	public function label($label)
	{
		$type = $this->factory->get($this->name)->getType()->getInnerType()->getName();
		$options = $this->factory->get($this->name)->getOptions();
		$options['label'] = $label;

		$this->factory->add($this->name, $type, $options);
		return $this;
	}

	/**
	 * @param string $label
	 * @return Element
	 */
	public function size($size)
	{
		return $this;
	}
}