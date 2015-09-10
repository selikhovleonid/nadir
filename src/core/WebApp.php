<?php

namespace core;

/**
 * Класс веб-приложения. Специфицирует абстрактное приложение.
 * @author coon
 */
class WebApp extends AApplication {

    /** @var self Объект-singleton текущего класса. */
    protected static $_instance = NULL;

    /**
     * @ignore.
     */
    protected function __construct() {
        // nothing here...
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest() {
        $oRequest      = new Request();
        $oCtrlResolver = new WebCtrlResolver($oRequest);
        $oCtrlResolver->run();
    }

}
