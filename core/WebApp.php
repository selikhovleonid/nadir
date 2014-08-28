<?php

/**
 * Класс определяет центральную точку входа для всех запросов, создает 
 * конфигурированное веб приложение. 
 * Реализует шаблон Front Controller, является Singleton-ом.
 * @author coon
 */

namespace core;

use extensions\core\UserProcess;

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
		$this->_initUserProcess();
		$this->handleRequest();
		$this->_stopUserProcess();
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
		$aRoot = AppHelper::getInstance()->getConfig('autoloadingRootSet');
		foreach ($aRoot as $sRoot) {
			\Autoloader::getInstance()->add($sRoot);
		}
		\Autoloader::getInstance()->run();
	}

	/**
	 * Метод выполняет запуск пользовательских процессов.
	 * @return void.
	 */
	private function _initUserProcess() {
		UserProcess::getInstance()->run();
	}

	/**
	 * Метод останавливает пользовательские процессы.
	 * @return void.
	 */
	private function _stopUserProcess() {
		UserProcess::getInstance()->stop();
	}

}