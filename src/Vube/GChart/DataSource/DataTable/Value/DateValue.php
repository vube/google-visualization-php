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
		$output = $this->value->format("Y-m-d");
		return $output;
	}
}