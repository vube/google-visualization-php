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

	public function toString()
	{
		$output = $this->format("Y-m-d");
		return $output;
	}
}