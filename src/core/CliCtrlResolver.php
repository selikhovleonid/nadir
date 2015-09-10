<?php

namespace core;

/**
 * Класс отвечает за связывание параметров вызова cli-скрипта с определенным 
 * контроллером и action в нем.
 * @author coon
 */
class CliCtrlResolver extends ACtrlResolver {

    /** @var string Роут вызова cli-скрипта (первый переданный параметр). */
    protected $requestRoute = NULL;

    /**
     * Инициализация свойств объекта.
     * @param string[] $aArgs Массив переданных скрипту аргументов.
     * @throws \core\Exception Генерируется в случае, если не был передан роут как
     * первый параметр вызова.
     */
    public function __construct(array $aArgs) {
        parent::__construct();
        if (!isset($aArgs[1])) {
            throw new Exception('Undefined route for cli request. '
            . "The route wasn't passed as first param when cli script was called.");
        }
        $this->requestRoute = $aArgs[1];
        unset($aArgs[0]);
        unset($aArgs[1]);
        $this->actionArgs   = array_values($aArgs);
    }

    /**
     *  {@inheritdoc}
     */
    protected function createCtrl() {
        $sCtrlNameFull = '\\controllers\\' . $this->ctrlName;
        return new $sCtrlNameFull();
    }

    /**
     *  {@inheritdoc}
     */
    protected function tryAssignController() {
        if (isset($this->routeMap['cli'])) {
            foreach ($this->routeMap['cli'] as $sRoute => $aRouteConfig) {
                if ($sRoute == $this->requestRoute) {
                    AppHelper::getInstance()->setRouteConfig($aRouteConfig);
                    $this->ctrlName   = $aRouteConfig['ctrl'][0];
                    $this->actionName = $aRouteConfig['ctrl'][1];
                    break;
                }
            }
        }
    }

    /**
     * Запуск action контроллера на исполнение.
     * @throws Exception.
     */
    public function run() {
        $this->tryAssignController();
        if ($this->isControllerAssigned()) {
            $oCtrl = $this->createCtrl();
            $oCtrl->{$this->actionName}($this->actionArgs);
        } else {
            throw new Exception('Unable assign controller with this route path.');
        }
    }

}
