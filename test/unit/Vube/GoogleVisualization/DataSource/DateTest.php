<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\test;
use Vube\GoogleVisualization\DataSource\Date;


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

	public function testToString()
	{
		$mysqlDateTimeLocal = '2013-10-18 15:14:43';
		$date = new Date($mysqlDateTimeLocal, new \DateTimeZone('CDT'));

		$expected = '2013-10-18T15:14:43.000000-0500';
		$actual = $date->__toString();

		$this->assertSame($expected, $actual, "__toString must return this format");
	}

	public function testToStringAndBackAgain()
	{
		$mysqlDateTimeLocal = '2013-10-18 15:14:43';
		$date1 = new Date($mysqlDateTimeLocal, new \DateTimeZone('CDT'));

		$string1 = $date1->__toString();
		$date2 = new Date($string1);

		$this->assertEquals($date1->getTimestamp(), $date2->getTimestamp(),
			"Parsing __toString result should yield same date as before");
	}

	/**
	 * Make sure that we handle microseconds correctly
	 *
	 * When we Date->__toString() it should preserve the microseconds,
	 * and we should be able to parse it again to yield the exact same
	 * value in another object.
	 */
	public function testMicrosecondsToStringAndBack()
	{
		$date0 = new Date();
		// Format the current date without any microseconds, but use
		// a "@" char where the micros should be
		$stringDate = $date0->format('Y-m-d\TH:i:s@O');
		// Explicitly add microseconds to the date format string, so
		// we _guarantee_ that the date contains microseconds
		$stringDate = str_replace('@', '.123456', $stringDate);

		$date1 = new Date($stringDate);
		$string1 = $date1->__toString();
		$date2 = new Date($string1);

		$this->assertEquals($date1->getTimestamp(), $date2->getTimestamp(),
			"Parsing __toString result should yield same date as before");
	}

}