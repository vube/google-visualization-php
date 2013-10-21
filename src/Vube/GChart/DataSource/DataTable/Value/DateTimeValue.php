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

	public function __toString()
	{
		$output = $this->value->format("Y-m-d H:i:s");
		$millis = $this->value->getMilliseconds();
		if($millis > 0)
			$output .= ".".sprintf("%03d", $millis);
		return $output;
	}
}