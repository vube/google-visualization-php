<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * RenderFailureException class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class RenderFailureException extends Exception {

	public function __construct(\Exception $previous)
	{
		$message = "Failed to render output: ".$previous->getMessage();
		parent::__construct($message, 0, $previous);
	}
}