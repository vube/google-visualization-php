<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value\test;

use Vube\GoogleVisualization\DataSource\DataTable\Value\DateTimeValue;
use Vube\GoogleVisualization\DataSource\Date;


/**
 * DateTimeValueTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class DateTimeValueTest extends \PHPUnit_Framework_TestCase {

	public function testStringConstructorToString()
	{
		$expected = "2013-01-01T00:01:02+0000";
		$datetime = new DateTimeValue($expected);
		$actual = $datetime->__toString();
		$this->assertSame($expected, $actual);
	}

	public function testDateConstructorToString()
	{
		$expected = "2013-01-01T00:01:02.123456+0130";
		$datetime = new DateTimeValue(new Date($expected));
		$actual = $datetime->__toString();
		$this->assertSame($expected, $actual);
	}

	public function testNullConstructorToString()
	{
		$expected = "null";
		$datetime = new DateTimeValue(null);
		$actual = $datetime->__toString();
		$this->assertSame($expected, $actual, "toString should return null");
	}
}