<?php

namespace Boyhagemann\Crud\FormBuilder;

class CheckableElement extends InputElement
{
	/**
	 * @param array $choices
	 * @return Element
	 */
	public function choices($choices)
	{
		$this->options['choices'] = $choices;
		return $this;
	}

}