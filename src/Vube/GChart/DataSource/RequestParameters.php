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
	const REQUEST_ID_PARAMETER = 'reqId';
	const VERSION_PARAMETER = 'version';
	const SIGNATURE_PARAMETER = 'sig';
	const OUTPUT_TYPE_PARAMETER = 'out';
	const RESPONSE_HANDLER_PARAMETER = 'responseHandler';
	const OUTPUT_FILE_NAME_PARAMETER = 'outFileName';

	static private $knownParameters = array(
		self::REQUEST_ID_PARAMETER => false, // no default value
		self::VERSION_PARAMETER => '0.6',
		self::SIGNATURE_PARAMETER => false, // no default value
		self::OUTPUT_TYPE_PARAMETER => 'json',
		self::RESPONSE_HANDLER_PARAMETER => 'google.visualization.Query.setResponse',
		self::OUTPUT_FILE_NAME_PARAMETER => false, // no default value
	);

	/**
	 * Parameters effective during this request
	 *
	 * Once initialized by the constructor or by a subsequent
	 * call to set(), this is an assoc array containing ALL known
	 * parameters, whether or not they were explicitly set by the
	 * client.
	 *
	 * A value of boolean false means the client did NOT set the
	 * parameter AND there is no default value for this parameter.
	 *
	 * All values will be strings if they are not boolean false.
	 *
	 * @var array
	 */
	private $params;

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
	 * @return false|string Value of the parameter if it has one, else FALSE
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
		// Note that the default value may === false, which hereafter means
		// the var was not set explicitly and also has no default value.
		foreach(self::$knownParameters as $name => $default)
		{
			if(! isset($this->params[$name]))
				$this->params[$name] = $default;
		}
	}
}