<?php

/**
 * Абстрактный класс представления.
 * @author coon
 */

namespace core;

abstract class AView {
	
	/** @var array Карта пользовательских пар ключ-значение. */
	private $_dataMap	 = array();

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
		return isset($this->$this->_dataMap[$sKey]);
	}

	/** @var string Путь к файлу с разметкой представления. */
	protected $filePath = '';

	/**
	 * Инициализация приватных свойств объекта.
	 * @param string $sViewFilePath Путь к файлу с разметкой представления.
	 * @return self.
	 */
	public function __construct($sViewFilePath) {
		$this->setFilePath($sViewFilePath);
	}
	
	/**
	 * Метод-акссесор к переменной, содержащей путь к файлу представления.
	 * @return string.
	 */
	public function getFilePath() {
		return $this->filePath;
	}
	
	/**
	 * Связывает объект с файлом представления.
	 * @param string $sViewFilePath Путь к файлу с разметкой представления.
	 * @throws CoreException.
	 */
	public function setFilePath($sViewFilePath) {
		if (is_readable($sViewFilePath)) {
			$this->filePath = $sViewFilePath;
		} else {
			throw new CoreException("View file {$sViewFilePath} isn't readable");
		}		
	}

	/**
	 * Абстрактный метод, отвечающий за рендеринг файла представления.
	 * @return void.
	 */
	abstract public function render();
}
