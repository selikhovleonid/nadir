<?php

namespace nadir\core;

/**
 * This class provides the choosing of controller, passing the request parameters
 * in it and binding with corresponding layout and view.
 * @author Leonid Selikhov
 */
class WebCtrlResolver extends AbstractCtrlResolver
{
    /** @var \nadir\core\Request Объект запроса. */
    protected $request = null;

    /**
     * It inits the request property.
     * @param \nadir\core\Request $oRequest.
     */
    public function __construct(Request $oRequest)
    {
        parent::__construct();
        $this->request = $oRequest;
    }

    /**
     * It creates the controller object, assignes it with default view and layout
     * objects.
     * @return \nadir\core\AWebController.
     */
    protected function createCtrl()
    {
        $oView              = ViewFactory::createView(
            $this->ctrlName,
            str_replace('action', '', $this->actionName)
        );
        $aComponentsRootMap = AppHelper::getInstance()->getConfig('componentsRootMap');
        if (!isset($aComponentsRootMap['controllers'])) {
            throw new Exception("The field 'componentsRootMap.controllers' must be "
            ."presented in the main configuration file.");
        }
        $sCtrlNamespace = str_replace(
            \DIRECTORY_SEPARATOR,
            '\\',
            $aComponentsRootMap['controllers']
        );
        $sCtrlNameFull  = $sCtrlNamespace.'\\'.$this->ctrlName;
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
                if (preg_match(
                    '#^'.$sRoute.'/?$#u',
                    urldecode($this->request->getUrlPath()),
                    $aParam
                )
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
