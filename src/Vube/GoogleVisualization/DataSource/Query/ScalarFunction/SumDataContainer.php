<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\ScalarFunction;

use Vube\GoogleVisualization\DataSource\DataTable\TableCell;
use Vube\GoogleVisualization\DataSource\DataTable\Value\NumberValue;


/**
 * SumDataContainer class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class SumDataContainer implements iDataContainer {

	private $numCells = 0;
	private $totalValue = 0;

	/**
	 * @param TableCell $cell
	 */
	public function addCell(TableCell $cell)
	{
		$this->numCells++;
		$this->totalValue += $cell->getValue()->getRawValue();
	}

	/**
	 * @return NumberValue
	 */
	public function getComputedValue()
	{
		return new NumberValue($this->totalValue / $this->numCells);
	}
}