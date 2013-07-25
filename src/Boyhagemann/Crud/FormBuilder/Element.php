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
		$type = $this->factory->get($this->name)->getType()->getInnerType()->getName();
		$options = $this->factory->get($this->name)->getOptions();
		$options['max_length'] = $size;

		$this->factory->add($this->name, $type, $options);
		return $this;
	}

	/**
	 * @param string $value
	 * @return Element
	 */
	public function value($value = '')
	{
		$type = $this->factory->get($this->name)->getType()->getInnerType()->getName();
		$options = $this->factory->get($this->name)->getOptions();
		$options['data'] = $value;

		$this->factory->add($this->name, $type, $options);
		return $this;
	}

	/**
	 * @param bool $required
	 * @return Element
	 */
	public function required($required = true)
	{
		$type = $this->factory->get($this->name)->getType()->getInnerType()->getName();
		$options = $this->factory->get($this->name)->getOptions();
		$options['required'] = $required;

		$this->factory->add($this->name, $type, $options);
		return $this;
	}
}