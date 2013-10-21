<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value;


/**
 * Value class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
abstract class Value {

	/**
	 * @var mixed
	 */
	protected $value;
	/**
	 * @var ValueType
	 */
	protected $valueType;

	public function __construct($value, $valueTypeCode)
	{
		$this->value = $value;
		$this->valueType = new ValueType($valueTypeCode);
	}

	/**
	 * @return bool
	 */
	public function isNull()
	{
		return $this->value === null;
	}

	/**
	 * @return ValueType
	 */
	public function getType()
	{
		return $this->valueType;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		if($this->value === null)
			return "null";

		return $this->value;
	}
}