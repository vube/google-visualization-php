<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\test\Render;

use Vube\GoogleVisualization\DataSource\Base\ReasonType;
use Vube\GoogleVisualization\DataSource\Base\ResponseStatus;
use Vube\GoogleVisualization\DataSource\Base\StatusType;
use Vube\GoogleVisualization\DataSource\Base\Warning;
use Vube\GoogleVisualization\DataSource\DataTable\ColumnDescription;
use Vube\GoogleVisualization\DataSource\DataTable\DataTable;
use Vube\GoogleVisualization\DataSource\DataTable\TableCell;
use Vube\GoogleVisualization\DataSource\DataTable\TableRow;
use Vube\GoogleVisualization\DataSource\DataTable\Value\BooleanValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\DateTimeValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\DateValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\NumberValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\TextValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\TimeOfDayValue;
use Vube\GoogleVisualization\DataSource\DataTable\Value\ValueType;
use Vube\GoogleVisualization\DataSource\Date;
use Vube\GoogleVisualization\DataSource\OutputType;
use Vube\GoogleVisualization\DataSource\Render\JsonRenderer;
use Vube\GoogleVisualization\DataSource\Request;
use Vube\GoogleVisualization\DataSource\RequestParameters;
use Vube\GoogleVisualization\DataSource\Response;


/**
 * JsonRendererTest class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class JsonRendererTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var DataTable
	 */
	private $dataTable;
	/**
	 * @var Response
	 */
	private $response;

	public function setUp()
	{
		$request = new Request();

		$data = new DataTable();
		$data->addColumn(new ColumnDescription('date', ValueType::DATE));
		$data->addColumn(new ColumnDescription('name', ValueType::STRING));
		$data->addColumn(new ColumnDescription('count', ValueType::NUMBER));

		$row = new TableRow();
		$row->addCell(new TableCell(new DateValue(new Date('2013-10-19'))));
		$row->addCell(new TableCell(new TextValue('eleven')));
		$row->addCell(new TableCell(new NumberValue(11)));
		$data->addRow($row);

		$row = new TableRow();
		$row->addCell(new TableCell(new DateValue(new Date('2013-01-01'))));
		$row->addCell(new TableCell(new TextValue('twenty_three')));
		$row->addCell(new TableCell(new NumberValue(23)));
		$data->addRow($row);

		// A row with a NULL value in the middle
		$row = new TableRow();
		$row->addCell(new TableCell(new DateValue(new Date('2013-07-01'))));
		$row->addCell(new TableCell(new TextValue(null)));
		$row->addCell(new TableCell(new NumberValue(35)));
		$data->addRow($row);

		// A row with a NULL value in the last cell
		$row = new TableRow();
		$row->addCell(new TableCell(new DateValue(new Date('1999-12-31'))));
		$row->addCell(new TableCell(new TextValue('null_value')));
		$row->addCell(new TableCell(new NumberValue(null)));

		$data->addRow($row);

		$this->dataTable = $data;

		$this->response = new Response($request);
		$this->response->setDataTable($data);
	}

	public function testGetSignature()
	{
		$renderer = new JsonRenderer();
		$sig = $renderer->getSignature($this->response->getDataTable());

		$this->assertTrue(is_string($sig), "signature must be a string");
	}

	public function testRenderWithEmptyDataReturnsJson()
	{
		$dataTable = new DataTable();
		$this->response->setDataTable($dataTable);
		$renderer = new JsonRenderer();
		$json = $renderer->render($this->response);
		$data = json_decode($json, true);

		$this->assertNotNull($data, "render() result must be valid json");
		$this->assertTrue(is_array($data), "parsed render() result must be array");
		$this->assertArrayHasKey('version', $data, "render() must return a protocol version");
		$this->assertArrayHasKey('status', $data, "render() must set output status");
		$this->assertSame(StatusType::OK, $data['status'], "render() must set status=OK");
		$this->assertArrayHasKey('sig', $data, "Data signature must be set");
		$this->assertArrayHasKey('table', $data, "Data table must be present");
	}

	public function testRenderWithDataReturnsJson()
	{
		$renderer = new JsonRenderer();
		$json = $renderer->render($this->response);
		$data = json_decode($json, true);

		$this->assertNotNull($data, "render() result must be valid json");
		$this->assertTrue(is_array($data), "parsed render() result must be array");
		$this->assertArrayHasKey('version', $data, "render() must return a protocol version");
		$this->assertArrayHasKey('status', $data, "render() must set output status");
		$this->assertSame(StatusType::OK, $data['status'], "render() must set status=OK");
		$this->assertArrayHasKey('sig', $data, "Data signature must be set");
		$this->assertArrayHasKey('table', $data, "Data table must be present");
	}

	public function testRenderReturnsRequestIdWhenPresent()
	{
		$request = new Request(array(
			Request::DATASOURCE_REQUEST_PARAMETER =>
				RequestParameters::REQUEST_ID_PARAMETER . ':123',
		));

		$response = new Response($request);
		$dataTable = new DataTable();
		$this->response->setDataTable($dataTable);

		$renderer = new JsonRenderer();
		$json = $renderer->render($response);
		$data = json_decode($json, true);

		$this->assertNotNull($data, "render() result must be valid json");
		$this->assertTrue(is_array($data), "parsed render() result must be array");
		$this->assertArrayHasKey('reqId', $data, "render() must return the reqId we sent it");
		$this->assertSame('123', $data['reqId'], "render() must return the same reqId we sent it");
	}

	public function testRenderNotModifiedErrorWhenClientSigMatches()
	{
		$renderer = new JsonRenderer();
		// Get the signature of the data we'll return
		$sig = $renderer->getSignature($this->dataTable);

		// Send the signature in the request, such that the client
		// is saying it already has this data.
		$request = new Request(array(
			Request::DATASOURCE_REQUEST_PARAMETER =>
			RequestParameters::SIGNATURE_PARAMETER .':'. $sig
		));

		$response = new Response($request);
		$response->setDataTable($this->dataTable);

		$json = $renderer->render($response);
		$data = json_decode($json, true);

		$this->assertNotNull($data, "render() result must be valid json");
		$this->assertTrue(is_array($data), "parsed render() result must be array");
		$this->assertArrayHasKey('errors', $data, "render must have set an error message");
		$this->assertArrayNotHasKey('table', $data, "render must not send data with an error request");

		$errors = $data['errors'];
		$this->assertTrue(is_array($errors), "Errors value must be array");
		$reasonCode = $errors[0]['reason'];
		$this->assertSame(ReasonType::NOT_MODIFIED, $reasonCode, "Expect to receive NOT_MODIFIED response");
	}

	public function testRenderReturnsWarningsWhenTheyArePresent()
	{
		$dataTable = $this->dataTable;
		$dataTable->addWarning(new Warning(
			new ReasonType(ReasonType::DATA_TRUNCATED),
			"Test message 1"
		));
		$dataTable->addWarning(new Warning(
			new ReasonType(ReasonType::INVALID_QUERY),
			"Test message 2"
		));
		$response = new Response(new Request());
		$response->setDataTable($dataTable);
		$renderer = new JsonRenderer();
		$json = $renderer->render($response, new ResponseStatus(new StatusType(StatusType::WARNING)));
		$data = json_decode($json, true);

		$this->assertNotNull($data, "render result must be valid json");
		$this->assertTrue(is_array($data), "parsed render() result must be array");
		$this->assertSame(StatusType::WARNING, $data['status'], "render() must set status=WARNING");
		$this->assertArrayHasKey('sig', $data, "Data signature must be set");
		$this->assertArrayHasKey('table', $data, "Data table must be present");

		$warnings = $data['warnings'];
		$this->assertTrue(is_array($warnings), "Warnings must be an array");
		$this->assertSame(2, count($warnings), "Expected 2 warnings");
		$this->assertSame(ReasonType::DATA_TRUNCATED, $warnings[0]['reason'], "First warning reason must match");
		$this->assertSame("Test message 1", $warnings[0]['detailed_message'], "First warning detailed message must match");
		$this->assertSame(ReasonType::INVALID_QUERY, $warnings[1]['reason'], "Second warning reason must match");
		$this->assertSame("Test message 2", $warnings[1]['detailed_message'], "Second warning detailed message must match");
	}

	public function testColumnDescriptionJsonWithEmptyLabelAndEmptyPattern()
	{
		$expected = '{"id":"foo","type":"string"}';

		$cd = new ColumnDescription('foo', ValueType::STRING);
		$renderer = new JsonRenderer();
		$actual = $renderer->renderColumnDescriptionJson($cd);

		$this->assertSame($expected, $actual, "ColumnDescription JSON render must match");
	}

	public function testColumnDescriptionJsonWithEmptyPattern()
	{
		$expected = '{"id":"foo","type":"date","pattern":"YYYY-mm-dd"}';

		$cd = new ColumnDescription('foo', ValueType::DATE);
		$cd->setPattern('YYYY-mm-dd');
		$renderer = new JsonRenderer();
		$actual = $renderer->renderColumnDescriptionJson($cd);

		$this->assertSame($expected, $actual, "ColumnDescription JSON render must match");
	}

	public function testColumnDescriptionJsonWithLabelAndPattern()
	{
		$expected = '{"id":"foo","label":"foo label","type":"date","pattern":"YYYY-mm-dd"}';

		$cd = new ColumnDescription('foo', ValueType::DATE, 'foo label');
		$cd->setPattern('YYYY-mm-dd');
		$renderer = new JsonRenderer();
		$actual = $renderer->renderColumnDescriptionJson($cd);

		$this->assertSame($expected, $actual, "ColumnDescription JSON render must match");
	}

	public function testRenderEmptyCustomProperties()
	{
		$customProperties = array();
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCustomPropertiesString($customProperties);
		$this->assertSame(null, $actual, "Empty custom properties JSON should be NULL");
	}

	public function testRenderMultipleCustomProperties()
	{
		$customProperties = array(
			'prop1' => 'value1',
			'prop2' => 'value2',
		);
		$expected = json_encode($customProperties);
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCustomPropertiesString($customProperties);
		$this->assertSame($expected, $actual, "Empty custom properties JSON should be NULL");
	}

	public function testRenderCellJsonDate()
	{
		$expected = '{"v":new Date(2013,9,19)}';
		$date = new Date('Oct 19, 2013');
		$cell = new TableCell(new DateValue($date));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, true, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonNoDateConstructor()
	{
		$expected = '{"v":"Date(2013,9,19)"}';
		$date = new Date('Oct 19, 2013');
		$cell = new TableCell(new DateValue($date));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, true, false);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonWithFormatting()
	{
		$expected = '{"v":new Date(2013,9,19),"f":"2013-10-19"}';
		$date = new Date('Oct 19, 2013');
		$formattedDate = $date->format("Y-m-d");
		$cell = new TableCell(new DateValue($date), $formattedDate);
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, true, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonWithFormattingDisabled()
	{
		$expected = '{"v":new Date(2013,9,19)}';
		$date = new Date('Oct 19, 2013');
		$formattedDate = $date->format("Y-m-d");
		$cell = new TableCell(new DateValue($date), $formattedDate);
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, false, true, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonWithFormattingAndProperties()
	{
		$expected = '{"v":new Date(2013,9,19),"f":"2013-10-19","p":{"prop":"val"}}';
		$date = new Date('Oct 19, 2013');
		$formattedDate = $date->format("Y-m-d");
		$cell = new TableCell(new DateValue($date), $formattedDate);
		$cell->setCustomProperty('prop', 'val');
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, false, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonString()
	{
		$expected = '{"v":"foo"}';
		$cell = new TableCell(new TextValue("foo"));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, false, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonNullStringLastCell()
	{
		$expected = '{"v":null}';
		$cell = new TableCell(new TextValue(null));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, false, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonNullStringOptimizingNullValues()
	{
		$expected = '';
		$cell = new TableCell(new TextValue(null));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, true, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonBooleanTrue()
	{
		$expected = '{"v":true}';
		$cell = new TableCell(new BooleanValue(true));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, false, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonBooleanNull()
	{
		$expected = '{"v":null}';
		$cell = new TableCell(new BooleanValue(null));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, false, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonNumberValue()
	{
		$expected = '{"v":123}';
		$cell = new TableCell(new NumberValue(123));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, false, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonNumberNull()
	{
		$expected = '{"v":null}';
		$cell = new TableCell(new NumberValue(null));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, false, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonStringValue()
	{
		$expected = '{"v":"foo"}';
		$cell = new TableCell(new TextValue("foo"));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, false, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonStringNull()
	{
		$expected = '{"v":null}';
		$cell = new TableCell(new TextValue(null));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, false, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonDateValue()
	{
		$expected = '{"v":new Date(2013,0,1)}';
		$cell = new TableCell(new DateValue(new Date("2013-01-01")));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, false, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonDateTimeValue()
	{
		$expected = '{"v":new Date(2013,0,1,0,1,2)}';
		$cell = new TableCell(new DateTimeValue(new Date("2013-01-01 00:01:02")));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, false, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderCellJsonTimeOfDayValue()
	{
		$expected = '{"v":[0,1,2,0]}';
		$cell = new TableCell(new TimeOfDayValue(new Date("2013-01-01 00:01:02")));
		$renderer = new JsonRenderer();
		$actual = $renderer->renderCellJson($cell, true, false, true);
		$this->assertSame($expected, $actual, "TableCell rendered json must match expected value");
	}

	public function testRenderWithDataReturnsExpectedJson()
	{
		$expected = '{"version":"0.6","status":"ok","sig":"64bc093622add54643a29199ec583dc8","table":{"cols":[{"id":"date","type":"date"},{"id":"name","type":"string"},{"id":"count","type":"number"}],"rows":[{"c":[{"v":"Date(2013,9,19)"},{"v":"eleven"},{"v":11}]},{"c":[{"v":"Date(2013,0,1)"},{"v":"twenty_three"},{"v":23}]},{"c":[{"v":"Date(2013,6,1)"},{"v":null},{"v":35}]},{"c":[{"v":"Date(1999,11,31)"},{"v":"null_value"},{"v":null}]}]}}';
		$renderer = new JsonRenderer();
		$actual = $renderer->render($this->response);

		$this->assertSame($expected, $actual, "Result must match expected");
	}

	public function testRenderWithDataReturnsExpectedJsonWithOptimizedNulls()
	{
		$expected = '{"version":"0.6","status":"ok","sig":"64bc093622add54643a29199ec583dc8","table":{"cols":[{"id":"date","type":"date"},{"id":"name","type":"string"},{"id":"count","type":"number"}],"rows":[{"c":[{"v":"Date(2013,9,19)"},{"v":"eleven"},{"v":11}]},{"c":[{"v":"Date(2013,0,1)"},{"v":"twenty_three"},{"v":23}]},{"c":[{"v":"Date(2013,6,1)"},,{"v":35}]},{"c":[{"v":"Date(1999,11,31)"},{"v":"null_value"},{"v":null}]}]}}';
		$renderer = new JsonRenderer(true);
		$actual = $renderer->render($this->response);

		$this->assertSame($expected, $actual, "Result must match expected");
	}

	public function testRequestOutputTypeJsonpAffectsResponse()
	{
		$request = new Request(array(
			Request::DATASOURCE_REQUEST_PARAMETER =>
				RequestParameters::OUTPUT_TYPE_PARAMETER .':'. OutputType::JSONP,
		));

		$response = new Response($request);
		$response->setDataTable($this->dataTable);

		$renderer = new JsonRenderer();
		$json_p = $renderer->render($response);

		$prefix = "google.visualization.Query.setResponse(";
		$suffix = ");";
		$this->assertSame($prefix, substr($json_p,0,strlen($prefix)), "JSONP output must be prefixed with 'callbackFn('");
		$this->assertSame($suffix, substr($json_p,-1*strlen($suffix)), "JSONP output must be suffixed with ');'");

		$newDateIndex = strpos($json_p, '"v":new Date('); // JSONP style date
		$stringDateIndex = strpos($json_p, '"v":"Date('); // JSON style date
		$this->assertTrue($newDateIndex > 0, "Expect JSONP date format to be present");
		$this->assertFalse($stringDateIndex, "Expect JSON date format to NOT be found");
	}

	/**
	 * When sending an ERROR response, DO NOT send a DataTable or the data signature
	 * to the client, as per Google Visualization wire protocol spec.
	 */
	public function testErrorResponseDoesNotContainDataTable()
	{
		$responseStatus = new ResponseStatus(new StatusType(StatusType::ERROR),
			new ReasonType(ReasonType::INTERNAL_ERROR), "internal error test");
		$renderer = new JsonRenderer();
		$json = $renderer->render($this->response, $responseStatus);
		$data = json_decode($json, true);

		$this->assertNotNull($data, "render() result must be valid json");
		$this->assertTrue(is_array($data), "parsed render() result must be array");
		$this->assertArrayNotHasKey('table', $data, "render() must NOT return data table");
		$this->assertArrayNotHasKey('sig', $data, "render() must NOT return data signature");
	}
}