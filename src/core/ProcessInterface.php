<?php

namespace nadir\core;

/**
 * This interface describes a started and stopped process functionality.
 * @author Leonid Selikhov
 */
interface ProcessInterface extends RunnableInterface
{

    /**
     * It stops and/or kills the process.
     * @return void.
     */
    public function stop();
}
