<?php

/**
 * Description of ControllerResolver
 *
 * @author coon
 */

namespace core;

class ControllerResolver {

	private $_request	 = NULL;
	private $_routeMap	 = array();
	private $_ctrlName	 = '';
	private $_actionName = '';
	private $_actionArgs = array();

	public function __construct(Request $oRequest) {
		$this->_request	 = $oRequest;
		$this->_routeMap = AppHelper::getInstance()->getConfig('routeMap');
	}

	private function _createCtrl() {
		$oView			 = ViewFactory::createView($this->_ctrlName, $this->_actionName);
		$sCtrlNameFull	 = '\\controllers\\' . $this->_ctrlName;
		if (!is_null($oView)) {
			$sLayoutName = AppHelper::getInstance()->getConfig('defaultLayout');
			if (!is_null($sLayoutName)) {
				$oLayout = ViewFactory::createLayout($sLayoutName, $oView);
				$oCtrl = new $sCtrlNameFull($this->_request, $oLayout);
			} else {
				$oCtrl = new $sCtrlNameFull($this->_request, $oView);
			}
		} else {
			$oCtrl = new $sCtrlNameFull($this->_request);
		}
		return $oCtrl;
	}

	private function _callActionWithArgs(AController $oCtrl) {
		$oMethod = new \ReflectionMethod($oCtrl, $this->_actionName);
		$oMethod->invokeArgs($oCtrl, $this->_actionArgs);
	}

	private function _tryAssignController() {
		foreach ($this->_routeMap as $sRoute => $aCtrlConf) {
			if (preg_match('#^' . $sRoute . '/?$#', $this->_request->getUri(), $aParam)) {
				$this->_ctrlName	 = $aCtrlConf[0];
				$this->_actionName	 = $aCtrlConf[1];
				unset($aParam[0]);
				$this->_actionArgs	 = array_values($aParam);
				break;
			}
		}
	}

	private function _isControllerAssigned() {
		return !empty($this->_ctrlName) && !empty($this->_actionName);
	}

	public function run() {
		$this->_tryAssignController();
		if ($this->_isControllerAssigned()) {
			$oCtrl = $this->_createCtrl();
			if (empty($this->_actionArgs)) {
				$oCtrl->{$this->_actionName}();
			} else {
				$this->_callActionWithArgs($oCtrl);
			}
		} else {
			throw new CoreException('Unable assign controller with this route path.');
		}
	}

}

