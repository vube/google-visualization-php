<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\DataTable\Value\test;

use Vube\GChart\DataSource\DataTable\Value\BooleanValue;


/**
 * BooleanValueTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class BooleanValueTest extends \PHPUnit_Framework_TestCase {

	public function testToStringTrue()
	{
		$expected = 'true';
		$bool = new BooleanValue(true);
		$actual = $bool->__toString();
		$this->assertSame($expected, $actual, "Expect boolean TRUE output to match");
	}

	public function testToStringFalse()
	{
		$expected = 'false';
		$bool = new BooleanValue(false);
		$actual = $bool->__toString();
		$this->assertSame($expected, $actual, "Expect boolean FALSE output to match");
	}

	public function testToStringNull()
	{
		$expected = 'null';
		$bool = new BooleanValue(null);
		$actual = $bool->__toString();
		$this->assertSame($expected, $actual, "Expect boolean NULL output to match");
	}

	public function testToStringImpliedTrue()
	{
		$expected = 'true';
		$bool = new BooleanValue(1);
		$actual = $bool->__toString();
		$this->assertSame($expected, $actual, "Expect boolean implied TRUE output to match");
	}

	public function testToStringImpliedFalse()
	{
		$expected = 'false';
		$bool = new BooleanValue(0);
		$actual = $bool->__toString();
		$this->assertSame($expected, $actual, "Expect boolean implied FALSE output to match");
	}
}