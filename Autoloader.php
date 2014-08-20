<?php

/**
 * Автоподгрузчик классов PHP, реализован как Singleton.
 * Распознает как систему имен, принятую в пакетах PEAR, 
 * так и систему именований классов, основанную на неймспейсах.
 * @author coon.
 */
class Autoloader {
	
	/**
	 * Константа, определяющая корень веб приложения.
	 * @var string
	 */
	const APP_ROOT = APP_ROOT;

	/**
	 * @var Autoloader 
	 */
	private static $_instance = NULL;

	/**
	 * Множество корней директорий для автоподгрузки. 
	 * @var array {
	 * 		@type string $root Корень директории для подргузки.
	 * 		@type boolean $isLoaded Флаг, определяющий использована ли уже директория в автоподгрузке.
	 * }
	 */
	private static $_rootSet = array();

	private function __construct() {
		// nothing here
	}

	/**
	 * Возвращает singleton-экземпляр класса Autoloder.
	 * @return Autoloder.
	 */
	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Проверка на вхождение во множество уже зарегестрированных корней для автоподгрузки.
	 * @param string $sRoot Корень директории относительно корня приложения.
	 * @return boolean.
	 */
	private function _isInRootSet($sRoot) {
		foreach (self::$_rootSet as $aSet) {
			if ($aSet['root'] == $sRoot) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Добавляет корень директории для автоподгрузки, выполняя проверку на дублирование.
	 * @param string $sRoot Корень директории относительно корня приложения.
	 * @return Autoloader.
	 */
	public function add($sRoot) {
		$sRoot = trim($sRoot);
		if (substr($sRoot, -1) == DIRECTORY_SEPARATOR) {
			$sRoot = substr($sRoot, 0, -1);
		}
		if (strpos($sRoot, self::APP_ROOT) === FALSE) {
			$sRoot = self::APP_ROOT . $sRoot;
		}
		if (!$this->_isInRootSet($sRoot)) {
			self::$_rootSet[] = array(
				'root'		 => $sRoot,
				'isLoaded'	 => FALSE
			);
		}
		return self::$_instance;
	}

	/**
	 * Аксессор, возвращающий множество корней директорий для автоподгрузки.
	 * @return array Множество зарегистрированных корней.
	 */
	public function getRootSet() {
		return self::$_rootSet;
	}

	/**
	 * Запускает автоподгрузчик классов (становятся доступными для загрузки 
	 * все зарегистрированные и ранее неподгруженные корни директорий).
	 * @return void.
	 */
	public function run() {
		foreach (self::$_rootSet as & $aSet) {
			if (!$aSet['isLoaded']) {
				spl_autoload_register(self::_getFuncCall($aSet['root']));
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
						$sRelativePath = str_replace('\\', DIRECTORY_SEPARATOR, $sClassName);
					} elseif (preg_match('/_/', $sClassName)) {
						$sRelativePath = str_replace('_', DIRECTORY_SEPARATOR, $sClassName);
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