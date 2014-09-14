<?php

/**
 * Тестовый контроллер.
 * @author coon
 */

namespace controllers;

use core\AController;

class Test extends AController {

    public function actionDefault() {
        $this->getLayout()->isUserOnline = FALSE;
        $oModel                          = new \models\Test();
        $aData                           = $oModel->readDefault();
        $this->getView()->foo            = $aData['foo'];
        $this->getView()->bar            = $aData['bar'];
        $this->render();
    }

}