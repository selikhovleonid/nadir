<?php

namespace core;

/**
 * Абстрактный класс отвечает за связывание контроллера с параметрами вызова 
 * (запроса).
 * @author coon
 */
abstract class ACtrlResolver implements IRunnable {

    /** @var array[] Карта маршрутов. */
    protected $routeMap = array();

    /** @var string Имя контроллера. */
    protected $ctrlName = '';

    /** @var string Имя action. */
    protected $actionName = '';

    /** @var mixed[] Дополнительные параметры, передаваемые в action. */
    protected $actionArgs = array();

    /**
     * Инициализация свойства карты маршрутов объекта.
     * @return self.
     */
    public function __construct() {
        $this->routeMap = AppHelper::getInstance()->getConfig('routeMap');
    }

    /**
     * Метод содержит функционал создания объекта контроллера.
     */
    abstract protected function createCtrl();

    /**
     * Метод осуществляет попытку связать маршрут запроса с конкретным контроллером
     * и action в нем по карте регулярных выражений маршрутов.
     * @return void.
     */
    abstract protected function tryAssignController();

    /**
     * Метод проверяет, осуществлена ли связь маршрута запроса с конкретным 
     * контроллером и action в нем.
     * @return boolean
     */
    protected function isControllerAssigned() {
        return !empty($this->ctrlName) && !empty($this->actionName);
    }

    /**
     * Запуск action контроллера на исполнение.
     */
    abstract public function run();
}
