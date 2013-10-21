<?php
/**
 * Simple Apache/Nginx Servlet Endpoint
 *
 * This endpoint will create a MyServlet object and use it to
 * populate the DataTable for the response.  Then it will write
 * the response to the client.
 *
 * If you have your own autoloader, remove the autoloader.php require
 * below and make sure you can lose Vube\GChart\DataSource\*
 *
 * @author Ross Perkins <ross@vubeology.com>
 */

try
{
	// Enable autoloader of Vube source (only if you don't have your own)
	require_once '../../src/Vube/GChart/DataSource/autoload.php';

	// Enable autoloader of your own source, or include it directly
	require_once './MyServlet.php';

	// Execute the servlet to generate the data table and send the output
	$servlet = new MyServlet();
	$servlet->execute();
}
catch(\Exception $e)
{
	// Fatal exception, cannot send any reasonable response
	header("Status: 500 Internal Server Error");
	error_log("Fatal exception while executing servlet: ".$e->__toString());
	exit;
}
