<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value;


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
		// Handle null specially
		if($this->value === null)
			return 'null';

		// All other values implicitly cast to boolean
		return $this->value ? 'true' : 'false';
	}
}