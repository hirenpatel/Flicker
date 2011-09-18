<?php
error_reporting (E_ALL | E_STRICT);

ini_set ('display_startup_errors', 1);
ini_set ('display_errors', 1);

set_include_path ('../library' . PATH_SEPARATOR . get_include_path());

require_once "Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

$front = Zend_Controller_Front::getInstance();

$front->throwExceptions(true);

$front->setControllerDirectory(array('default' => '../application/controllers'));

$front->dispatch();