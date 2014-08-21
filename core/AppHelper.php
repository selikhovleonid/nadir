<?php

/**
 * Класс-помощник-приложения, отвечающий за загрузку данных конфигурации и обеспечивающий общий 
 * доступ приложения к ним. Реализован как Singleton.
 * @author coon
 */

namespace core;

class AppHelper {
	/**
	 * @var string Константа, определяющая корень веб приложения.
	 */

	const APP_ROOT = APP_ROOT;

	/**
	 * @var self Объект-singleton текущего класса.
	 */
	private static $_instance = NULL;

	/**
	 * @var mixed[] Множество конфигураций.
	 */
	private $_configSet = array();

	/**
	 * @var mixed[] Шаблон для валидации основной конфигурации.
	 * @todo Создать шаблон для валидатора. Возможно, следует использовать
	 * сторонний валидатор.
	 */
	private static $_mainConfigPattern	 = array(); // TODO concrete pattern map
	
	/**
	 * @var string Базовый URL сайта. 
	 */
	private $_siteBaseUrl		 = '';

	/**
	 * Загружает основной конфигурационный файл и выполняет проверку валидности.
	 * @todo В настоящее время валидация формально прописана, класс Validator 
	 * не содержит функциональности. Возможно, следует использовать
	 * сторонний валидатор.
	 * @return self.
	 * @throws CoreException.
	 */
	private function __construct() {
		$this->_siteBaseUrl	 = self::_getBaseUrl();
		$sFilePath			 = self::APP_ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'main.php';
		if (is_readable($sFilePath)) {
			$mConfig = include $sFilePath;
			if (is_array($mConfig)) {
				$oValidator = new Validator(self::$_mainConfigPattern);
				if ($oValidator->isValid($mConfig)) {
					$this->_configSet = $mConfig;
				} else {
					throw new CoreException("Main config isn't valid.");
				}
			} else {
				throw new CoreException("Main config must be an array.");
			}
		} else {
			throw new CoreException("Unable load {$sFilePath} as main config file.");
		}
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
	 * Возвращает конкретную конфигурацию по имени либо всю конфигурацию как массив.
	 * @param string $sName Имя конфигурации.
	 * @return array|null.
	 */
	public function getConfig($sName = '') {
		if (empty($sName)) {
			return $this->_configSet;
		} elseif (isset($this->_configSet[$sName])) {
			return $this->_configSet[$sName];
		} else {
			return NULL;
		}
	}

	/**
	 * Определят базовый URL сайта.
	 * @return string.
	 */
	private static function _getBaseUrl() {
		$sProtocol = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
		return $sProtocol . '://' . $_SERVER['SERVER_NAME'];
	}

	/**
	 * Метод-аксессор, возвращающий базовый URL сайта.
	 * @return string.
	 */
	public function getSiteBaseUrl() {
		return $this->_siteBaseUrl;
	}

	/**
	 * Метод возвращает абсолютный или относительный путь (URL) к компоненту по
	 * его имени. Полный URL обычно требуется для определения пути к медиа-файлам 
	 * (директория assets).
	 * @param string $sName.
	 * @param boolean $fAsAbsolute Флаг по умолчанию принимает значение TRUE. 
	 * @return string.
	 */
	public function getComponentUrl($sName, $fAsAbsolute = TRUE) {
		$aRootMap	 = $this->getConfig('componentsRootMap');
		$sSiteUrl	 = $fAsAbsolute ? $this->_siteBaseUrl : '';
		return isset($aRootMap[$sName]) ? $sSiteUrl . $aRootMap[$sName] : NULL;
	}

	/**
	 * Метод возвращает полный путь к родительской директории компонента по его имени.
	 * @param string $sName Имя компонента.
	 * @return string|null.
	 */
	public function getComponentRoot($sName) {
		$aRootMap = $this->getConfig('componentsRootMap');
		return isset($aRootMap[$sName]) ? self::APP_ROOT . $aRootMap[$sName] : NULL;
	}

}