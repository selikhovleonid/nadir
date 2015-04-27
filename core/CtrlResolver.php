<?php

/**
 * Класс отвечает за выбор контроллера, передачу в него параметров 
 * запроса, связывание с соответствующими макетом и представлением.
 * @author coon
 */

namespace core;

class CtrlResolver {

    /** @var \core\Request Объект запроса. */
    private $_request = NULL;

    /** @var array[] Карта маршрутов. */
    private $_routeMap = array();

    /** @var string Имя контроллера. */
    private $_ctrlName = '';

    /** @var string Имя action. */
    private $_actionName = '';

    /** @var mixed[] Дополнительные параметры, передаваемые в action. */
    private $_actionArgs = array();

    /**
     * Инициализация приватных свойств объекта.
     * @return self.
     */
    public function __construct(Request $oRequest) {
        $this->_request  = $oRequest;
        $this->_routeMap = AppHelper::getInstance()->getConfig('routeMap');
    }

    /**
     * Создает объект контроллера, связывая его с умалчиваемыми объектами 
     * представления и макета.
     * @return \core\Controller.
     */
    private function _createCtrl() {
        $oView         = ViewFactory::createView(
            $this->_ctrlName, 
            str_replace('action', '', $this->_actionName)
        );
        $sCtrlNameFull = '\\controllers\\' . $this->_ctrlName;
        if (!is_null($oView)) {
            $sLayoutName = AppHelper::getInstance()->getConfig('defaultLayout');
            if (!is_null($sLayoutName)) {
                $oLayout = ViewFactory::createLayout($sLayoutName, $oView);
                $oCtrl   = new $sCtrlNameFull($this->_request, $oLayout);
            } else {
                $oCtrl = new $sCtrlNameFull($this->_request, $oView);
            }
        } else {
            $oCtrl = new $sCtrlNameFull($this->_request);
        }
        return $oCtrl;
    }

    /**
     * Рефлексивный вызов action контроллера с пердачей дополнительных параметров
     * запроса.
     * @param \core\AController $oCtrl
     */
    private function _callActionWithArgs(AController $oCtrl) {
        $oMethod = new \ReflectionMethod($oCtrl, $this->_actionName);
        $oMethod->invokeArgs($oCtrl, $this->_actionArgs);
    }

    /**
     * Метод осуществляет попытку связать маршрут запроса с конкретным контроллером
     * и action в нем по карте регулярных выражений маршрутов.
     * @return void.
     */
    private function _tryAssignController() {
        foreach ($this->_routeMap as $sRoute => $aCtrlConf) {
            if (preg_match(
                    '#^' . $sRoute . '/?$#', 
                    $this->_request->getUrlPath(), 
                    $aParam
            )) {
                $this->_ctrlName   = $aCtrlConf[0];
                $this->_actionName = $aCtrlConf[1];
                unset($aParam[0]);
                $this->_actionArgs = array_values($aParam);
                break;
            }
        }
    }

    /**
     * Метод проверяет, осуществлена ли связь маршрута запроса с конкретным 
     * контроллером и action в нем.
     * @return boolean
     */
    private function _isControllerAssigned() {
        return !empty($this->_ctrlName) && !empty($this->_actionName);
    }

    /**
     * Запуск action контроллера на исполнение.
     * @throws Exception.
     */
    public function run() {
        $this->_tryAssignController();
        if ($this->_isControllerAssigned()) {
            $oCtrl = $this->_createCtrl();
            if (empty($this->_actionArgs)) {
                $oCtrl->{$this->_actionName}();
            } else {
                $this->_callActionWithArgs($oCtrl);
            }
        } else {
            throw new Exception('Unable assign controller with this route path.');
        }
    }

}

