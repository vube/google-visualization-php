<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource;

use Vube\GChart\DataSource\Base\ReasonType;
use Vube\GChart\DataSource\DataTable\DataTable;
use Vube\GChart\DataSource\Exception\AccessDeniedException;


/**
 * Servlet class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
abstract class Servlet {

	protected $isRestrictedAccessModeEnabled = true;

	/**
	 * @param Request $request
	 * @return DataTable
	 */
	abstract public function getDataTable(Request $request);

	/**
	 * @return Request
	 */
	public function getRequest()
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
		$request = $this->getRequest();
		$response = new Response($request);
		return $response;

	}

	/**
	 * @return Response
	 * @throws \Exception if anything goes wrong constructing the Response
	 */
	public function execute()
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
			$response->setErrorResponse(ReasonType::INTERNAL_ERROR);
		}
		return $response;
	}

	/**
	 * @param Response &$response [required] [IN] [OUT]
	 */
	private function executeTrue(Response &$response)
	{
		$request = $response->getRequest();

		// Verify that the user is granted access to the data

		if($this->isRestrictedAccessMode())
			$this->verifyAccessAllowed($request->getOutputType());

		// Populate the data

		$data = $this->getDataTable($request);
		$response->setDataTable($data);
	}

	/**
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