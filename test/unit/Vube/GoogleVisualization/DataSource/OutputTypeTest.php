<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\test;

use Vube\GoogleVisualization\DataSource\OutputType;


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
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception\\InvalidOutputTypeException');
		$ot = new OutputType('no-such-output-type');
	}
}