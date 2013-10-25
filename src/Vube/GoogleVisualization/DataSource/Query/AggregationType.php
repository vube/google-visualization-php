<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query;

use Vube\GoogleVisualization\DataSource\Query\Exception\InvalidAggregationTypeException;


/**
 * AggregationType class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class AggregationType {

	const SUM = 'sum';
	const AVG = 'avg';
	const MIN = 'min';
	const MAX = 'max';
	const COUNT = 'count';

	/**
	 * @var string
	 */
	private $code;

	/**
	 * @param string $code
	 */
	public function __construct($code)
	{
		if($code instanceof self)
			$code = $code->getCode();
		else
			self::validateCode($code);

		$this->code = $code;
	}

	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 * @throws InvalidAggregationTypeException
	 */
	public static function validateCode($code)
	{
		switch($code)
		{
			case self::SUM: case self::AVG:
			case self::MIN: case self::MAX:
			case self::COUNT:
				break;
			default:
				throw new InvalidAggregationTypeException($code);
		}
	}
} 