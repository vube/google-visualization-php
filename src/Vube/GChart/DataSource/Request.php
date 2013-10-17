<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource;


/**
 * Request class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class Request
{
	const QUERY_REQUEST_PARAMETER = 'tq';

	const DATASOURCE_REQUEST_PARAMETER = 'tqx';

	const SAME_ORIGIN_HEADER = 'X-DataSource-Auth';

	/**
	 * @var string
	 */
	private $query;
	/**
	 * @var RequestParameters
	 */
	private $params;
	/**
	 * @var OutputType
	 */
	private $outputType = null;

	/**
	 * @param array $args
	 */
	public function __construct($args=array())
	{
		if(! is_array($args))
			throw new Exception\TypeMismatchException('array', $args);

		$this->setQuery(isset($args[self::QUERY_REQUEST_PARAMETER]) ? $args[self::QUERY_REQUEST_PARAMETER] : null);

		$this->setParams(isset($args[self::DATASOURCE_REQUEST_PARAMETER]) ? $args[self::DATASOURCE_REQUEST_PARAMETER] : null);
	}

	/**
	 * @return null|string
	 */
	public function getQuery()
	{
		return $this->query;
	}

	/**
	 * @return RequestParameters
	 */
	public function getParameters()
	{
		return $this->params;
	}

	/**
	 * @return OutputType
	 */
	public function getOutputType()
	{
		if($this->outputType === null)
			throw new Exception("Cannot call getOutputType() before setParams()");

		return $this->outputType;
	}

	/**
	 * @param string $query
	 */
	public function setQuery($query)
	{
		$this->query = $query;
	}

	/**
	 * @param null|string $dataSourceParameters
	 */
	public function setParams($dataSourceParameters)
	{
		$this->params = new RequestParameters();

		if($dataSourceParameters !== null)
			$this->params->set($dataSourceParameters);

		$code = $this->params->getParameter(RequestParameters::OUTPUT_TYPE_PARAMETER);
		$this->outputType = new OutputType($code);
	}
}