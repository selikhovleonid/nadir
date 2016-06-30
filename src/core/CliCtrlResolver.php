<?php

namespace core;

/**
 * The class provides the binding of call parameters of cli-script  with determinated
 * controller and action in it.
 * @author coon
 */
class CliCtrlResolver extends ACtrlResolver {

    /** @var string The route of cli-script calling (the first passed param). */
    protected $requestRoute = NULL;

    /**
     * The oject properties initialization.
     * @param string[] $aArgs The array of passed to script arguments.
     * @throws \core\Exception It throws if it wasn't passed the route as the first 
     * call param.
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
     * It executes the action of controller.
     * @throws Exception.
     */
    public function run() {
        $this->tryAssignController();
        if ($this->isControllerAssigned()) {
            $oCtrl = $this->createCtrl();
            $oCtrl->{$this->actionName}($this->actionArgs);
        } else {
            throw new Exception("It's unable assign controller with this route path.");
        }
    }

}
