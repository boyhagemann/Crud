<?php

namespace Boyhagemann\Crud\FormBuilder;

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
	 * @var array
	 */
	protected $options;

	/**
	 * @var string
	 */
	protected $rules;

	/**
	 * @param       $name
	 * @param       $type
	 * @param array $options
	 */
	public function __construct($name, $type, Array $options = array())
	{
		$this->name = $name;
		$this->type = $type;
		$this->options = $options;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
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