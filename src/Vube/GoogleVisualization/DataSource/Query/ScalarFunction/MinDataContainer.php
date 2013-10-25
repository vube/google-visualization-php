<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\ScalarFunction;

use Vube\GoogleVisualization\DataSource\DataTable\TableCell;
use Vube\GoogleVisualization\DataSource\DataTable\Value\NumberValue;


/**
 * MinDataContainer class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class MinDataContainer implements iDataContainer {

	private $min = null;

	/**
	 * @param TableCell $cell
	 */
	public function addCell(TableCell $cell)
	{
		if(! $cell->getValue()->isNull())
		{
			$value = $cell->getValue()->getRawValue();

			if($this->min > $value)
				$this->min = $value;
			else if($this->min === null)
				$this->min = $value;
		}
	}

	/**
	 * @return NumberValue
	 */
	public function getComputedValue()
	{
		return new NumberValue($this->min);
	}
}