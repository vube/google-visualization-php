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

	public function toString()
	{
		$year = $this->value->format("Y");
		$month = -1 + $this->value->format("n"); // 0..11
		$day = $this->value->format("j"); // 1..31
		$hours = $this->value->format("G"); // 0..23
		$mins = 0 + $this->value->format("i"); // remove leading zeros
		$secs = 0 + $this->value->format("s"); // remove leading zeros

		$output = "Date($year,$month,$day,$hours,$mins,$secs)";
		return $output;
	}
}