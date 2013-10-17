<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\test;
use Vube\GChart\DataSource\Request;
use Vube\GChart\DataSource\RequestParameters;


/**
 * RequestTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
	public function testEmptyRequestConstructs()
	{
		$r = new Request();
		$params = $r->getParameters();
		$query = $r->getQuery();

		$this->assertTrue($params instanceof RequestParameters, "Request->getParameters must return a RequestParameters object");
		$this->assertSame(null, $query, "Query is expected to be null for an empty request");
	}

	public function testRequestWithUnknownParametersConstructs()
	{
		$vars = array('foo' => 'bar', 'other' => 'thing', 'unknown' => 'ok');
		$r = new Request($vars);
		$params = $r->getParameters();

		$this->assertTrue($params instanceof RequestParameters, "Request->getParameters must return a RequestParameters object");
	}

	public function testRequestWithQuerySpecified()
	{
		$vars = array('tq' => 'select Col1');
		$r = new Request($vars);
		$query = $r->getQuery();

		$this->assertSame($vars['tq'], $query, 'Expect query to equal the tq value');
	}

	public function testRequestWithEmptyQuery()
	{
		$vars = array('tq' => '');
		$r = new Request($vars);
		$query = $r->getQuery();

		$this->assertSame($vars['tq'], $query, 'Expect query to equal the tq value');
	}

	public function testRequestWithPopulatedParameters()
	{
		$vars = array('tqx' => 'reqId:1');
		$r = new Request($vars);
		$params = $r->getParameters();

		$this->assertTrue($params->isParameterSet('reqId'), 'reqId parameter must be set');
		$this->assertSame('1', $params->getParameter('reqId'), 'reqId value must equal input');
	}

	public function testRequestWithBothQueryAndParameters()
	{
		$vars = array('tqx' => 'reqId:2', 'tq' => 'select Col1');
		$r = new Request($vars);
		$params = $r->getParameters();
		$query = $r->getQuery();

		$this->assertSame($vars['tq'], $query, 'Expect query to equal the tq value');
		$this->assertTrue($params->isParameterSet('reqId'), 'reqId parameter must be set');
		$this->assertSame('2', $params->getParameter('reqId'), 'reqId value must equal input');

		$this->assertSame($vars['tq'], $query, 'Expect query to equal the tq value');
	}
}
