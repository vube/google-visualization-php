<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value;

use Vube\GoogleVisualization\DataSource\Date;


/**
 * DateTimeValue class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class DateTimeValue extends Value {

	/**
	 * @param Date|string|int $value
	 */
	public function __construct($value)
	{
		if($value !== null && ! $value instanceof Date)
			$value = new Date($value);

		parent::__construct($value, ValueType::DATETIME);
	}

	public function __toString()
	{
		if($this->value === null)
			$output = "null";
		else
			$output = $this->value->__toString();
		return $output;
	}
}