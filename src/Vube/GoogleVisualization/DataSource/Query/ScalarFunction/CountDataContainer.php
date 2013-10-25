<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\ScalarFunction;

use Vube\GoogleVisualization\DataSource\DataTable\TableCell;
use Vube\GoogleVisualization\DataSource\DataTable\Value\NumberValue;


/**
 * CountDataContainer class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class CountDataContainer implements iDataContainer {

	private $count = 0;

	/**
	 * @param TableCell $cell
	 */
	public function addCell(TableCell $cell)
	{
		$this->count++;
	}

	/**
	 * @return NumberValue
	 */
	public function getComputedValue()
	{
		return new NumberValue($this->count);
	}
}