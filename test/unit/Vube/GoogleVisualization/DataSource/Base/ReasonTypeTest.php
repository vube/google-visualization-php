<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\test\Base;

use Vube\GoogleVisualization\DataSource\Base\ReasonType;


/**
 * ReasonTypeTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ReasonTypeTest extends \PHPUnit_Framework_TestCase {

	public function testValidateInvalidType()
	{
		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception\\InvalidReasonTypeException');
		ReasonType::validateType('no-such-ReasonType');
	}
}