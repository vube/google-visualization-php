<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value\test;

use Vube\GoogleVisualization\DataSource\DataTable\Value\TimeOfDayValue;
use Vube\GoogleVisualization\DataSource\Date;


/**
 * TimeOfDayValueTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class TimeOfDayValueTest extends \PHPUnit_Framework_TestCase {

	public function testToStringWithoutMicroseconds()
	{
		$expected = '00:01:02';
		$tod = new TimeOfDayValue(new Date($expected));
		$actual = $tod->__toString();
		$this->assertSame($expected, $actual, "toString should return a clock-like format");
	}

	public function testToStringWithMicroseconds()
	{
		$expected = '00:01:02.123456';
		$tod = new TimeOfDayValue(new Date($expected));
		$actual = $tod->__toString();
		$this->assertSame($expected, $actual, "toString should return a clock-like format");
	}

	public function testStringConstructor()
	{
		$expected = '00:01:02.123456';
		$tod = new TimeOfDayValue($expected);
		$actual = $tod->__toString();
		$this->assertSame($expected, $actual, "toString should return a clock-like format");
	}

	public function testNullConstructor()
	{
		$expected = 'null';
		$tod = new TimeOfDayValue(null);
		$actual = $tod->__toString();
		$this->assertSame($expected, $actual, "toString should return null");
	}
}