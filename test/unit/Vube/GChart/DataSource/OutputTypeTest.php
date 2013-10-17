<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\test;

use Vube\GChart\DataSource\OutputType;


/**
 * OutputTypeTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class OutputTypeTest extends \PHPUnit_Framework_TestCase {

	public function testConstructor()
	{
		$ot = new OutputType(OutputType::JSON);
		$this->assertSame(OutputType::JSON, $ot->getCode(), "getCode() must match constructor value");
	}

	public function testConstructorWithInvalidValue()
	{
		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\InvalidOutputTypeException');
		$ot = new OutputType('no-such-output-type');
	}
}