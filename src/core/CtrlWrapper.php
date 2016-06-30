<?php

namespace core;

use \extensions\core\Auth;

/**
 * This's a class of controller wrapper, an instance of which perfoms delegated 
 * function - it calls target controller after the succesful auth or calls onFail 
 * auth class functionality in other case.
 * @author coon
 */
class CtrlWrapper {

    /** @var \core\AWebCtrl The target controller object. */
    protected $ctrl = NULL;

    /**
     * The constructor assigns the object-wrapper with controller object.
     * @param \core\AWebCtrl $oCtrl The controller object.
     */
    public function __construct(AWebCtrl $oCtrl) {
        $this->ctrl = $oCtrl;
    }

    /**
     * The method calls user's auth checking, on successful complition of which
     * it invokes the target controller and the onFail method of Auth class in 
     * other case.
     * @param type $sName The action name of target controller.
     * @param type $aArgs The action parameters.
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
     * This's the method-interseptor of method calling of the target controller.
     * @param string $sName The action name of target controller.
     * @param mixed[] $aArgs The action parameters.
     */
    public function __call($sName, $aArgs) {
        $this->processAuth($sName, $aArgs);
    }

}
