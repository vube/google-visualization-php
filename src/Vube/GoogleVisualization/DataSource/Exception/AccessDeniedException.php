<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Exception;

use Vube\GoogleVisualization\DataSource\Exception;


/**
 * AccessDeniedException class
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
class AccessDeniedException extends Exception {

	public function __construct()
	{
		parent::__construct("Access denied");
	}
}