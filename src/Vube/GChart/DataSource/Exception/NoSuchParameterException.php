<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\Exception;

use Vube\GChart\DataSource\Exception;


/**
 * No Such Parameter Exception class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class NoSuchParameterException extends Exception
{
	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 * @param string $parameter [required] The name of the unknown parameter.
	 * @param int $code [optional] The Exception code.
	 * @param \Exception $previous [optional] The previous exception used for the exception chaining.
	 */
	public function __construct($parameter, $code = 0, \Exception $previous = null)
	{
		parent::__construct("No such parameter: $parameter", $code, $previous);
	}
}