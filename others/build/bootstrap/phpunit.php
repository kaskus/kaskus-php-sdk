<?php
ini_set('output_buffering', 'on');
ini_set('memory_limit', '-1');

require_once 'vendor/autoload.php';
require_once 'Autoloader.php';
require_once 'PhpunitConfig.php';

AutoLoader::registerDirectory(realpath(dirname(__FILE__)."/../"));
define('BASEPATH', '/');

error_reporting(E_ALL | E_STRICT);

// Autoload tests classes
$fakesDirectory = dirname(__FILE__) . '/../tests/Fakes/';
$utilityDirectory = dirname(__FILE__) . '/../tests/Utility/';
spl_autoload_register(function ($class) use ($fakesDirectory, $utilityDirectory) {
	$fakesFile = $fakesDirectory . $class . '.php';
	$utilityFile = $utilityDirectory . $class . '.php';
	if (is_file($fakesFile)) {
		include $fakesFile;
	} else if (is_file($utilityFile)) {
		include $utilityFile;
	}
});