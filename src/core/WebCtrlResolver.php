<?php

namespace core;

/**
 * Класс отвечает за выбор контроллера, передачу в него параметров запроса, 
 * связывание с соответствующими макетом и представлением.
 * @author coon
 */
class WebCtrlResolver extends ACtrlResolver {

    /** @var \core\Request Объект запроса. */
    protected $request = NULL;

    /**
     * Инициализация свойства Объекта запроса.
     * @param \core\Request $oRequest.
     */
    public function __construct(Request $oRequest) {
        parent::__construct();
        $this->request = $oRequest;
    }

    /**
     * Создает объект контроллера, связывая его с умалчиваемыми объектами 
     * представления и макета.
     * @return \core\AWebController.
     */
    protected function createCtrl() {
        $oView         = ViewFactory::createView(
                        $this->ctrlName, str_replace('action', '', $this->actionName)
        );
        $sCtrlNameFull = '\\controllers\\' . $this->ctrlName;
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
    protected function tryAssignController() {
        $sMethod = strtolower($this->request->getMethod());
        if (isset($this->routeMap[$sMethod])) {
            foreach ($this->routeMap[$sMethod] as $sRoute => $aRouteConfig) {
                if (preg_match('#^' . $sRoute . '/?$#u', 
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
     * Запуск action контроллера на исполнение.
     * @throws \core\Exception.
     */
    public function run() {
        $this->tryAssignController();
        if ($this->isControllerAssigned()) {
            $oCtrl        = $this->createCtrl();
            $oCtrlWrapper = new CtrlWrapper($oCtrl);
            $oCtrlWrapper->{$this->actionName}($this->actionArgs);
        } else {
            throw new Exception('Unable assign controller with this route path.');
        }
    }

}
