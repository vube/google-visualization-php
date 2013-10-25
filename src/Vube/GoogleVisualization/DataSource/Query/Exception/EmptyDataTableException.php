<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * EmptyDataTableException class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class EmptyDataTableException extends Exception {

	public function __construct()
	{
		parent::__construct("You provided an empty DataTable");
	}
}