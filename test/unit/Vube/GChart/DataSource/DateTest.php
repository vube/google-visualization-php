<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\test;
use Vube\GChart\DataSource\Date;


/**
 * DateTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class DateTest extends \PHPUnit_Framework_TestCase {

	public function testIntConstructor()
	{
		$time = 1382127283;
		$date = new Date($time);
		$this->assertSame($time, $date->getTimestamp(), "After parsing date, time must match");
	}

	public function testMysqlDateTimeConstructor()
	{
		$time = 1382127283;
		$mysqlDateTimeUTC = '2013-10-18 20:14:43';
		$date = new Date($mysqlDateTimeUTC);
		$this->assertSame($time, $date->getTimestamp(), "After parsing date, time must match");
	}

	public function testStringConstructorWithTimeZoneSpecifiedInFormat()
	{
		$time = 1382127283;
		$mysqlDateTimeCentral = '2013-10-18 15:14:43 -0500';
		$date = new Date($mysqlDateTimeCentral);
		$this->assertSame($time, $date->getTimestamp(), "After parsing date, time must match");
	}

	public function testStringConstructorWithTimeZoneArgument()
	{
		$time = 1382127283;
		$mysqlDateTimeLocal = '2013-10-18 15:14:43';
		$date = new Date($mysqlDateTimeLocal, new \DateTimeZone('CDT'));
		$this->assertSame($time, $date->getTimestamp(), "After parsing date, time must match");
	}
}