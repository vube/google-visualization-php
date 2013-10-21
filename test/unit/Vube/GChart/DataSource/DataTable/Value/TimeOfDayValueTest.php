<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\Value\test;

use Vube\GChart\DataSource\DataTable\Value\TimeOfDayValue;
use Vube\GChart\DataSource\Date;


/**
 * TimeOfDayValueTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class TimeOfDayValueTest extends \PHPUnit_Framework_TestCase {

	public function testToStringWithoutMilliseconds()
	{
		$expected = '00:01:02';
		$tod = new TimeOfDayValue(new Date($expected));
		$actual = $tod->__toString();
		$this->assertSame($expected, $actual, "toString should return a clock-like format");
	}

	public function testToStringWithMilliseconds()
	{
		$expected = '00:01:02.125';
		$tod = new TimeOfDayValue(new Date($expected));
		$actual = $tod->__toString();
		$this->assertSame($expected, $actual, "toString should return a clock-like format");
	}
}