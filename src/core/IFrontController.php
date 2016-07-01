<?php

namespace core;

/**
 * The interface describes the Front Controller pattern functionality.
 * @author coon
 */
interface IFrontController {

    /**
     * It's the main executable method. It runs the application.
     * @return void.
     */
    public function run();

    /**
     * The method inits settings at initial appliction startup.
     * @return void.
     */
    public function init();

    /**
     * It handles the Request object passing it to the ControllerResolver object.
     * @return void.
     */
    public function handleRequest();
}
