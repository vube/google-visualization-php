<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\Value;


/**
 * BooleanValue class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class BooleanValue extends Value {

	public function __construct($value)
	{
		parent::__construct($value, ValueType::BOOLEAN);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		if($this->value === true)
			return 'true';
		else if($this->value === false)
			return 'false';
		else if($this->value === null)
			return 'null';

		// implicit cast to boolean
		return $this->value ? 'true' : 'false';
	}
}