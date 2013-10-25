<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\DataTable\Value\test;

use Vube\GoogleVisualization\DataSource\DataTable\Value\DateValue;
use Vube\GoogleVisualization\DataSource\Date;


/**
 * DateValueTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class DateValueTest extends \PHPUnit_Framework_TestCase {

	public function testToString()
	{
		$expected = "2013-01-01";
		$date = new DateValue(new Date($expected));
		$actual = $date->__toString();
		$this->assertSame($expected, $actual, "Expect to get a YYYY-MM-DD result from toString");
	}

	public function testToStringWithStringConstructor()
	{
		$expected = "2013-02-03";
		$date = new DateValue($expected);
		$actual = $date->__toString();
		$this->assertSame($expected, $actual, "Expect to get a YYYY-MM-DD result from toString");
	}

	public function testToStringWithNullConstructor()
	{
		$expected = "null";
		$date = new DateValue(null);
		$actual = $date->__toString();
		$this->assertSame($expected, $actual, "toString should return null");
	}
}