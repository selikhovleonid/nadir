<?php

namespace core;

/**
 * Класс cli-приложения (приложения интерфейса командной строки).
 * @author coon
 */
class CliApp extends AApplication {

    /** @var self Объект-singleton текущего класса. */
    protected static $_instance = NULL;

    /**
     * @ignore.
     */
    protected function __construct() {
        // nothing here...
    }

    /**
     * Обрабатывает параметры вызова cli-скрипта, передавая их объекту CliCtrlResolver. 
     * @global string[] $argv Массив переданных cli-скрипту аргументов.
     * @throws \core\Exception Генерируется в случае попытки вызова cli-скрипта
     * вне интерфейса командной строки.
     */
    public function handleRequest() {
        global $argv;
        if (!is_array($argv) || empty($argv)) {
            throw new Exception("Invalid value of the cli args array given.");
        }
        $oCtrlResolver = new CliCtrlResolver($argv);
        $oCtrlResolver->run();
    }

}
