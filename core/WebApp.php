<?php

/**
 * Description of WebApp
 *
 * @author coon
 */

namespace core;

class WebApp implements IFrontController {

	private function __construct() {
	}

	public static function run() {
		$oInstance = new self();
		$oInstance->init();
	}

	public function init() {
		$this->_initAutoload();
		$this->handleRequest();
	}

	public function handleRequest() {
		$oRequest = new Request();
		$oCtrlResolver = new ControllerResolver($oRequest);
		$oCtrlResolver->run();
	}
	
	private function _initAutoload() {
		$aRoot = AppHelper::getInstance()->getAutoloadRootSet();
		foreach ($aRoot as $sRoot) {
			\Autoloader::getInstance()->add($sRoot);
		}
		\Autoloader::getInstance()->run();
	}
}

