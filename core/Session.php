<?php

/**
 * Класс-фасад для работы с сессией.
 * @author coon
 */

namespace core;

class Session {

	/**
	 * Инициализирует данные сессии.
	 * @return string Id сессии.
	 */
	public static function start() {
		@session_start();
		return self::getId();
	}

	/**
	 * Возвращает Id сессии.
	 * @return string Id сессии.
	 */
	public static function getId() {
		return session_id();
	}

}

