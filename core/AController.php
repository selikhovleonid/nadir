<?php

/**
 * Description of AController
 *
 * @author coon
 */

namespace core;

abstract class AController {

	protected $request = NULL;
	protected $view	 = NULL;
	protected $layout	 = NULL;

	public function __construct(Request $oRequest, AView $oView = NULL) {
		$this->request = $oRequest;
		if (!is_null($oView)) {
			if ($oView instanceof View) {
				$this->view = $oView;
			} elseif ($oView instanceof Layout) {
				$this->layout	 = $oView;
				$this->view		 = $this->layout->view;
			}
		}
	}

	protected function setView($sCtrlName, $sActionName) {
		$this->view = ViewFactory::createView($sCtrlName, $sActionName);
		if (!is_null($this->layout)) {
			$this->layout->view = $this->view;
		}
	}
	
	protected function setLayout($sLayoutName) {
		if (!is_null($this->view)) {
			$this->layout = ViewFactory::createLayout($sLayoutName, $this->view);
		} else {
			throw new CoreException('Unable set Layout without View.');
		}
	}

	protected function render() {
		if (!is_null($this->layout)) {
			$this->layout->render();
		} elseif (!is_null($this->view)) {
			$this->partialRender();
		} else {
			throw new CoreException('Unable render with empty View.');;
		}
	}
	
	protected function partialRender() {
		$this->view->render();
	}
	
	protected function render404() {
		$sRawName = AppHelper::getInstance()->getConfig(AppHelper::PAGE_404);
		Headers::getInstance()->addByHttpCode(404)->run();
		$this->view = ViewFactory::createView(NULL, $sRawName);
		$this->view->render();
	}
	
	public function renderJson($mData) {
		echo stripslashes(json_encode($mData));
	}

}