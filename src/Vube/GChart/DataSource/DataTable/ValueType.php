<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable;
use Vube\GChart\DataSource\Date;
use Vube\GChart\DataSource\Exception\NoSuchValueTypeException;
use Vube\GChart\DataSource\Exception\TypeMismatchException;


/**
 * ValueType class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ValueType
{
	const STRING = 'string';
	const NUMBER = 'number';
	const BOOLEAN = 'boolean';
	const DATE = 'date';

	private $type;

	public function __construct($type)
	{
		if($type instanceof ValueType)
			$this->type = $type->getTypeId();
		else if(is_string($type))
		{
			if(self::isValidTypeString($type))
				$this->type = $type;
			else
				throw new NoSuchValueTypeException($type);
		}
		else
			throw new TypeMismatchException(array(__CLASS__,'string'), $type);
	}

	public function getTypeId()
	{
		return $this->type;
	}

	public function isSameDataType($data)
	{
		$dataType = gettype($data);

		switch($dataType)
		{
			case 'string':
				return $this->type === self::STRING;

			case 'boolean':
				return $this->type === self::BOOLEAN;

			case 'integer':
			case 'double':
				return $this->type === self::NUMBER;

			case 'object':
				if($data instanceof Date)
					return $this->type === self::DATE;
				// any other object is not a valid type
				break;

			default:
				break;
		}
		return false;
	}

	public static function isValidTypeString($type)
	{
		if($type === self::STRING
			|| $type === self::NUMBER
			|| $type === self::BOOLEAN
			|| $type === self::DATE )
			return true;

		return false;
	}
}