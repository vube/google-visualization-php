<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * No Such Value Type Exception class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class NoSuchValueTypeException extends Exception
{
	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 * @param int $type [required] ValueType code that is invalid.
	 * @param int $code [optional] The Exception code.
	 * @param \Exception $previous [optional] The previous exception used for the exception chaining.
	 */
	public function __construct($type, $code = 0, \Exception $previous = null)
	{
		parent::__construct("No such value type: ".$type, $code, $previous);
	}

}
