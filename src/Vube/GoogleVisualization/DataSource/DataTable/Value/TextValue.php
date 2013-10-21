<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value;


/**
 * TextValue class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class TextValue extends Value {

	public function __construct($value)
	{
		parent::__construct($value, ValueType::STRING);
	}
}