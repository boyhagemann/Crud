<?php

namespace Boyhagemann\Crud\FormBuilder;

class CheckableElement extends InputElement
{
	/**
	 * @param array $choices
	 * @return $this
	 */
	public function choices($choices)
	{
		$this->options['choices'] = $choices;
		return $this;
	}

}