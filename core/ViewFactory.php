<?php

/**
 * Description of ViewFactory
 *
 * @author coon
 */

namespace core;

class ViewFactory {

	private function __construct() {
		// nothing here
	}

	public function createView($sCtrlName = NULL, $sActionName) {
		$sViewsRoot	 = AppHelper::getInstance()->getComponentRoot('views');
		$sAddPath	 = '';
		if (!empty($sCtrlName)) {
			$sAddPath .= DIRECTORY_SEPARATOR . strtolower($sCtrlName);
		}
		$sViewFile = $sViewsRoot
				. $sAddPath
				. DIRECTORY_SEPARATOR . strtolower($sActionName) . '.php';
		if (is_readable($sViewFile)) {
			return new View($sViewFile);
		} else {
			return NULL;
		}
	}

	public function createLayout($sLayoutName, View $oView) {
		$sLayoutsRoot	 = AppHelper::getInstance()->getComponentRoot('layouts');
		$sLayoutFile	 = $sLayoutsRoot . DIRECTORY_SEPARATOR . strtolower($sLayoutName) . '.php';
		if (is_readable($sLayoutFile)) {
			return new Layout($sLayoutFile, $oView);
		} else {
			return NULL;
		}
	}

}
