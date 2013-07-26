<?php

namespace Boyhagemann\Crud;

class Overview
{
	protected $labels = array();
	protected $rows = array();

	public function row($id, $columns)
	{
		$this->rows[$id] = new Overview\Row($columns);
	}

	public function label($name, $value)
	{
		$this->labels[$name] = $value;
	}

	public function labels()
	{
		return $this->labels;
	}

	/**
	 * @return array
	 */
	public function rows()
	{
		return $this->rows;
	}
}