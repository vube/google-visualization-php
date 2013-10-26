<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value;

use Vube\GoogleVisualization\DataSource\Exception\NoSuchValueTypeException;


/**
 * ValueType class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ValueType
{
	const STRING = 0;
	const NUMBER = 1;
	const BOOLEAN = 2;
	const DATE = 3;
	const DATETIME = 4;
	const TIMEOFDAY = 5;

	private static $typeNames = array(
		self::STRING => 'string',
		self::NUMBER => 'number',
		self::BOOLEAN => 'boolean',
		self::DATE => 'date',
		self::DATETIME => 'datetime',
		self::TIMEOFDAY => 'timeofday',
	);

	private $code;

	/**
	 * @param ValueType|int $code
	 * @throws NoSuchValueTypeException
	 */
	public function __construct($code)
	{
		if($code instanceof ValueType)
			$this->code = $code->getCode();
		else
		{
			if(! isset(self::$typeNames[$code]))
				throw new NoSuchValueTypeException($code);

			$this->code = $code;
		}
	}

	public function getCode()
	{
		return $this->code;
	}

	public function getTypeName()
	{
		return self::$typeNames[$this->code];
	}

	/**
	 * @return bool TRUE if this value is a type of date
	 */
	public function isDateValue()
	{
		switch($this->code)
		{
			case self::DATE: case self::DATETIME: case self::TIMEOFDAY:
				return true;
			default:
				return false;
		}
	}
}