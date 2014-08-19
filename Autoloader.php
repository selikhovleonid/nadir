<?php

/**
 * Description of Autoloader
 *
 * @author coon
 */

if (DEBUG_APP) {
	error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

class Autoloader {
	
	const APP_ROOT = APP_ROOT;
	private static $_instance = NULL;
	private static $_rootSet = array();

	private function __construct() {
		// nothing here
	}

	public static function getInstance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function _isInRootSet($sRoot) {
		foreach (self::$_rootSet as $aSet) {
			if ($aSet['root'] == $sRoot) {
				return TRUE;
			}
		}
		return FALSE;
	}

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
	
	public function getRootSet() {
		return self::$_rootSet;
	}

	public function run() {
		foreach (self::$_rootSet as & $aSet) {
			if (!$aSet['isLoaded']) {
				spl_autoload_register(self::_getFuncCall($aSet['root']));
				$aSet['isLoaded'] = TRUE;
			}
		}
	}

	private static function _getFuncCall($sRoot) {
	// Currying
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