<?php

namespace controllers;

/**
 * Контроллер интерфейса командной строки.
 * @author coon
 */
use core\ACliCtrl;

class Cli extends ACliCtrl {

    public function actionTest(array $aArgs) {
        if (!empty($aArgs)) {
            $this->printInfo('The test cli action was called with args: '
                    . implode(', ', $aArgs) . '.');
        } else {
            $this->printError(new \Exception('The test cli action was called without args.'));
        }
    }

}