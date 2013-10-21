<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\test;

use Vube\GChart\DataSource\OutputType;
use Vube\GChart\DataSource\Request;
use Vube\GChart\DataSource\RequestParameters;
use Vube\GChart\DataSource\ResponseWriter;


class MockResponseWriter extends ResponseWriter {
	public $headersSent = array();
	public function sendHeader($name, $value) {
		$this->headersSent[$name] = $value;
		parent::sendHeader($name, $value);
	}
}


class NewOutputTypeTest extends OutputType {
	const NEW_TYPE_NAME = 'new-type';
	public static function enableNewUnknownOutputType() {
		array_unshift(OutputType::$validCodes, self::NEW_TYPE_NAME);
	}
	public static function disableNewUnknownOutputType() {
		if(static::$validCodes[0] === self::NEW_TYPE_NAME)
			array_shift(OutputType::$validCodes);
	}
}


/**
 * ResponseWriterTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ResponseWriterTest extends \PHPUnit_Framework_TestCase {

	private $expectedOutputString = 'mock-output-string';

	public function createMockResponseFromRequest(Request $request)
	{
		$response = $this->getMock('\\Vube\\GChart\\DataSource\\Response',
			array('__toString'), array($request));
		$response->expects($this->any())
			->method('__toString')
			->will($this->returnValue($this->expectedOutputString));
		return $response;
	}

	public function constructMockResponse($outputTypeCode)
	{
		$requestParameters = implode(";", array(
			RequestParameters::OUTPUT_TYPE_PARAMETER .":". $outputTypeCode,
		));

		$request = new Request();
		$request->setParams($requestParameters);

		return $this->createMockResponseFromRequest($request);
	}

	public function testJsonResponseWriter()
	{
		$response = $this->constructMockResponse(OutputType::JSON);
		$responseWriter = new MockResponseWriter();

		$this->expectOutputString($this->expectedOutputString);
		$responseWriter->send($response);

		$this->assertArrayHasKey('Content-Type', $responseWriter->headersSent,
			"Expect to have sent Content-Type header");
		$this->assertSame("application/json; charset=UTF-8", $responseWriter->headersSent['Content-Type'],
			"Expect to have sent JSON Content-Type");
	}

	public function testJsonpResponseWriter()
	{
		$response = $this->constructMockResponse(OutputType::JSONP);
		$responseWriter = new MockResponseWriter();

		$this->expectOutputString($this->expectedOutputString);
		$responseWriter->send($response);

		$this->assertArrayHasKey('Content-Type', $responseWriter->headersSent,
			"Expect to have sent Content-Type header");
		$this->assertSame("text/javascript; charset=UTF-8", $responseWriter->headersSent['Content-Type'],
			"Expect to have sent JSONP Content-Type");
	}

	public function testCsvResponseWriter()
	{
		$response = $this->constructMockResponse(OutputType::CSV);
		$responseWriter = new MockResponseWriter();

		$this->expectOutputString($this->expectedOutputString);
		$responseWriter->send($response);

		$this->assertArrayHasKey('Content-Type', $responseWriter->headersSent,
			"Expect to have sent Content-Type header");
		$this->assertSame("text/csv; charset=UTF-8", $responseWriter->headersSent['Content-Type'],
			"Expect to have sent CSV Content-Type");

		$this->assertArrayHasKey('Content-Disposition', $responseWriter->headersSent,
			"Expect to have sent Content-Disposition header");
		$this->assertSame("attachment; filename=results.csv", $responseWriter->headersSent['Content-Disposition'],
			"Expect to have sent results.csv filename");
	}

	public function testCsvResponseWriterWithCustomOutputFilename()
	{
		$requestParameters = implode(";", array(
			RequestParameters::OUTPUT_TYPE_PARAMETER .":". OutputType::CSV,
			RequestParameters::OUTPUT_FILE_NAME_PARAMETER .":custom_secure.123",
		));

		$request = new Request();
		$request->setParams($requestParameters);

		$response = $this->createMockResponseFromRequest($request);
		$responseWriter = new MockResponseWriter();

		$this->expectOutputString($this->expectedOutputString);
		$responseWriter->send($response);

		$this->assertArrayHasKey('Content-Type', $responseWriter->headersSent,
			"Expect to have sent Content-Type header");
		$this->assertSame("text/csv; charset=UTF-8", $responseWriter->headersSent['Content-Type'],
			"Expect to have sent CSV Content-Type");

		$this->assertArrayHasKey('Content-Disposition', $responseWriter->headersSent,
			"Expect to have sent Content-Disposition header");
		$this->assertSame("attachment; filename=custom_secure.123.csv", $responseWriter->headersSent['Content-Disposition'],
			"Expect to have sent results.csv filename");
	}

	public function testHtmlResponseWriter()
	{
		$response = $this->constructMockResponse(OutputType::HTML);
		$responseWriter = new MockResponseWriter();

		$this->expectOutputString($this->expectedOutputString);
		$responseWriter->send($response);

		$this->assertArrayHasKey('Content-Type', $responseWriter->headersSent,
			"Expect to have sent Content-Type header");
		$this->assertSame("text/html; charset=UTF-8", $responseWriter->headersSent['Content-Type'],
			"Expect to have sent HTML Content-Type");
	}

	public function testTsvExcelResponseWriter()
	{
		$response = $this->constructMockResponse(OutputType::TSV_EXCEL);
		$responseWriter = new MockResponseWriter();

		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception\\NotImplementedException');
		$responseWriter->send($response);
	}

	public function testUnknownOutputTypeResponseWriter()
	{
		// First enable a new output type in the OutputType class so it doesn't
		// throw any exceptions - as if we're adding a new type and we forgot
		// to add it to the ResponseWriter
		NewOutputTypeTest::enableNewUnknownOutputType();
		// Now create the response
		$response = $this->constructMockResponse(NewOutputTypeTest::NEW_TYPE_NAME);
		// Now disable the new type so future tests will run without it enabled,
		// as they would in production.
		NewOutputTypeTest::disableNewUnknownOutputType();

		$responseWriter = new MockResponseWriter();

		$this->setExpectedException('\\Vube\\GChart\\DataSource\\Exception',
			"Unknown Output Type: ".NewOutputTypeTest::NEW_TYPE_NAME);
		$responseWriter->send($response);
	}
}