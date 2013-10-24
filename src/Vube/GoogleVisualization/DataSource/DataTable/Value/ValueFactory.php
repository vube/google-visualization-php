<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value;
use Vube\GoogleVisualization\DataSource\Exception\NoSuchValueTypeException;


/**
 * ValueFactory class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ValueFactory {

	/**
	 * @param ValueType|string $type
	 * @return Value
	 * @throws NoSuchValueTypeException
	 */
	public static function constructNull($type)
	{
		$type = new ValueType($type);
		switch($type->getCode())
		{
			case ValueType::STRING:
				return new TextValue(null);
			case ValueType::NUMBER:
				return new NumberValue(null);
			case ValueType::BOOLEAN:
				return new BooleanValue(null);
			case ValueType::DATE:
				return new DateValue(null);
			case ValueType::DATETIME:
				return new DateTimeValue(null);
			case ValueType::TIMEOFDAY:
				return new TimeOfDayValue(null);
			default:
				throw new NoSuchValueTypeException($type->getCode());
		}
	}
}