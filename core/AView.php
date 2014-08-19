<?php

/**
 * Description of AView
 *
 * @author coon
 */

namespace core;

abstract class AView {
	
	private $_dataMap	 = array();

	public function __set($sKey, $sValue) {
		$this->_dataMap[$sKey] = $sValue;
	}

	public function __get($sKey) {
		if (array_key_exists($sKey, $this->_dataMap)) {
			return $this->_dataMap[$sKey];
		} else {
			return NULL;
		}
	}

	public function __isset($sKey) {
		return isset($this->$this->_dataMap[$sKey]);
	}

	protected $filePath = '';

	public function __construct($sViewFilePath) {
		$this->setFilePath($sViewFilePath);
	}
	
	public function getFilePath() {
		return $this->filePath;
	}
	
	public function setFilePath($sViewFilePath) {
		if (is_readable($sViewFilePath)) {
			$this->filePath = $sViewFilePath;
		} else {
			throw new CoreException("View file {$sViewFilePath} isn't readable");
		}		
	}

	abstract public function render();
}
