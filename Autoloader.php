<?php

/**
 * Автоподгрузчик классов PHP, реализован как Singleton. Распознает как систему 
 * имен, принятую в пакетах PEAR, так и систему именований классов, основанную на 
 * неймспейсах.
 * @author coon.
 */
class Autoloader {

	/** @var string Путь к корню веб-приложения. */
	private $_appRoot = NULL;

	/** @var self Объект-singleton текущего класса. */
	private static $_instance = NULL;

	/** @var array[]. */
	private $_rootSet = array();

	/**
	 * @ignore.
	 */
	private function __construct() {
		// nothing here
	}

	/**
	 * Возвращает singleton-экземпляр класса Autoloder.
	 * @return self.
	 */
	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Проверка на вхождение во множество уже зарегестрированных корней для 
	 * автоподгрузки.
	 * @param string $sRoot Корень директории относительно корня приложения.
	 * @return boolean.
	 */
	private function _isInRootSet($sRoot) {
		foreach ($this->_rootSet as $aSet) {
			if ($aSet['root'] == $sRoot) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Устанавливает корень веб-приложения.
	 * @param string $sAppRoot Путь к корню приложения.
	 * @return self.
	 */
	public function setAppRoot($sAppRoot) {
		$this->_appRoot = $sAppRoot;
		return self::$_instance;
	}

	/**
	 * Возвращает корень веб-приложения.
	 * @return string.
	 */
	public function getAppRoot() {
		return $this->_appRoot;
	}

	/**
	 * Возвращает TRUE, если корень веб-приложения определен.
	 * @return boolean.
	 */
	public function isAppRootSet() {
		return !empty($this->_appRoot);
	}

	/**
	 * Добавляет (регистрирует) корень директории для автоподгрузки, выполняя 
	 * проверку на дублирование.
	 * @param string $sRoot Корень директории относительно корня приложения.
	 * @return self.
	 * @throws Exception.
	 */
	public function add($sRoot) {
		if ($this->isAppRootSet()) {
			$sRoot = trim($sRoot);
			if (substr($sRoot, -1) == DIRECTORY_SEPARATOR) {
				$sRoot = substr($sRoot, 0, -1);
			}
			if (strpos($sRoot, $this->getAppRoot()) === FALSE) {
				$sRoot = $this->getAppRoot() . $sRoot;
			}
			if (!$this->_isInRootSet($sRoot)) {
				$this->_rootSet[] = array(
					'root'		 => $sRoot,
					'isLoaded'	 => FALSE
				);
			}
			return self::$_instance;
		} else {
			throw new Exception("Application root isn't define.");
		}
	}

	/**
	 * Аксессор, возвращающий множество корней директорий для автоподгрузки.
	 * @return string[] Множество зарегистрированных корней.
	 */
	public function getRootSet() {
		return $this->_rootSet;
	}

	/**
	 * Запускает автоподгрузчик классов (становятся доступными для загрузки 
	 * все зарегистрированные и ранее неподгруженные корни директорий).
	 * @return void.
	 */
	public function run() {
		foreach ($this->_rootSet as & $aSet) {
			if (!$aSet['isLoaded']) {
				spl_autoload_register(self::_getFuncCall($aSet['root']), TRUE, TRUE);
				$aSet['isLoaded'] = TRUE;
			}
		}
	}

	/**
	 * Возвращает безымянную замкнутую функцию автоподгрузки классов PHP
	 * (как в стиле PEAR, так и основанную на неймспейсах).
	 * @param string $sRoot Корень директории относительно корня приложения.
	 * @return callable Замыкание (closure).
	 */
	private static function _getFuncCall($sRoot) {
		// Каррирование (currying)
		return function ($sClassName) use ($sRoot) {
					if (preg_match('/\\\\/', $sClassName)) {
						$sRelativePath = str_replace('\\', DIRECTORY_SEPARATOR, 
							$sClassName);
					} elseif (preg_match('/_/', $sClassName)) {
						$sRelativePath = str_replace('_', DIRECTORY_SEPARATOR, 
							$sClassName);
					} else {
						$sRelativePath = $sClassName;
					}
					$sPath = $sRoot . DIRECTORY_SEPARATOR . $sRelativePath . '.php';
					if (is_readable($sPath)) {
						require_once $sPath;
					}
				};
	}

}