<?php

// Вкл/выкл режима отладки.
defined('DEBUG_MODE') || define('DEBUG_MODE', TRUE);
if (DEBUG_MODE) {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
}

require_once 'Autoloader.php';
Autoloader::getInstance()
        // Установка пути к корню приложения.
        ->setAppRoot(__DIR__)
        // Добавление корня пакета core в стек автоподгрузки.
        // По умолчанию директория core находится в корне веб-приложения.
        ->add(DIRECTORY_SEPARATOR)
        ->run();

// Запуск веб приложения.
\core\WebApp::run();
