<?php

/**
 * Класс контроллера-обертки, экземпляр которого выполняет делегирующую функцию - 
 * вызов метода целевого контроллера после успешного прохождения авторизации, либо 
 * вызов функционала onFail класса авторизации.
 * @author coon
 */

namespace core;

use \extensions\core\Auth;

class CtrlWrapper {

    /** @var \core\AWebCtrl Объект целевого контроллера. */
    protected $ctrl = NULL;

    /**
     * Связывает объект-обертку с объектом контроллера.
     * @param \core\AWebCtrl $oCtrl.
     */
    public function __construct(AWebCtrl $oCtrl) {
        $this->ctrl = $oCtrl;
    }

    /**
     * Метод осуществляет вызов проверки авторизации пользователя, при успешном 
     * прохождении которой, осуществляется вызов целевого action, при неуспешном - 
     * вызов метода onFail класса авторизации.
     * @param type $sName Имя action целевого контроллера.
     * @param type $aArgs Параметры action.
     */
    protected function processAuth($sName, & $aArgs) {
        $oAuth = new Auth($this->ctrl->getRequest());
        $oAuth->run();
        if ($oAuth->isValid()) {
            if (empty($aArgs)) {
                $this->ctrl->{$sName}();
            } else {
                $oMethod = new \ReflectionMethod($this->ctrl, $sName);
                $oMethod->invokeArgs($this->ctrl, $aArgs);
            }
        } else {
            $oAuth->onFail();
        }
    }

    /**
     * Метод-перехватчик вызовов методов целевого контроллера.
     * @param string $sName Имя action целевого контроллера.
     * @param mixed[] $aArgs Параметры action.
     */
    public function __call($sName, $aArgs) {
        $this->processAuth($sName, $aArgs);
    }

}
