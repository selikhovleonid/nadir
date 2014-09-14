<?php

/**
 * Интерфейс описывает функциональность шаблона Front Controller.
 * @author coon
 */

namespace core;

interface IFrontController {

    /**
     * Запускает веб приложение на исполнение.
     * @return void.
     */
    public static function run();

    /**
     * Метод инициализирует настройки при первоначальном запуске приложения.
     * @return void.
     */
    public function init();

    /**
     * Обрабатывает объект Request, передавая его объекту ControllerResolver. 
     * @return void.
     */
    public function handleRequest();
}

