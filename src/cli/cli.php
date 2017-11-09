<?php
# Add follow line to the  .htaccess to give ability to return cli file with httpd.
# RedirectMatch 403 /cli.php$

require_once '../core/Autoloader.php';

// Running cli application.
\nadir\core\CliApp::getInstance()
    // The Application root setting.
    ->setAppRoot(dirname(__DIR__))
    // Setting the relative path to the main configuration file of the
    // Application.
    ->setConfigFile('/config/main.php')
    ->run();

