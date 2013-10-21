<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable;

use Vube\GoogleVisualization\DataSource\DataTable\Value\Value;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;


/**
 * TableCell class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class TableCell
{
	/**
	 * @var Value
	 */
	private $value;
	/**
	 * @var null|string
	 */
	private $formattedValue;
	/**
	 * @var array
	 */
	private $customProperties;

	/**
	 * @param mixed $value Raw value
	 * @param null|string $formattedValue
	 * @param array $customProperties
	 */
	public function __construct(Value $value, $formattedValue=null, array $customProperties=array())
	{
		$this->value = $value;
		$this->formattedValue = $formattedValue;
		$this->customProperties = $customProperties;
	}

	/**
	 * @return Value
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return ValueType
	 */
	public function getValueType()
	{
		return $this->value->getType();
	}

	/**
	 * @return null|string
	 */
	public function getFormattedValue()
	{
		return $this->formattedValue;
	}

	/**
	 * @return array
	 */
	public function getCustomProperties()
	{
		return $this->customProperties;
	}

	/**
	 * @param string $name Name of the custom property to retrieve.
	 * @return null|string
	 */
	public function getCustomProperty($name)
	{
		if(! isset($this->customProperties[$name]))
			return null;

		return $this->customProperties[$name];
	}

	/**
	 * @param string $name Name of the custom property to set.
	 * @param string $value Value of the custom property to set.
	 */
	public function setCustomProperty($name, $value)
	{
		$this->customProperties[$name] = $value;
	}
}