<?php
# Добавьте следующую директиву в файл .htaccess для того, чтобы не позволить
# httpd отдавать cli файл
# RedirectMatch 403 /cli.php$

require_once './core/Autoloader.php';

\core\Autoloader::getInstance()
        // Установка пути к корню приложения.
        ->setAppRoot(__DIR__)
        // Добавление корня пакета core в стек автоподгрузки.
        // По умолчанию директория core находится в корне приложения.
        ->add(DIRECTORY_SEPARATOR)
        ->run();

// Запуск cli приложения.
\core\CliApp::getInstance()
        ->setConfigFile('/config/main.php')
        ->run();

