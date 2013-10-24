<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Query\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * NoDataFieldsException class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class NoDataFieldsException extends Exception {

	public function __construct() {
		parent::__construct("No data fields found, a pivot is meaningless");
	}
} 