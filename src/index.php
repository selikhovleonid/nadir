<?php

require_once './core/Autoloader.php';

\core\Autoloader::getInstance()
        // Установка пути к корню приложения.
        ->setAppRoot(__DIR__)
        // Добавление корня пакета core в стек автоподгрузки.
        // По умолчанию директория core находится в корне веб-приложения.
        ->add(DIRECTORY_SEPARATOR)
        ->run();

// Запуск веб-приложения.
\core\WebApp::getInstance()
        // Определение относительного пути к файлу с основной конфигурацией 
        // приложения.
        ->setConfigFile('/config/main.php')
        ->run();
