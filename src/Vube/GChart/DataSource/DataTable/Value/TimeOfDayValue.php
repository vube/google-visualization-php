<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\Value;

use Vube\GChart\DataSource\Date;


/**
 * TimeOfDayValue class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class TimeOfDayValue extends Value {

	public function __construct(Date $value)
	{
		parent::__construct($value, ValueType::TIMEOFDAY);
	}

	public function toString()
	{
		$hours = $this->value->format("G"); // 0..23
		$mins = 0 + $this->value->format("i"); // remove leading zeros
		$secs = 0 + $this->value->format("s"); // remove leading zeros
		$micros = $this->value->format("u"); // microseconds
		$millis = ((int)($micros/1000)); // convert to milliseconds

		$output = "[$hours,$mins,$secs,$millis]";
		return $output;
	}
}