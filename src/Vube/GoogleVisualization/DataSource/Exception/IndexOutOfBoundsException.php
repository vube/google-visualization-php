<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * IndexOutOfBoundsException class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class IndexOutOfBoundsException extends Exception {

	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 * @param int $index [required] The index that is out of bounds.
	 * @param int $code [optional] The Exception code.
	 * @param \Exception $previous [optional] The previous exception used for the exception chaining.
	 */
	public function __construct($index, $code=0, \Exception $previous=null)
	{
		parent::__construct("Index out of bounds: $index", $code, $previous);
	}
}