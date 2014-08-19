<?php

defined('APP_ROOT') || define('APP_ROOT', __DIR__);
defined('DEBUG_APP') || define('DEBUG_APP', TRUE);

require_once 'Autoloader.php';
Autoloader::getInstance()
		->add(DIRECTORY_SEPARATOR) // add core lib root
		->run();
\core\WebApp::run();
