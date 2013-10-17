<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource;


/**
 * ResponseWriter class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class ResponseWriter {

	private $headers = array();
	private $output = '';

	public function prepare(Response $response)
	{
		$request = $response->getRequest();
		$outputTypeCode = $request->getParameter(RequestParameters::OUTPUT_TYPE_PARAMETER);

		switch($outputTypeCode)
		{
			case OutputType::TSV_EXCEL:
				throw new Exception($outputTypeCode." output is not implemented");
				break;

			case OutputType::CSV:
				$this->headers['Content-Type'] = 'text/csv; charset=UTF-8';

				$outFilename = $request->getParameter(RequestParameters::OUTPUT_FILE_NAME_PARAMETER);
				if($outFilename === false)
					$outFilename = 'results.csv';

				// For security reasons, make sure this filename ends with .csv
				if(substr($outFilename, -4) !== '.csv')
					$outFilename .= '.csv';

				$this->headers['Content-Disposition'] = 'attachment; filename='.$outFilename;
				break;

			case OutputType::HTML:
				$this->headers['Content-Type'] = 'text/html; charset=UTF-8';
				break;

			case OutputType::JSONP:
				$this->headers['Content-Type'] = 'text/javascript; charset=UTF-8';
				break;

			case OutputType::JSON:
				$this->headers['Content-Type'] = 'application/json; charset=UTF-8';
				break;

			default:
				throw new Exception("Unknown Output Type: ".$outputTypeCode);
				break;
		}

		$this->output = $response->toString();
	}

	public function sendHeader($name, $value)
	{
		header($name.": ".$value);
	}

	public function send()
	{
		foreach($this->headers as $name => $value)
			$this->sendHeader($name, $value);

		echo $this->output;
	}
}