<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value\test;

use Vube\GoogleVisualization\DataSource\DataTable\Value\BooleanValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\DateTimeValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\DateValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\NumberValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\TextValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\TimeOfDayValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueFactory;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;


/**
 * ValueFactoryTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ValueFactoryTest  extends \PHPUnit_Framework_TestCase {

	public function testNullString()
	{
		$value = ValueFactory::constructNull(ValueType::STRING);
		$this->assertTrue($value instanceof TextValue);
	}

	public function testNullNumber()
	{
		$value = ValueFactory::constructNull(ValueType::NUMBER);
		$this->assertTrue($value instanceof NumberValue);
	}

	public function testNullBoolean()
	{
		$value = ValueFactory::constructNull(ValueType::BOOLEAN);
		$this->assertTrue($value instanceof BooleanValue);
	}

	public function testNullDate()
	{
		$value = ValueFactory::constructNull(ValueType::DATE);
		$this->assertTrue($value instanceof DateValue);
	}

	public function testNullDateTime()
	{
		$value = ValueFactory::constructNull(ValueType::DATETIME);
		$this->assertTrue($value instanceof DateTimeValue);
	}

	public function testNullTimeOfDay()
	{
		$value = ValueFactory::constructNull(ValueType::TIMEOFDAY);
		$this->assertTrue($value instanceof TimeOfDayValue);
	}
}