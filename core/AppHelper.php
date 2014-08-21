<?php

/**
 * Класс, отвечающий за загрузку данных конфигурации и обеспечивающий общий 
 * доступ приложения к ним.
 * @author coon
 */

namespace core;

class AppHelper {

	const APP_ROOT = APP_ROOT;

	private static $_instance				 = NULL;
	private $_configSet				 = array();
	private static $_mainConfigPattern		 = array(); // TODO concrete pattern map
	private static $_componentsWithAutoload = array('controllers', 'libs', 'vendor');
	private $_siteUrl				 = '';

	const COMPONENTS_ROOT_MAP	 = 'componentsRootMap';
	const ROUT_MAP			 = 'routeMap';
	const DEFAULT_LAYOUT		 = 'defaultLayout';
	const PAGE_404			 = 'page404';
	const FILE_UPLOADER		 = 'fileuploader';
	const FACEBOOK			 = 'facebook';

	/**
	 * @return self.
	 * @throws CoreException.
	 */
	private function __construct() {
		$sFilePath = self::APP_ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'main.php';
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

	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function getConfig($sName = '') {
		if (empty($sName)) {
			return $this->_configSet;
		} elseif (isset($this->_configSet[$sName])) {
			return $this->_configSet[$sName];
		} else {
			return NULL;
		}
	}

	private function _getSiteUrl() {
		// Copyright 2010, Sebastian Tschan
		$fHttp = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;
		return
				($fHttp ? 'https://' : 'http://') .
				(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] . '@' : '') .
				(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] .
						($fHttp && $_SERVER['SERVER_PORT'] === 443 ||
						$_SERVER['SERVER_PORT'] === 80 ? '' : ':' . $_SERVER['SERVER_PORT']))) .
				substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], DIRECTORY_SEPARATOR));
	}

	public function getSiteUrl() {
		return $this->_siteUrl;
	}

	public function getComponentUrl($sName, $fAsAbsolute = TRUE) {
		$aRootMap	 = $this->getConfig(self::COMPONENTS_ROOT_MAP);
		$sSiteUrl	 = '';
		if ($fAsAbsolute) {
			$sSiteUrl = $this->_siteUrl;
		}
		return isset($aRootMap[$sName]) ? $sSiteUrl . $aRootMap[$sName] : NULL;
	}

	public function getComponentRoot($sName) {
		$aRootMap = $this->getConfig(self::COMPONENTS_ROOT_MAP);
		return isset($aRootMap[$sName]) ? self::APP_ROOT . $aRootMap[$sName] : NULL;
	}

	public function getAutoloadRootSet() {
		$aRes		 = array(DIRECTORY_SEPARATOR);
		$aRootMap	 = $this->getConfig(self::COMPONENTS_ROOT_MAP);
		foreach (self::$_componentsWithAutoload as $sComponent) {
			if (isset($aRootMap[$sComponent])) {
				if ($sComponent == 'controllers') {
					$sCtrlRoot	 = substr($aRootMap[$sComponent], 0, -strlen(DIRECTORY_SEPARATOR . 'controllers'));
					$aRes[]		 = $sCtrlRoot != '' ? : DIRECTORY_SEPARATOR;
				} else {
					$aRes[] = $aRootMap[$sComponent];
				}
			} else {
				throw new CoreException("Required component root isn't set in main config.");
			}
		}
		return $aRes;
	}

}

