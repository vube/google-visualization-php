<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\Render;

use Vube\GChart\DataSource\OutputType;
use Vube\GChart\DataSource\RequestParameters;
use Vube\GChart\DataSource\Response;


/**
 * JsonRenderer class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class JsonRenderer implements iRenderer {

	/**
	 * @param Response $response
	 * @return string
	 */
	public function render(Response $response)
	{
		$output = '';

		$request = $response->getRequest();
		$outputTypeCode = $request->getOutputType()->getCode();
		$isJsonP = $outputTypeCode === OutputType::JSONP;

		if($isJsonP)
		{
			$responseHandler = $request->getParameters()->getParameter(RequestParameters::RESPONSE_HANDLER_PARAMETER);
			$output .= $responseHandler.'(';
		}

		$dataTable = $response->getDataTable();


		throw new Exception("NOT IMPLEMENTED");


		if($isJsonP)
			$output .= ")";
		return $output;
	}
}