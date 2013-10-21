<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\test;
use Vube\GoogleVisualization\DataSource\RequestParameters;


/**
 * RequestParametersTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class RequestParametersTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructorWithoutArguments()
	{
		$rp = new RequestParameters();
	}

	public function testConstructorWithValidArgument()
	{
		$rp = new RequestParameters('reqId:1');
	}

	public function testSetWithNonStringThrowsException()
	{
		$rp = new RequestParameters();

		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception\\TypeMismatchException');
		$rp->set(null);
	}

	public function testGetUnknownParameterThrowsException()
	{
		$rp = new RequestParameters();

		$this->setExpectedException('\\Vube\\GoogleVisualization\\DataSource\\Exception\\NoSuchParameterException');
		$rp->getParameter('this-parameter-does-not-exist');
	}

	public function testGetParameterNamesReturnsArray()
	{
		$rp = new RequestParameters();
		$names = $rp->getParameterNames();

		$this->assertTrue(is_array($names), 'getParameterNames() must return array');
	}

	public function testSetGoogleExampleTQXValue()
	{
		$rp = new RequestParameters();
		$rp->set('version:0.6;reqId:1;sig:5277771;out:json;responseHandler:myQueryHandler');

		$this->assertTrue($rp->isParameterSet('version'), 'version is expected to be set');
		$this->assertTrue($rp->isParameterSet('reqId'), 'reqId is expected to be set');
		$this->assertTrue($rp->isParameterSet('sig'), 'sig is expected to be set');
		$this->assertTrue($rp->isParameterSet('out'), 'out is expected to be set');
		$this->assertTrue($rp->isParameterSet('responseHandler'), 'responseHandler is expected to be set');

		$this->assertSame('0.6', $rp->getParameter('version'), 'version value must match');
		$this->assertSame('1', $rp->getParameter('reqId'), 'reqId value must match');
		$this->assertSame('5277771', $rp->getParameter('sig'), 'sig value must match');
		$this->assertSame('json', $rp->getParameter('out'), 'out value must match');
		$this->assertSame('myQueryHandler', $rp->getParameter('responseHandler'), 'responseHandler value must match');
	}

	public function testSetNonDefaultValues()
	{
		$rp = new RequestParameters();
		$rp->set('version:0.123;reqId:99;sig:123;out:csv');

		$this->assertTrue($rp->isParameterSet('version'), 'version is expected to be set');
		$this->assertTrue($rp->isParameterSet('reqId'), 'reqId is expected to be set');
		$this->assertTrue($rp->isParameterSet('sig'), 'sig is expected to be set');
		$this->assertTrue($rp->isParameterSet('out'), 'out is expected to be set');

		$this->assertSame('0.123', $rp->getParameter('version'), 'version value must match');
		$this->assertSame('99', $rp->getParameter('reqId'), 'reqId value must match');
		$this->assertSame('123', $rp->getParameter('sig'), 'sig value must match');
		$this->assertSame('csv', $rp->getParameter('out'), 'out value must match');
	}
}