<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\Value;

use Vube\GChart\DataSource\Date;


/**
 * DateTimeValue class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class DateTimeValue extends Value {

	public function __construct(Date $value)
	{
		parent::__construct($value, ValueType::DATETIME);
	}

	/**
	 * @return int
	 */
	public function getYear()
	{
		$year = $this->value->format("Y");
		return (int)$year;
	}

	/**
	 * Zero-based Month index
	 * @return int 0..11
	 */
	public function getMonth()
	{
		$month = -1 + $this->value->format("n"); // 0..11
		return $month;
	}

	/**
	 * @return int 1..31
	 */
	public function getMonthDay()
	{
		$day = $this->value->format("j"); // 1..31
		return (int)$day;
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
		$output = $this->format("Y-m-d H:i:s");
		$millis = $this->getMilliseconds();
		$output .= ".".$millis;
		return $output;
	}
}