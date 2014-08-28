<?php

/**
 * Абстрактный класс, содержащий функционал записи (set), чтения (get) и проверки
 * на существование пользовательских переменных для наследующего класса.
 * Модификатор abstract указан немеренно, несмотря на отсутствие абстрактных
 * методов.
 * @author coon
 */

namespace core;

abstract class AUserPropAccessor {

	/** @var array Карта пользовательских пар ключ-значение. */
	private $_dataMap = array();
	
	/**
	 * @ignore.
	 */
	public function __construct() {
		// nothing here...
	}

	/**
	 * Определяет пользовательскую переменную. (Используется переопределение 
	 * "магического" метода).
	 * @param string $sKey Переменная.
	 * @param mixed $sValue Значение.
	 */
	public function __set($sKey, $sValue) {
		$this->_dataMap[$sKey] = $sValue;
	}

	/**
	 * Возвращает значение пользовательской переменной, если таковая установлена.
	 * (Используется переопределение "магического" метода).
	 * @param string $sKey Переменная.
	 * @return mixed|null
	 */
	public function __get($sKey) {
		if (array_key_exists($sKey, $this->_dataMap)) {
			return $this->_dataMap[$sKey];
		} else {
			return NULL;
		}
	}

	/**
	 * Переопределяет "магический" метод.
	 * @param string $sKey.
	 * @return boolean.
	 */
	public function __isset($sKey) {
		return isset($this->_dataMap[$sKey]);
	}

}
