<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable;

use Vube\GoogleVisualization\DataSource\DataTable\Value\BooleanValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\DateValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\NumberValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\TextValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\Value;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;
use Vube\GoogleVisualization\DataSource\Date;
use Vube\GoogleVisualization\DataSource\Exception\NoSuchValueTypeException;
use Vube\GoogleVisualization\DataSource\Exception\TypeMismatchException;


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
	 * @param Value|mixed $value Raw value of the cell
	 * @param null|string $formattedValue
	 * @param array $customProperties
	 */
	public function __construct($value, $formattedValue=null, array $customProperties=array())
	{
		$this->setValue($value);
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
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		if(! $value instanceof Value)
			$value = $this->constructDefaultValueType($value);

		$this->value = $value;
	}

	/**
	 * Given a raw value, construct the default Value object for it
	 *
	 * @param mixed $value
	 * @return Value
	 * @throws TypeMismatchException
	 */
	public function constructDefaultValueType($value)
	{
		if(is_int($value) || is_float($value))
			return new NumberValue($value);

		if(is_bool($value))
			return new BooleanValue($value);

		if($value instanceof Date)
			return new DateValue($value);

		if(is_string($value) || $value === null)
			return new TextValue($value);

		throw new TypeMismatchException(array('int','float','bool','Date','string'), $value);
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