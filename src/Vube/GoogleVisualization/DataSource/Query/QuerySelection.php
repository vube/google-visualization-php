<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query;
use Vube\GoogleVisualization\DataSource\Exception\IndexOutOfBoundsException;


/**
 * QuerySelection class
 *
 * @author Ross Perkins <ross@vubeology.com>
 */
class QuerySelection {

	private $numCols = 0;
	private $columns = array();

	public function addColumn($text, $label = null, $func = null)
	{
		$this->columns[] = array(
			'text' => $text,
			'label' => $label,
			'func' => $func,
		);
		$this->numCols++;
	}

	public function getNumberOfColumns()
	{
		return $this->numCols;
	}

	public function getColumnText($index)
	{
		if($index >= $this->numCols)
			throw new IndexOutOfBoundsException($index);
		return $this->columns[$index]['text'];
	}

	public function getColumnLabel($index)
	{
		if($index >= $this->numCols)
			throw new IndexOutOfBoundsException($index);
		return $this->columns[$index]['label'];
	}

	public function getColumnFunction($index)
	{
		if($index >= $this->numCols)
			throw new IndexOutOfBoundsException($index);
		return $this->columns[$index]['func'];
	}

	public function getAllColumnIds()
	{
		$names = array();
		foreach($this->columns as $column)
			$names[$column['text']] = 1;
		$names = array_keys($names);
		return $names;
	}
}