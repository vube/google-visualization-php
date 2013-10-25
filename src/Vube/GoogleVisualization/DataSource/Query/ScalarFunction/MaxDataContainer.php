<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\ScalarFunction;

use Vube\GoogleVisualization\DataSource\DataTable\TableCell;
use Vube\GoogleVisualization\DataSource\DataTable\Value\NumberValue;


/**
 * MaxDataContainer class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class MaxDataContainer implements iDataContainer {

	private $max = null;

	/**
	 * @param TableCell $cell
	 */
	public function addCell(TableCell $cell)
	{
		if(! $cell->getValue()->isNull())
		{
			$value = $cell->getValue()->getRawValue();

			if($this->max < $value)
				$this->max = $value;
			else if($this->max === null)
				$this->max = $value;
		}
	}

	/**
	 * @return NumberValue
	 */
	public function getComputedValue()
	{
		return new NumberValue($this->max);
	}
}