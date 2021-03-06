<?php

namespace nadir\core;

/**
 * The class provides the binding of call parameters of cli-script  with determinated
 * controller and action in it.
 * @author Leonid Selikhov
 */
class CliCtrlResolver extends AbstractCtrlResolver
{
    /** @var string The route of cli-script calling (the first passed param). */
    protected $requestRoute = null;

    /**
     * The oject properties initialization.
     * @param string[] $aArgs The array of passed to script arguments.
     * @throws \core\Exception It throws if it wasn't passed the route as the first
     * call param.
     */
    public function __construct(array $aArgs)
    {
        parent::__construct();
        if (!isset($aArgs[1])) {
            throw new Exception('Undefined route for the cli request. '
            ."The route wasn't passed as first param when the cli script was called.");
        }
        $this->requestRoute = $aArgs[1];
        unset($aArgs[0]);
        unset($aArgs[1]);
        $this->actionArgs   = array_values($aArgs);
    }

    /**
     *  {@inheritdoc}
     */
    protected function createCtrl()
    {
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
        return new $sCtrlNameFull();
    }

    /**
     *  {@inheritdoc}
     */
    protected function tryAssignController()
    {
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
     * @throws Exception It throws if it was unable to assign controller with
     * route path.
     */
    public function run()
    {
        $this->tryAssignController();
        if (!$this->isControllerAssigned()) {
            throw new Exception("It's unable to assign controller to this route path.");
        }
        $oCtrl = $this->createCtrl();
        $oCtrl->{$this->actionName}($this->actionArgs);
    }
}
