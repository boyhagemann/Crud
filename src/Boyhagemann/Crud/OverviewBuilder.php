<?php

namespace Boyhagemann\Crud;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OverviewBuilder
{
	protected $fields = array();

	protected $options = array();

	/**
	 * @var ModelBuilder
	 */
	protected $modelBuilder;

	/**
	 * @var FormBuilder
	 */
	protected $formBuilder;

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
	 * @param FormBuilder $formBuilder
	 */
	public function setFormBuilder(FormBuilder $formBuilder)
	{
		$this->formBuilder = $formBuilder;
	}

	/**
	 * @return FormBuilder
	 */
	public function getFormBuilder()
	{
		return $this->formBuilder;
	}

	public function display($limit)
	{
		$this->options['limit'] = $limit;
	}

	/**
	 * @param array $fields
	 */
	public function fields(Array $fields)
	{
		$this->fields = $fields;
	}

	/**
	 * @return string
	 */
	public function build()
	{
		$model = $this->getModelBuilder()->build();
		$form = $this->getFormBuilder()->build();
		$overview = new Overview();

		foreach($this->fields as $field) {
			$element = $form->get($field);
			$label = $element->createView()->vars['label'];
			$overview->label($field, $label);
		}

		$records = $model->all();

		foreach($records as $record) {

			$columns = array();
			foreach($this->fields as $field) {
				$columns[$field] = $this->buildColumn($field, $form->get($field), $record);
			}

			$overview->row($record->id, $columns);
		}

		return $overview;
	}

	/**
	 * @param $field
	 * @param $form
	 * @param $record
	 * @return string
	 */
	public function buildColumn($field, $form, $record)
	{
		$type = $form->getConfig()->getType()->getInnerType();
		$value = $record->$field;

		if($type instanceof ChoiceType) {
			$choices = $form->createView()->vars['choices'];
			foreach($choices as $choice) {

				if(in_array($choice->value, (array) $value)) {
					$selected[] = $choice->label;
				}
			}

			return implode(', ', $selected);
		}

		return $value;
	}
}