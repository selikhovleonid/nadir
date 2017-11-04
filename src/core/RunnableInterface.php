<?php

namespace core;

/**
 * This class describes the running process functionality.
 * @author coon.
 */
interface RunnableInterface
{

    /**
     * It executes the process.
     * @return void.
     */
    public function run();
}