<?php

/**
 * Description of Test
 *
 * @author coon
 */

namespace controllers;

class Test extends \core\AController {

	public function actionDefault() {
		$this->layout->isUserOnline	 = FALSE;
		$this->view->foo			 = 'bar';
		$this->view->bar			 = array(42, 'baz');
		$this->render();
	}

}