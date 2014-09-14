<?php

/**
 * Тестовый контроллер.
 * @author coon
 */

namespace controllers;

class Test extends \core\AController {

    public function actionDefault() {
        $this->getLayout()->isUserOnline = FALSE;
        $this->getView()->foo            = 'bar';
        $this->getView()->bar            = array(42, 'baz');
        $this->render();
    }

}