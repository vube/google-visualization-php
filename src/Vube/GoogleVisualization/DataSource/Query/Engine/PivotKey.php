<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Engine;


/**
 * PivotKey class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class PivotKey {

	/**
	 * @var array
	 */
	private $columnValues;
	/**
	 * @var array
	 */
	private $rowIndexes;
	/**
	 * @var string
	 */
	private $serial;

	/**
	 * @param array $columnValues
	 */
	public function __construct($columnValues)
	{
		$this->columnValues = $columnValues;
		$this->rowIndexes = array();

		$this->serial = self::hash($columnValues);
	}

	/**
	 * @param int $rowIndex
	 */
	public function addRowIndex($rowIndex)
	{
		$this->rowIndexes[] = $rowIndex;
	}

	/**
	 * @return array
	 */
	public function getColumnValues()
	{
		return $this->columnValues;
	}

	/**
	 * @return int
	 */
	public function getFirstRowIndex()
	{
		return $this->rowIndexes[0];
	}

	/**
	 * @return array
	 */
	public function getRowIndexes()
	{
		return $this->rowIndexes;
	}

	/**
	 * @param array $columnValues
	 * @return string
	 */
	public static function hash($columnValues)
	{
		// Convert all the column values into strings if they're objects
		$numColumns = count($columnValues);
		for($i=0; $i<$numColumns; $i++)
			if(is_object($columnValues[$i]))
				$columnValues[$i] = $columnValues[$i]->__toString();

		// Now hash the strings
		$hash = json_encode($columnValues);
		return $hash;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->serial;
	}
}