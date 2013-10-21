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

	/**
	 * @return int
	 */
	public function getHours()
	{
		$hours = $this->value->format("G"); // 0..23
		return (int)$hours;
	}

	/**
	 * @return int
	 */
	public function getMinutes()
	{
		$mins = 0 + $this->value->format("i"); // remove leading zeros
		return $mins;
	}

	/**
	 * @return int
	 */
	public function getSeconds()
	{
		$secs = 0 + $this->value->format("s"); // remove leading zeros
		return $secs;
	}

	/**
	 * @return int
	 */
	public function getMilliseconds()
	{
		$micros = $this->value->format("u"); // microseconds
		$millis = ((int)($micros/1000)); // convert to milliseconds
		return $millis;
	}

	public function toString()
	{
		$output = $this->format("H:i:s");
		$millis = $this->getMilliseconds();
		$output .= ".".$millis;
		return $output;
	}
}