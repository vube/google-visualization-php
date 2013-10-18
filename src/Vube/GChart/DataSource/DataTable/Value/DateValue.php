<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\Value;

use Vube\GChart\DataSource\Date;


/**
 * DateValue class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class DateValue extends Value {

	public function __construct(Date $value)
	{
		parent::__construct($value, ValueType::DATE);
	}

	public function toString()
	{
		$year = $this->value->format("Y");
		$month = -1 + $this->value->format("n"); // 0..11
		$day = $this->value->format("j"); // 1..31

		$output = "Date($year,$month,$day)";
		return $output;
	}
}