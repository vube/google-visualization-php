<?php
/**
 * Test Bootloader
 *
 * @author Ross Perkins <ross@vubeology.com>
 */

$composerAutoloadPhp = implode(DIRECTORY_SEPARATOR, array(__DIR__,'..','vendor','autoload.php'));
$loader = require_once $composerAutoloadPhp;
