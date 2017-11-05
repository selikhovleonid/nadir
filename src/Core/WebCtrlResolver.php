<?php

namespace Nadir\Core;

/**
 * This class provides the choosing of controller, passing the request parameters 
 * in it and binding with corresponding layout and view.
 * @author coon
 */
class WebCtrlResolver extends AbstractCtrlResolver
{
    /** @var \Nadir\Core\Request Объект запроса. */
    protected $request = null;

    /**
     * It inits the request property.
     * @param Nadir\Core\Request $oRequest.
     */
    public function __construct(Request $oRequest)
    {
        parent::__construct();
        $this->request = $oRequest;
    }

    /**
     * It creates the controller object, assignes it with default view and layout
     * objects.
     * @return \Nadir\Core\AWebController.
     */
    protected function createCtrl()
    {
        $oView         = ViewFactory::createView(
                $this->ctrlName, str_replace('action', '', $this->actionName)
        );
        $sCtrlNameFull = '\\Controllers\\'.$this->ctrlName;
        if (!is_null($oView)) {
            $sLayoutName = AppHelper::getInstance()->getConfig('defaultLayout');
            if (!is_null($sLayoutName)) {
                $oLayout = ViewFactory::createLayout($sLayoutName, $oView);
                $oCtrl   = new $sCtrlNameFull($this->request, $oLayout);
            } else {
                $oCtrl = new $sCtrlNameFull($this->request, $oView);
            }
        } else {
            $oCtrl = new $sCtrlNameFull($this->request);
        }
        return $oCtrl;
    }

    /**
     *  {@inheritdoc}
     */
    protected function tryAssignController()
    {
        $sMethod = strtolower($this->request->getMethod());
        if (isset($this->routeMap[$sMethod])) {
            foreach ($this->routeMap[$sMethod] as $sRoute => $aRouteConfig) {
                if (preg_match('#^'.$sRoute.'/?$#u',
                        urldecode($this->request->getUrlPath()), $aParam)
                ) {
                    AppHelper::getInstance()->setRouteConfig($aRouteConfig);
                    $this->ctrlName   = $aRouteConfig['ctrl'][0];
                    $this->actionName = $aRouteConfig['ctrl'][1];
                    unset($aParam[0]);
                    $this->actionArgs = array_values($aParam);
                    break;
                }
            }
        }
    }

    /**
     * It runs the controller action on execution.
     * @throws \core\Exception.
     */
    public function run()
    {
        $this->tryAssignController();
        if (!$this->isControllerAssigned()) {
            throw new Exception("It's unable to assign controller to this route path.");
        }
        $oCtrl        = $this->createCtrl();
        $oCtrlWrapper = new CtrlWrapper($oCtrl);
        $oCtrlWrapper->{$this->actionName}($this->actionArgs);
    }
}