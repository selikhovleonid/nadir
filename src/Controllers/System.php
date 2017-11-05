<?php

namespace Controllers;

use Nadir\Core\AbstractWebCtrl;
use Nadir\Extensions\Core\SystemCtrlInterface;
use Nadir\Core\Headers;

/**
 * This is system controller.
 * @author coon
 */
class System extends AbstractWebCtrl implements SystemCtrlInterface
{

    public function actionPage401(array $aErrors)
    {
        Headers::getInstance()->addByHttpCode(401)->run();
        // put your code here...
        $this->render();
    }

    public function actionPage403(array $aErrors)
    {
        Headers::getInstance()->addByHttpCode(403)->run();
        // put your code here...
        $this->render();
    }

    public function actionPage404(array $aErrors)
    {
        Headers::getInstance()->addByHttpCode(404)->run();
        // put your code here...
        $this->render();
    }
}