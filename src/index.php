<?php
require_once './core/Autoloader.php';

\nadir\core\Autoloader::getInstance()
    // The Application root setting.
    ->setAppRoot(__DIR__)
    // Adding the Core package to the autoload stack.
    // The Core directory is at the root of Web App by default.
    ->add(DIRECTORY_SEPARATOR)
    ->run();

// Running the Web Application.
\nadir\core\WebApp::getInstance()
    // Setting the relative path to the main configuration file of the
    // Application.
    ->setConfigFile('/config/main.php')
    ->run();
