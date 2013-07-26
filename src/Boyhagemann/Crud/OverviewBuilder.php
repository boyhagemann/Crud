<?php

namespace Boyhagemann\Crud;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Illuminate\Database\Query\Builder as QueryBuilder;

class OverviewBuilder
{
	protected $fields = array();

	/**
	 * @var QueryBuilder
	 */
	protected $queryBuilder;

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
	 * @return QueryBuilder
	 */
	public function getQueryBuilder()
	{
		if($this->queryBuilder) {
			return $this->queryBuilder;
		}

		$this->queryBuilder = $this->getModelBuilder()->build()->query();

		return $this->queryBuilder;
	}

	/**
	 * @return FormBuilder
	 */
	public function getFormBuilder()
	{
		return $this->formBuilder;
	}

	/**
	 * @param $limit
	 * @return $this
	 */
	public function display($limit)
	{
		$this->getQueryBuilder()->take($limit);
		return $this;
	}

	/**
	 * @param $order
	 * @param $direction
	 * @return $this
	 */
	public function order($order, $direction = null)
	{
		$this->getQueryBuilder()->orderBy($order, $direction);
		return $this;
	}

	/**
	 * @param array $fields
	 */
	public function fields(Array $fields)
	{
		$this->fields = $fields;
	}

	/**
	 * @param \Closure $callback
	 * @return $this
	 */
	public function query(\Closure $callback)
	{
		$callback($this->getQueryBuilder());
		return $this;
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

		foreach($this->getQueryBuilder()->get() as $record) {

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