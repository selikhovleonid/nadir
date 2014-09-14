<?php

/**
 * Системный контроллер.
 * @author coon
 */

namespace controllers;

class System extends \core\AController {

    public function actionPage404() {
        $this->render404();
    }

}
