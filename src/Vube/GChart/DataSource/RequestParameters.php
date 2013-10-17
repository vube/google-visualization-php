<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource;
use Vube\GChart\DataSource\Exception\NoSuchParameterException;
use Vube\GChart\DataSource\Exception\TypeMismatchException;

/**
 * Data Source Request Parameters class
 *
 * This class is responsible for parsing the request parameters into a
 * format that we can easily use.
 *
 * @see https://developers.google.com/chart/interactive/docs/dev/implementing_data_source
 * @author Ross Perkins <ross@vubeology.com>
 */
class RequestParameters
{
	const OUTPUT_TYPE_PARAMETER = 'out';
	const OUTPUT_FILE_NAME_PARAMETER = 'outFileName';
	const RESPONSE_HANDLER_PARAMETER = 'responseHandler';

	private $params;

	static private $knownParameters = array(
		'reqId' => false,
		'version' => '0.6',
		'sig' => false,
		'out' => 'json',
		'responseHandler' => 'google.visualization.Query.setResponse',
		'outFileName' => false,
	);

	/**
	 * Constructor
	 * @param string $tqx Value of the 'tqx' argument in the Google Visualization Wire protocol
	 */
	public function __construct($tqx='')
	{
		$this->set($tqx);
	}

	/**
	 * @param string $name Name of the parameter whose existence to check.
	 * @return bool
	 */
	public function isParameterSet($name)
	{
		return isset($this->params[$name]) && $this->params[$name] !== false;
	}

	/**
	 * @param string $name Name of the parameter whose value to return.
	 * @return string|false Value of the parameter if it has one, else FALSE
	 * if the parameter was not set and has no default value.
	 * @throws NoSuchParameterException if $name is not a valid parameter name.
	 */
	public function getParameter($name)
	{
		if(! isset($this->params[$name]))
			throw new NoSuchParameterException($name);

		return $this->params[$name];
	}

	/**
	 * Get a list of all possible parameter names
	 * @return array
	 */
	public function getParameterNames()
	{
		return array_keys($this->params);
	}

	/**
	 * Set the parameters based on a tqx specification
	 * @param string $tqx Value of the 'tqx' argument in the Google Visualization Wire protocol
	 * @throws TypeMismatchException
	 */
	public function set($tqx)
	{
		if(! is_string($tqx))
			throw new TypeMismatchException('string', $tqx);

		$this->params = array();

		// If $tqx is an empty string, set an empty array
		if($tqx === '')
			$vars = array();
		else // otherwise split the string by semicolons
			$vars = explode(';', $tqx);

		// Traverse parameters that were explicitly set in the tqx value
		foreach($vars as $var)
		{
			list($name, $value) = explode(':', $var, 2);

			// Only preserve known parameters, ignore unknown parameters
			// as per the Google Visualization wire protocol specification
			if(isset($name, self::$knownParameters))
			{
				$this->params[$name] = $value;
			}
		}

		// Assign default values for any parameters that were not explicitly set
		foreach(self::$knownParameters as $name => $default)
		{
			if(! isset($this->params[$name]))
				$this->params[$name] = $default;
		}
	}
}