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
     * It calls the controller action with passage the parameters if necessary.
     * @param string $sName The action name of target controller.
     * @param array $aArgs The action parameters.
     */
    private function callActon($sName, array $aArgs)
    {
        if (empty($aArgs)) {
            $this->ctrl->{$sName}();
        } else {
            $oMethod = new \ReflectionMethod($this->ctrl, $sName);
            $oMethod->invokeArgs($this->ctrl, $aArgs);
        }
    }

    /**
     * The method calls user's auth checking, on successful complition of which
     * it invokes the target controller and the onFail method of Auth class in
     * other case.
     * @param string $sName The action name.
     * @param mixed[] $aArgs The action parameters.
     */
    protected function processAuth($sName, array $aArgs)
    {
        if (class_exists('\extensions\core\Auth')) {
            $oAuth = new \extensions\core\Auth($this->ctrl->getRequest());
            $oAuth->run();
            if ($oAuth->isValid()) {
                $this->callActon($sName, $aArgs);
            } else {
                $oAuth->onFail();
            }
        } else {
            $this->callActon($sName, $aArgs);
        }
    }

    /**
     * This is the method-interseptor of method calling of the target controller.
     * @param string $sName The action name of target controller.
     * @param mixed[] $aArgs The action parameters.
     */
    public function __call($sName, array $aArgs)
    {
        $this->processAuth($sName, $aArgs);
    }
}
