<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value;

use Vube\GoogleVisualization\DataSource\Date;


/**
 * DateValue class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class DateValue extends Value {

	/**
	 * @param Date|string|int $value
	 */
	public function __construct($value)
	{
		if($value !== null && ! $value instanceof Date)
			$value = new Date($value);

		parent::__construct($value, ValueType::DATE);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		if($this->value === null)
			$output = "null";
		else
			$output = $this->value->format("Y-m-d");
		return $output;
	}
}