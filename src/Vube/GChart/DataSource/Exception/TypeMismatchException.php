<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\Exception;

use Vube\GChart\DataSource\Exception;


/**
 * Type Mismatch Exception class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class TypeMismatchException extends Exception
{
	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 * @param string|array $expectedTypes [required] The expected data type or an array of expected types.
	 * @param mixed &$data [required] The data that is of the wrong type.
	 * @param int $code [optional] The Exception code.
	 * @param \Exception $previous [optional] The previous exception used for the exception chaining.
	 */
	public function __construct($expectedTypes, &$data, $code = 0, \Exception $previous = null)
	{
		if(is_array($expectedTypes))
			$expectedTypes = "one of these: ".implode(", ", $expectedTypes);

		$type = gettype($data);

		if($type === 'object')
			$type = get_class($data);

		$message = "Data type mismatch (".$type."), expected ".$expectedTypes;

		parent::__construct($message, $code, $previous);
	}

}
