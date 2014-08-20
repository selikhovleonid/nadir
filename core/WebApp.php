<?php

/**
 * Класс определяет центральную точку входа для всех запросов, создает 
 * конфигурированное веб приложение. 
 * Реализует шаблон Front Controller, является Singleton-ом.
 * @author coon
 */

namespace core;

class WebApp implements IFrontController {

	private function __construct() {
		// nothing here...
	}

	/**
	 * {@inheritdoc}
	 */
	public static function run() {
		$oInstance = new self();
		$oInstance->init();
	}

	/**
	 * {@inheritdoc}
	 */
	public function init() {
		$this->_initAutoload();
		$this->handleRequest();
	}

	/**
	 * {@inheritdoc}
	 */
	public function handleRequest() {
		$oRequest		 = new Request();
		$oCtrlResolver	 = new ControllerResolver($oRequest);
		$oCtrlResolver->run();
	}

	/**
	 * Инициализация автоподгрузки всех классов приложения. Объект 
	 * Автоподгрузчика получает все корни директорий из объекта 
	 * Помощника приложения, после чего связывает их с автоподгрузкой. 
	 * @return void.
	 */
	private function _initAutoload() {
		$aRoot = AppHelper::getInstance()->getAutoloadRootSet();
		foreach ($aRoot as $sRoot) {
			\Autoloader::getInstance()->add($sRoot);
		}
		\Autoloader::getInstance()->run();
	}

}