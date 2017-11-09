<?php
require_once '../../vendor/autoload.php';

// Running the Web Application.
\nadir\core\WebApp::getInstance()
    // The Application root setting.
    ->setAppRoot(dirname(__DIR__))
    // Setting the relative path to the main configuration file of the
    // Application.
    ->setConfigFile('/config/main.php')
    ->run();
