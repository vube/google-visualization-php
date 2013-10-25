<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\test;

use Vube\GoogleVisualization\DataSource\Query\Engine\PivotKey;


/**
 * PivotKeyTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class PivotKeyTest extends \PHPUnit_Framework_TestCase {

	public function testStringEqualsHash()
	{
		$values = array(1,2,3);
		$hash = PivotKey::hash($values);
		$key = new PivotKey($values);
		$stringValue = $key->__toString();
		$this->assertSame($hash, $stringValue, "Expect __toString to return the hash");
	}
} 