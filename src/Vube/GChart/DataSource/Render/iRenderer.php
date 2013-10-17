<?php
/**
 * @author Ross Perkins <ross@vubeology.com>
 */

namespace Vube\GChart\DataSource\Render;

use Vube\GChart\DataSource\Response;


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