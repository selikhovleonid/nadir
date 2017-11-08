<?php
# Add follow line to the  .htaccess to give ability to return cli file with httpd.
# RedirectMatch 403 /cli.php$

require_once '../core/Autoloader.php';

\nadir\core\Autoloader::getInstance()
    // Setting the path to the root of application.
    ->setAppRoot(__DIR__.DIRECTORY_SEPARATOR.'..')
    // Adding the root of core package to the autoloading stack.
    // By default the core directory is in the application root.
    ->add(DIRECTORY_SEPARATOR)
    ->run();

// Running cli application.
\nadir\core\CliApp::getInstance()
    ->setConfigFile('/config/main.php')
    ->run();

