<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * No Such Column Id Exception class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class NoSuchColumnIdException extends Exception
{
	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 * @param string $id [required] The id of the unknown column.
	 * @param int $code [optional] The Exception code.
	 * @param \Exception $previous [optional] The previous exception used for the exception chaining.
	 */
	public function __construct($id, $code = 0, \Exception $previous = null)
	{
		parent::__construct("No such column id: $id", $code, $previous);
	}
}