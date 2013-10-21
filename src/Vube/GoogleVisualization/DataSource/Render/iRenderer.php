<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GoogleVisualization\DataSource\Render;

use Vube\GoogleVisualization\DataSource\Response;


/**
 * iRenderer interface
 * 
 * @author Ross Perkins <ross@vubeology.com>
 */
interface iRenderer {

	/**
	 * @param Response $response
	 * @return string
	 */
	public function render(Response $response);
};