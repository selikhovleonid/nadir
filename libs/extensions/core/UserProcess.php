<?php

/**
 * Класс отвечает за реализацию функционала инициализации пользовательских 
 * конфигураций при загрузке приложения, а также уничтожение запущенных
 * пользователем процессов после, в случае необходимости. Реализован как Singleton.
 * @author coon.
 */

namespace extensions\core;

class UserProcess implements \core\IProcess {

	/** @var self Объект-singleton текущего класса. */
	private static $_instance = NULL;

	/**
	 * @ignore.
	 */
	private function __construct() {
		// Nothing here...
	}

	/**
	 * Возвращает singleton-экземпляр текущего класса.
	 * @return self.
	 */
	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Метод содержит реализацию функционала инициализации пользовательских 
	 * конфигураций.
	 * @return void.
	 */
	public function run() {
		// Put your code here;
	}

	/**
	 * Метод реализует функционал уничтожения запущенных пользователем процессов.
	 * @return void.
	 */
	public function stop() {
		// Put your code here;
	}

}

