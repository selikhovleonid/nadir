<?php

/**
 * Системный контроллер.
 * @author coon
 */

namespace controllers;

use core\AbstractWebCtrl;
use extensions\core\ISystemCtrl;
use core\Headers;

class System extends AbstractWebCtrl implements ISystemCtrl {

    public function actionPage401(array $aErrors) {
        Headers::getInstance()->addByHttpCode(401)->run();
        // put your code here...
        $this->render();
    }

    public function actionPage403(array $aErrors) {
        Headers::getInstance()->addByHttpCode(403)->run();
        // put your code here...
        $this->render();
    }

    public function actionPage404(array $aErrors) {
        Headers::getInstance()->addByHttpCode(404)->run();
        // put your code here...
        $this->render();
    }

}
