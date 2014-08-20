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
	 * Обрабатывает объект Запроса, передавая его объекту Преобразователя 
	 * контроллеров.
	 * @return void.
	 */
	public function handleRequest();
}

