<?php

namespace core;

/**
 * This interface describes a started and stopped process functionality.
 * @author coon
 */
interface IProcess extends IRunnable {

    /**
     * It stops and/or kills the process.
     * @return void.
     */
    public function stop();
}
