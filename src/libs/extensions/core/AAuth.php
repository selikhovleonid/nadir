<?php

/**
 * Абстрактный класс авторизации.
 * @author coon
 */

namespace extensions\core;

use \core\IRunnable;

abstract class AAuth implements IRunnable {

    /**
     * Метод осуществляет проверку на валидность авторизации пользователя.
     * @return boolean.
     */
    abstract public function isValid();

    /**
     * Метод содержит функционал, вызываемый по ветке неудачного прохождения
     * авторизации.
     */
    abstract public function onFail();
}
