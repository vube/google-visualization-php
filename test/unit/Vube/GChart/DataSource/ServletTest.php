<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\test;

use Vube\GChart\DataSource\Base\ReasonType;
use Vube\GChart\DataSource\Base\StatusType;
use Vube\GChart\DataSource\DataTable\DataTable;
use Vube\GChart\DataSource\Exception;
use Vube\GChart\DataSource\OutputType;
use Vube\GChart\DataSource\Request;
use Vube\GChart\DataSource\RequestParameters;
use Vube\GChart\DataSource\Response;
use Vube\GChart\DataSource\ResponseWriter;
use Vube\GChart\DataSource\Servlet;


class MockServlet extends Servlet {
	public $isDataTablePopulated = false;
	public $serverGetArgs = array();
	public function getDataTable(Request $request) {
		$this->isDataTablePopulated = true;
		$data = new DataTable();
		return $data;
	}
	public function constructRequest() {
		$_GET = $this->serverGetArgs;
		return parent::constructRequest();
	}
	public function setRestrictedAccessMode($enabled) {
		$this->isRestrictedAccessModeEnabled = $enabled;
	}
	public function setSameOriginHeader($enabled) {
		$header = $this->getSameOriginHeaderApacheName();
		if($enabled)
			$_SERVER[$header] = true;
		else if(isset($_SERVER[$header]))
			unset($_SERVER[$header]);
	}
};

class MockInternalErrorServlet extends MockServlet {
	protected function executeTrue(Response &$response) {
		throw new Exception("Internal error test");
	}
}

/**
 * ServletTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ServletTest extends \PHPUnit_Framework_TestCase {

	public function testConstructResponse()
	{
		$servlet = new MockServlet();
		$response = $servlet->constructResponse();

		$this->assertTrue($response instanceof Response,
			"Expected a Response object to be constructed");
	}

	public function testRestrictedAccessModeWithoutSameOriginHeader()
	{
		$servlet = new MockServlet();
		$servlet->serverGetArgs = array(
			Request::DATASOURCE_REQUEST_PARAMETER => implode(";", array(
				RequestParameters::OUTPUT_TYPE_PARAMETER .":". OutputType::JSON,
			)),
		);
		$servlet->setRestrictedAccessMode(true);
		$servlet->setSameOriginHeader(false);
		$response = $servlet->generateResponse();
		$responseStatus = $response->getResponseStatus();

		$this->assertNotNull($responseStatus, "Expect getResponseStatus() did not return null");
		$this->assertSame(StatusType::ERROR, $responseStatus->getStatusType()->getCode(),
			"Expected to receive an ERROR StatusType");
		$this->assertSame(ReasonType::ACCESS_DENIED, $responseStatus->getReasonType()->getCode(),
			"Expected to receive an ACCESS_DENIED ReasonType");
		$this->assertSame(false, $servlet->isDataTablePopulated,
			"Expect DataTable IS NOT populated during ACCESS_DENIED request");
	}

	public function testRestrictedAccessModeWithSameOriginHeader()
	{
		$servlet = new MockServlet();
		$servlet->serverGetArgs = array(
			Request::DATASOURCE_REQUEST_PARAMETER => implode(";", array(
				RequestParameters::OUTPUT_TYPE_PARAMETER .":". OutputType::JSON,
			)),
		);
		$servlet->setRestrictedAccessMode(true);
		$servlet->setSameOriginHeader(true);
		$response = $servlet->generateResponse();
		$responseStatus = $response->getResponseStatus();

		$this->assertNull($responseStatus, "Expect getResponseStatus() returned a null status");
		$this->assertSame(true, $servlet->isDataTablePopulated,
			"Expect DataTable is populated when access is granted");
	}

	public function testNonRestrictedAccessModeWithoutSameOriginHeader()
	{
		$servlet = new MockServlet();
		$servlet->serverGetArgs = array(
			Request::DATASOURCE_REQUEST_PARAMETER => implode(";", array(
				RequestParameters::OUTPUT_TYPE_PARAMETER .":". OutputType::JSON,
			)),
		);
		$servlet->setRestrictedAccessMode(false);
		$servlet->setSameOriginHeader(false);
		$response = $servlet->generateResponse();
		$responseStatus = $response->getResponseStatus();

		$this->assertNull($responseStatus, "Expect getResponseStatus() returned a null status");
		$this->assertSame(true, $servlet->isDataTablePopulated,
			"Expect DataTable is populated when access is granted");
	}

	public function testGetDataTableException()
	{
		$servlet = new MockInternalErrorServlet();
		$response = $servlet->generateResponse();
		$responseStatus = $response->getResponseStatus();

		$this->assertNotNull($responseStatus, "Expect getResponseStatus() did not return null");
		$this->assertSame(StatusType::ERROR, $responseStatus->getStatusType()->getCode(),
			"Expected to receive an ERROR StatusType");
		$this->assertSame(ReasonType::INTERNAL_ERROR, $responseStatus->getReasonType()->getCode(),
			"Expected to receive an INTERNAL_ERROR ReasonType");
	}

	public function testGenerateResponseWriter()
	{
		$servlet = new MockInternalErrorServlet();
		$servlet->setRestrictedAccessMode(false);
		$writer = $servlet->generateResponseWriter();

		$this->assertTrue($writer instanceof ResponseWriter,
			"Expect a ResponseWriter object to be returned");
	}
}