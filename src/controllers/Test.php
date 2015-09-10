<?php

/**
 * Тестовый контроллер.
 * @author coon
 */

namespace controllers;

use core\AWebCtrl;

class Test extends AWebCtrl {

    public function actionDefault() {
        $this->getView()->addSnippet('topbar');
        $oTopBar               = $this->getView()->getSnippet('topbar');
        $oTopBar->isUserOnline = FALSE;
        $oModel                = new \models\Test();
        $aData                 = $oModel->readDefault();
        $this->getView()->foo  = $aData['foo'];
        $this->getView()->bar  = $aData['bar'];
        $this->render();
    }

}
