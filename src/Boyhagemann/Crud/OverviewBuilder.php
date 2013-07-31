<?php

namespace Boyhagemann\Crud;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Illuminate\Database\Query\Builder as QueryBuilder;

class OverviewBuilder
{
	protected $model;
	protected $form;

	protected $fields = array();

	protected $limit = 5;

	/**
	 * @var QueryBuilder
	 */
	protected $queryBuilder;

	/**
	 * @param mixed $form
	 */
	public function setForm($form) {
		$this->form = $form;
	}

	/**
	 * @return mixed
	 */
	public function getForm() {
		return $this->form;
	}

	/**
	 * @param mixed $model
	 */
	public function setModel($model) {
		$this->model = $model;
	}

	/**
	 * @return mixed
	 */
	public function getModel() {
		return $this->model;
	}

	/**
	 * @return QueryBuilder
	 */
	public function getQueryBuilder()
	{
		if($this->queryBuilder) {
			return $this->queryBuilder;
		}

		$this->queryBuilder = $this->model->query();

		return $this->queryBuilder;
	}

	/**
	 * @param $limit
	 * @return $this
	 */
	public function display($limit)
	{
		$this->limit = $limit;
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
		$overview = new Overview();

		foreach($this->fields as $field) {
			$element = $this->form->get($field);
			$label = $element->createView()->vars['label'];
			$overview->label($field, $label);
		}

		$q = $this->getQueryBuilder();
		$collection = $q->paginate($this->limit);
		$overview->setCollection($collection);

		foreach($collection as $record) {

			$columns = array();
			foreach($this->fields as $field) {
				$columns[$field] = $this->buildColumn($field, $this->form->get($field), $record);
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
			$selected = array();
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