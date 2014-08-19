<?php

/**
 * Description of Validator
 *
 * @author coon
 */
namespace core;

class Validator {
	
	private $_pattern = array();
	private $_errorList = array();
	
	public function __construct(array $aPattern) {
		$this->_pattern = $aPattern;
	}

	public function isValid(array $aData) {
		// TODO business logic
		return TRUE;
	}
	
	public function getErrors() {
		return $this->_errorList;
	}

}
