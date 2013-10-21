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

	public function __construct(Date $value)
	{
		parent::__construct($value, ValueType::TIMEOFDAY);
	}

	public function __toString()
	{
		$output = $this->value->format("H:i:s");
		$millis = $this->value->getMilliseconds();
		if($millis > 0)
			$output .= ".".sprintf("%03d", $millis);
		return $output;
	}
}