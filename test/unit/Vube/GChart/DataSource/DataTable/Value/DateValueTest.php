<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\Value\test;
use Vube\GChart\DataSource\DataTable\Value\DateValue;
use Vube\GChart\DataSource\Date;


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
}