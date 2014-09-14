<?php

/**
 * Класс-валидатор. Определяет, соответствуют ли данные некоторому шаблону.
 * @author coon
 */

namespace core;

class Validator {

    /** @var mixed[] Шаблон. */
    private $_pattern = array();

    /** @var array Массив ошибок. */
    private $_errorList = array();

    /**
     * @param mixed[] $aPattern Шаблон.
     * @return self.
     */
    public function __construct(array& $aPattern) {
        $this->_pattern = $aPattern;
    }

    /**
     * Метод проверяет соответствие данных шаблону.
     * @todo Реализовать логику.
     * @param mixed[] $aData Данные для валидации.
     * @return boolean.
     */
    public function isValid(array& $aData) {
        // TODO business logic
        return TRUE;
    }

    /**
     * Возвращает список ошибок.
     * @todo Реализовать логику.
     * @return array.
     */
    public function getErrors() {
        return $this->_errorList;
    }

}
