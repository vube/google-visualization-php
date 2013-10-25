<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable;

use Vube\GoogleVisualization\DataSource\Exception\IndexOutOfBoundsException;

/**
 * TableRow class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class TableRow
{
	/**
	 * @var array
	 */
	private $cells = array();
	/**
	 * @var int
	 */
	private $numCells = 0;
	/**
	 * @var array
	 */
	private $customProperties = array();

	/**
	 * Constructor
	 * @param array $cells List of TableCell to add to this row
	 */
	public function __construct($cells = array())
	{
		$this->addCells($cells);
	}

	/**
	 * @return array
	 */
	public function getCells()
	{
		return $this->cells;
	}

	/**
	 * @return int
	 */
	public function getNumberOfCells()
	{
		return $this->numCells;
	}

	/**
	 * @param int $index Index of the table cell requested
	 * @return TableCell
	 * @throws \Vube\GoogleVisualization\DataSource\Exception\IndexOutOfBoundsException
	 */
	public function getCell($index)
	{
		if($index < 0 || $index >= $this->numCells)
			throw new IndexOutOfBoundsException($index);

		return $this->cells[$index];
	}

	/**
	 * @param TableCell|mixed $cell
	 */
	public function addCell($cell)
	{
		if(! $cell instanceof TableCell)
			$cell = new TableCell($cell);

		$this->cells[] = $cell;
		$this->numCells++;
	}

	/**
	 * @param array $cells List of TableCell|mixed
	 */
	public function addCells($cells)
	{
		foreach($cells as $cell)
			$this->addCell($cell);
	}

	/**
	 * @param int $index
	 * @param TableCell|mixed $cell
	 * @throws \Vube\GoogleVisualization\DataSource\Exception\IndexOutOfBoundsException
	 */
	public function setCell($index, $cell)
	{
		if($index < 0 || $index >= count($this->cells))
			throw new IndexOutOfBoundsException($index);

		if(! $cell instanceof TableCell)
			$cell = new TableCell($cell);

		$this->cells[$index] = $cell;
	}

	/**
	 * @return array
	 */
	public function getCustomProperties()
	{
		return $this->customProperties;
	}

	/**
	 * @param string $name Name of the custom property to retrieve.
	 * @return null|string
	 */
	public function getCustomProperty($name)
	{
		if(! isset($this->customProperties[$name]))
			return null;

		return $this->customProperties[$name];
	}

	/**
	 * @param string $name Name of the custom property to set.
	 * @param string $value Value of the custom property to set.
	 */
	public function setCustomProperty($name, $value)
	{
		$this->customProperties[$name] = $value;
	}
}