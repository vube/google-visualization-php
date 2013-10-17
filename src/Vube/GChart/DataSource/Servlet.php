<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource;

use Vube\GChart\DataSource\DataTable\DataTable;
use Vube\GChart\DataSource\Exception\AccessDeniedException;


/**
 * Servlet class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
abstract class Servlet {

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
	public function getResponse()
	{
		$request = $this->getRequest();
		$outputType = new OutputType($request->getParameter(RequestParameters::OUTPUT_TYPE_PARAMETER));

		if($this->isRestrictedAccessMode())
			$this->verifyAccessAllowed($outputType);

		$data = $this->getDataTable($request);

		$response = new Response($request);
		$response->setDataTable($data);
		return $response;
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
		// In apache/nginx "Foo-Bar" header name looks like "FOO_BAR" in $_SERVER
		$header = strtoupper(Request::SAME_ORIGIN_HEADER);
		$header = str_replace('-', '_', $header);

		return isset($_SERVER[$header]);
	}

	/**
	 * @return bool
	 */
	public function isRestrictedAccessMode()
	{
		return true;
	}

}