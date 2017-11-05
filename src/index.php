<?php
require_once './Core/Autoloader.php';

\Nadir\Core\Autoloader::getInstance()
    // The Application root setting.
    ->setAppRoot(__DIR__)
    // Adding the Core package to the autoload stack.
    // The Core directory is at the root of Web App by default.
    ->add(DIRECTORY_SEPARATOR)
    ->run();

// Running the Web Application.
\Nadir\Core\WebApp::getInstance()
    // Setting the relative path to the main configuration file of the
    // Application.
    ->setConfigFile('/Config/main.php')
    ->run();
