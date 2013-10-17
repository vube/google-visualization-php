<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable;


/**
 * TableCell class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class TableCell
{
	/**
	 * @var mixed
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
	public function __construct($value, $formattedValue=null, array $customProperties=array())
	{
		$this->value = $value;
		$this->formattedValue = $formattedValue;
		$this->customProperties = $customProperties;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
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