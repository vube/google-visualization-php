<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource;

use Vube\GoogleVisualization\DataSource\Base\ReasonType;
use Vube\GoogleVisualization\DataSource\DataTable\DataTable;
use Vube\GoogleVisualization\DataSource\Exception\AccessDeniedException;
use Vube\GoogleVisualization\DataSource\Query\Engine\QueryEngine;
use Vube\GoogleVisualization\DataSource\Query\Query;


/**
 * Servlet class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
abstract class Servlet {

	/**
	 * Is Restricted Access Mode enabled?
	 *
	 * In restricted access mode, JSON and JSONP requests are
	 * expressly refused unless the same-origin header is sent
	 * with the request.
	 *
	 * @see https://developers.google.com/chart/interactive/docs/dev/implementing_data_source#security_considerations
	 * @var bool
	 */
	protected $isRestrictedAccessModeEnabled = true;

	/**
	 * @param Request $request
	 * @return DataTable
	 */
	abstract public function & getDataTable(Request $request);

	/**
	 * Constructor
	 */
	public function __construct() {}

	/**
	 * @return Request
	 */
	public function constructRequest()
	{
		// use apache/nginx method for retrieving GET query arguments
		$request = new Request($_GET);
		return $request;
	}

	/**
	 * @return Response
	 */
	public function constructResponse()
	{
		$request = $this->constructRequest();
		$response = new Response($request);
		return $response;

	}

	/**
	 * @return ResponseWriter
	 */
	public function constructResponseWriter()
	{
		$writer = new ResponseWriter();
		return $writer;
	}

	/**
	 * @return Response
	 * @throws \Exception if anything goes wrong constructing the Response
	 */
	public function generateResponse()
	{
		// Any exceptions thrown here will propagate up
		$response = $this->constructResponse();

		// Now we have a response, any exceptions thrown
		// while executing will set an error status in response
		try
		{
			$this->executeTrue($response);
		}
		catch(AccessDeniedException $e)
		{
			$response->setErrorResponse(ReasonType::ACCESS_DENIED);
		}
		catch(\Exception $e)
		{
			$response->setErrorResponse(ReasonType::INTERNAL_ERROR, $e->getMessage());
		}
		return $response;
	}

	/**
	 * Execute the servlet; generate the response and write it to the client
	 */
	public function execute()
	{
		$response = $this->generateResponse();

		$writer = $this->constructResponseWriter();
		$writer->send($response);
	}

	/**
	 * @param Response &$response [required] [IN] [OUT]
	 */
	protected function executeTrue(Response &$response)
	{
		$request = $response->getRequest();

		// Verify that the user is granted access to the data
		if($this->isRestrictedAccessMode())
			$this->verifyAccessAllowed($request->getOutputType());

		// Populate the data
		$data =& $this->getDataTable($request);

		// Apply query, if any
		$query = Query::constructFromString($request->getQuery());
		if(! $query->isEmpty())
			$data =& QueryEngine::execute($query, $data);

		$response->setDataTable($data);
	}

	/**
	 * @see https://developers.google.com/chart/interactive/docs/dev/implementing_data_source#security_considerations
	 * @param OutputType $outputType
	 * @throws AccessDeniedException
	 */
	public function verifyAccessAllowed(OutputType $outputType)
	{
		// Check HTTP headers to make sure the SAME_ORIGIN_HEADER
		// was sent with the request.

		$outputCode = $outputType->getCode();

		if($outputCode !== OutputType::HTML &&
			$outputCode !== OutputType::CSV &&
			$outputCode !== OutputType::TSV_EXCEL &&
			! $this->isSameOrigin())
			throw new AccessDeniedException();
	}

	/**
	 * @return bool
	 */
	public function isSameOrigin()
	{
		$header = $this->getSameOriginHeaderApacheName();
		$isSameOrigin = isset($_SERVER[$header]);
		return $isSameOrigin;
	}

	/**
	 * @return string Name of the same-origin header in Apache $_SERVER array
	 */
	public function getSameOriginHeaderApacheName()
	{
		// In apache/nginx "Foo-Bar" header name looks like "FOO_BAR" in $_SERVER
		$header = strtoupper(Request::SAME_ORIGIN_HEADER);
		$header = str_replace('-', '_', $header);
		return $header;
	}

	/**
	 * @return bool
	 */
	public function isRestrictedAccessMode()
	{
		return $this->isRestrictedAccessModeEnabled;
	}

}