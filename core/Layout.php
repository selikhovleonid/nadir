<?php

/**
 * Description of Layout
 *
 * @author coon
 */
namespace core;

class Layout extends AView {
	
	public $view = NULL;	

	public function __construct($sLayoutFilePath, View $oView) {
		parent::__construct($sLayoutFilePath);
		$this->view = $oView;
	}
	
	public function render() {
		require_once($this->filePath);
	}

}

