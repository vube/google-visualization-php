<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\test\Base;

use Vube\GChart\DataSource\Base\ReasonType;


/**
 * ReasonTypeTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ReasonTypeTest extends \PHPUnit_Framework_TestCase {

	public function testValidateInvalidType()
	{
		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\InvalidReasonTypeException');
		ReasonType::validateType('no-such-ReasonType');
	}
}