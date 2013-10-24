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

	public function addColumn($text, $label = null)
	{
		$this->columns[] = array(
			'text' => $text,
			'label' => $label,
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
}