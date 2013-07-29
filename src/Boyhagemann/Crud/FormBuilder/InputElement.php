<?php

namespace Boyhagemann\Crud\FormBuilder;

use Boyhagemann\Crud\ModelBuilder;

class InputElement
{
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $formType;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @var string
	 */
	protected $rules;

	/**
	 * @param       $name
	 * @param       $formType
	 * @param       $type
	 * @param array $options
	 */
	public function __construct($name, $formType, $type, Array $options = array())
	{
		$this->name = $name;
		$this->formType = $formType;
		$this->type = $type;
		$this->options = $options;
	}

	public function toArray()
	{
		return array(
			'name' => $this->name,
			'type' => $this->type,
			'rules' => $this->rules,
			'label' => $this->getOption('label'),
			'size' => $this->getOption('max_length'),
		);
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getFormType()
	{
		return $this->formType;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @param string $name
	 * @return mixed|null
	 */
	public function getOption($name)
	{
		if(!isset($this->options[$name])) {
			return;
		}

		return $this->options[$name];
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

        /**
	 * @return array
	 */
	public function getRules()
	{
		return $this->rules;
	}

	/**
	 * @param string $label
	 * @return $this
	 */
	public function label($label)
	{
		$this->options['label'] = $label;
		return $this;
	}

	/**
	 * @param integer $size
	 * @return $this
	 */
	public function size($size)
	{
		$this->options['max_length'] = $size;
		return $this;
	}
	/**
	 * @param string $value
	 * @return $this
	 */
	public function value($value)
	{
		$this->options['data'] = $value;
		return $this;
	}

	/**
	 * @param bool $required
	 * @return $this
	 */
	public function required($required = true)
	{
		if($required) {
			$this->rules('required');
		}

		$this->options['required'] = $required;
		return $this;
	}

	/**
	 * @param string $rules
	 * @return $this
	 */
	public function rules($rules)
	{
		$parts = explode('|', $rules);
		if($this->rules) {
			$parts = array_merge($parts, explode('|', $this->rules));
		}
		$this->rules = implode('|', array_unique($parts));

		return $this;
	}
}