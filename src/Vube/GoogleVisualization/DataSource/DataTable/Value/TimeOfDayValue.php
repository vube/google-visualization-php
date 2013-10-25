<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value;

use Vube\GoogleVisualization\DataSource\Date;


/**
 * TimeOfDayValue class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class TimeOfDayValue extends Value {

	/**
	 * @param Date|string|int $value
	 */
	public function __construct($value)
	{
		if($value !== null && ! $value instanceof Date)
			$value = new Date($value);

		parent::__construct($value, ValueType::TIMEOFDAY);
	}

	public function __toString()
	{
		if($this->value === null)
			$output = "null";
		else
		{
			$output = $this->value->format("H:i:s");
			$micros = $this->value->getMicroseconds();
			if($micros > 0)
				$output .= ".".sprintf("%06d", $micros);
		}
		return $output;
	}
}