<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\Value;

use Vube\GChart\DataSource\Exception\NoSuchValueTypeException;


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
}