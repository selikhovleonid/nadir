<?php

namespace nadir\core;

/**
 * This is a class of controller wrapper, an instance of which perfoms delegated 
 * function - it calls target controller after the succesful auth or calls onFail 
 * auth class functionality in other case.
 * @author Leonid Selikhov
 */
class CtrlWrapper
{
    /** @var \nadir\core\AbstractWebCtrl The target controller object. */
    protected $ctrl = null;

    /**
     * The constructor assigns the object-wrapper with controller object.
     * @param \nadir\core\AbstractWebCtrl $oCtrl The controller object.
     */
    public function __construct(AbstractWebCtrl $oCtrl)
    {
        $this->ctrl = $oCtrl;
    }

    /**
     * The method calls user's auth checking, on successful complition of which
     * it invokes the target controller and the onFail method of Auth class in 
     * other case.
     * @param type $sName The action name of target controller.
     * @param type $aArgs The action parameters.
     */
    protected function processAuth($sName, & $aArgs)
    {
        if (class_exists('\extensions\core\Auth')) {
            $oAuth = new \extensions\core\Auth($this->ctrl->getRequest());
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
    }

    /**
     * This is the method-interseptor of method calling of the target controller.
     * @param string $sName The action name of target controller.
     * @param mixed[] $aArgs The action parameters.
     */
    public function __call($sName, $aArgs)
    {
        $this->processAuth($sName, $aArgs);
    }
}