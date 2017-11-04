<?php

namespace core;

/**
 * This's the abstract class, which assign a controller with the request 
 * parameters.
 * @author coon
 */
abstract class AbstractCtrlResolver implements RunnableInterface
{
    /** @var array[] The route map. */
    protected $routeMap = array();

    /** @var string The controller name. */
    protected $ctrlName = '';

    /** @var string The action name. */
    protected $actionName = '';

    /** @var mixed[] These are additional parameters, which were passed to the action. */
    protected $actionArgs = array();

    /**
     * The constructor inits the properties of the route map object.
     * @return self.
     */
    public function __construct()
    {
        $this->routeMap = AppHelper::getInstance()->getConfig('routeMap');
    }

    /**
     * The method contains a controller object creating functionality.
     */
    abstract protected function createCtrl();

    /**
     * The method tries to assign the request rote to the concrete controller
     * action according the regexp map.
     * @return void.
     */
    abstract protected function tryAssignController();

    /**
     * The method checks if the request route was assigned to the concrete
     * controller.
     * @return boolean
     */
    protected function isControllerAssigned()
    {
        return !empty($this->ctrlName) && !empty($this->actionName);
    }

    /**
     * It starts the controller action on execution.
     */
    abstract public function run();
}