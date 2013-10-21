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

	public function testToStringWithoutMilliseconds()
	{
		$expected = "2013-01-01 00:01:02";
		$datetime = new DateTimeValue(new Date($expected));
		$actual = $datetime->__toString();
		$this->assertSame($expected, $actual, "Expect YYYY-MM-DD HH:mm:ss format");
	}

	public function testToStringWithMilliseconds()
	{
		$expected = "2013-01-01 00:01:02.001";
		$datetime = new DateTimeValue(new Date($expected));
		$actual = $datetime->__toString();
		$this->assertSame($expected, $actual, "Expect YYYY-MM-DD HH:mm:ss.mmm format");
	}
}