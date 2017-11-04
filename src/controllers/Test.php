<?php

namespace controllers;

use core\AbstractWebCtrl;

/**
 * This is demo test controller class.
 * @author coon
 */
class Test extends AbstractWebCtrl
{

    public function actionDefault()
    {
        $this->getView()->addSnippet('topbar');
        $oTopBar               = $this->getView()->getSnippet('topbar');
        $oTopBar->isUserOnline = false;
        $oModel                = new \models\Test();
        $aData                 = $oModel->readDefault();
        $this->getView()->foo  = $aData['foo'];
        $this->getView()->bar  = $aData['bar'];
        $this->render();
    }
}