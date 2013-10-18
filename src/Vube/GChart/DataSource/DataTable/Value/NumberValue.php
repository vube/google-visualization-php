<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\Value;


/**
 * NumberValue class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class NumberValue extends Value {

	public function __construct($value)
	{
		parent::__construct($value, ValueType::NUMBER);
	}
}