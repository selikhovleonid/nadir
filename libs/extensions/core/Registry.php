<?php

namespace extensions\core;

/**
 * Singleton-экземпляр класса является Регистром (реестром) - глобальным
 * хранилищем пользовательских переменных, время жизни которых равно времени 
 * жизненного цикла работы скрипта.
 * @author coon
 */
class Registry {

    /** @var self Объект-singleton текущего класса. */
    private static $_instance = NULL;

    /** @var mixed[] Хранилище переменных. */
    protected $store = array();

    /**
     * Возвращает singleton-экземпляр текущего класса.
     * @return self.
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Добавляет пользовательскую переменную в хранилище.
     * @param string $sKey Имя переменной.
     * @param mixed $mValue Значение переменной.
     * @return self
     */
    public function set($sKey, $mValue) {
        $this->store[$sKey] = $mValue;
        return self::$_instance;
    }

    /**
     * Возвращает значение переменной из хранилища по ключу, либо все хранилище.
     * @param string $sKey Имя переменной.
     * @return mixed|null
     */
    public function get($sKey = '') {
        if (empty($sKey)) {
            return $this->store;
        } else {
            return isset($this->store[$sKey]) ? $this->store[$sKey] : NULL;
        }
    }

}
