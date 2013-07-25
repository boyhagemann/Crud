<?php

use Symfony\Component\Form\FormView;

/**
 *
 */
Form::macro('render', function(FormView $view, Model $model = null) {

	if($model) {
		$html = Form::model($model, $view->vars['action']);
	}
	else {
		$html = Form::open($view->vars['action']);
	}
	
	$html .= Form::submit('Save');
	$html .= Form::close();

	return $html;
});

/**
 * 
 */
Form::macro('renderFields', function(FormView $view) {

	$html = '';
	
	foreach($view->children as $child) {
		$html .= Form::formRow($child);
	}
	
	return $html;
});

/**
 *
 */
Form::macro('formRow', function(FormView $view, $level = 1) {

	$html = '';
	$vars = $view->vars;
	$type = $vars['block_prefixes'][$level];
	$name = $vars['name'];
	$label = $vars['label'] ?: $name;

	$formLabel = Form::label($name, $label);

	switch($type) {

		case 'integer':
		case 'percent':
		case 'text':
			$formElement = Form::text($name, $vars['value']);
			break;

		case 'choice':

			$choices = array();
			foreach($vars['choices'] as $choice) {
				$choices[$choice->value] = $choice->label;
			}

			if($vars['expanded']) {
				if($vars['multiple']) {
					$formElement = Form::multiRadio($name, $choices);
				}
				else {
					$formElement = Form::multiCheckbox($name, $choices);
				}
			}
			else {
				$formElement = Form::select($name, $choices);
			}
			break;

		default: return;
	}

	$html = sprintf('<div class="row">%s%s</div>', $formLabel, $formElement);

	return $html;
});


/**
 *
 */
Form::macro('multiCheckbox', function ($name, $multiOptions, Array $defaults = null) {

	$name .= '[]';
	$inputs = array();

	foreach ($multiOptions as $key => $value) {

		$default = is_array($defaults) && in_array($key, $defaults) ? $key : null;

		$inputName = sprintf('%s[%s]', $name, $key);
		$inputs[]  =
			Form::checkbox($name, $key, $default, array(
				'id' => $inputName,
			)) .
			Form::label($inputName, $value);
	}

	return implode('<br>', $inputs);
});


/**
 *
 */
Form::macro('multiRadio', function ($name, $multiOptions, Array $defaults = null) {

	$inputs = array();

	foreach ($multiOptions as $key => $value) {
		$inputName = sprintf('%s_%s', $name, $key);
		$inputs[]  =
			Form::radio($name, $key, null, array(
				'id' => $inputName,
			)) .
			Form::label($inputName, $value);
	}

	return implode('<br>', $inputs);
});
