<?php

namespace Controllers;

use Nadir\Core\AbstractCliCtrl;

/**
 * This is command line interface controller.
 * @author coon
 */
class Cli extends AbstractCliCtrl
{

    public function actionTest(array $aArgs)
    {
        if (!empty($aArgs)) {
            $this->printInfo('The test cli action was called with args: '
                .implode(', ', $aArgs).'.');
        } else {
            $this->printError(new \Exception('The test cli action was called without args.'));
        }
    }
}