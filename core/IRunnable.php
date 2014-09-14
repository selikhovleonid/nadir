<?php

/**
 * Интерфейс, описывающий функциональность запускаемого процесса.
 * @author coon.
 */

namespace core;

interface IRunnable {

    /**
     * Запускает процесс на исполнение.
     * @return void.
     */
    public function run();
}

