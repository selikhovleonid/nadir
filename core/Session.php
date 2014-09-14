<?php

/**
 * Класс-фасад для работы с сессией.
 * @author coon
 */

namespace core;

class Session implements IArrayCollection {

    /**
     * @ignore.
     */
    public function __construct() {
        // nothing here...
    }

    /**
     * Возвращает текущий идентификатор сессии.
     * @return string Id сессии.
     */
    public function getId() {
        return session_id();
    }

    /**
     * Определяет, запущена ли сессия или нет.
     * @return boolean.
     */
    public function isStarted() {
        return $this->getId() !== '';
    }

    /**
     * Устанавливает идентификатор для текущей сессии.
     * @param string $iSess.
     * @return void.
     */
    public function setId($iSess) {
        @session_id($iSess);
    }

    /**
     * Возвращает имя текущей сессии.
     * @return string.
     */
    public function getName() {
        return session_name();
    }

    /**
     * Метод устанавливает имя текущей сессии.
     * @param string $sName By default PHPSESSID.
     * @throws Exception.
     */
    public function setName($sName) {
        if (!empty($sName)) {
            if (!is_numeric($sName)) {
                @session_name($sName);
            } else {
                throw new Exception('The session name can\'t consist of digits only, '
                    . 'at least one letter must be present.');
            }
        } else {
            throw new Exception('Empty the session name value.');
        }
    }

    /**
     * Инициализирует данные новой сессии или продолжает текущую.
     * @param string $sSessName Optional имя сессии, имеет более высокий приоритет,
     * чем  параметр $iSess.
     * @param string $iSess Optional идентификатор сессии. Значение игнорируется,
     * если установлен параметр $sSessName.
     * @return string Идентификатор текущей сессии.
     */
    public function start($sSessName = NULL, $iSess = NULL) {
        if (!$this->isStarted()) {
            if (!is_null($sSessName)) {
                $this->setName($sSessName);
            }
            @session_start($iSess);
        };
        return $this->getId();
    }

    /**
     * Фиксирует данные сессии и закрывает ее.
     * @return void|null.
     */
    public function commit() {
        if ($this->isStarted()) {
            session_commit();
        }
    }

    /**
     * Уничтожает данные сессии.
     * @return boolean|null Результат разрушения данных сессии.
     */
    public function destroy() {
        $mRes = NULL;
        if ($this->isStarted()) {
            @session_unset();
            $mRes = session_destroy();
        }
        return $mRes;
    }

    /**
     * Полностью уничтожает сессию вместе с куки.
     * @return boolean|null Результат.
     */
    public function destroyWithCookie() {
        $mRes = NULL;
        if ($this->isStarted()) {
            $this->destroy();
            $mRes = setcookie($this->getName(), '', time() - 1, '/');
        }
        return $mRes;
    }

    /**
     * Добавляет переменную в сессию.
     * @param string $sKey Имя переменной.
     * @param mixed $mValue Значение переменной.
     * @return void.
     */
    public function add($sKey, $mValue) {
        $_SESSION[$sKey] = $mValue;
    }

    /**
     * Добавляет массив переменных (пар ключ-значение) в сессию.
     * @param array $aData
     * @return void.
     */
    public function addAll(array $aPairs) {
        foreach ($aPairs as $sKey => $mValue) {
            $this->add($sKey, $mValue);
        }
    }

    /**
     * Возвращает TRUE, если переменная с указанным ключом есть в сессии.
     * @param string $sKey.
     * @return boolean.
     */
    public function contains($sKey) {
        return isset($_SESSION[$sKey]);
    }

    /**
     * Возвращает TRUE, если сессия пуста.
     * @return boolean.
     */
    public function isEmpty() {
        return empty($_SESSION);
    }

    /**
     * Возвращает значение переменной в сессии по ее имени.
     * @param string $sKey.
     * @return mixed|null.
     */
    public function get($sKey) {
        return $this->contains($sKey) ? $_SESSION[$sKey] : NULL;
    }

    /**
     * Возвращаетс список имен переменных сессии.
     * @return string [].
     */
    public function getKeys() {
        return array_keys($_SESSION);
    }

    /**
     * Возвращает все переменные сессии как ассоциативный массив.
     * @return mixed[].
     */
    public function getAll() {
        $aRes = array();
        foreach ($this->getKeys() as $sKey) {
            $aRes[$sKey] = $this->get($sKey);
        }
        return $aRes;
    }

    /**
     * Удаляет переменную в сессии по ее имени.
     * @param string $sKey.
     * @return mixed|null Значение удаленной переменной.
     */
    public function remove($sKey) {
        if ($this->contains($sKey)) {
            $mRes = $_SESSION[$sKey];
            unset($_SESSION[$sKey]);
            return $mRes;
        } else {
            return NULL;
        }
    }

    /**
     * Очищает сессию, удаляя все сохраненные переменные.
     * @return mixed[] Массив удаленных переменных сессии.
     */
    public function removeAll() {
        $aRes = array();
        foreach ($this->getKeys() as $sKey) {
            $aRes[$sKey] = $this->remove($sKey);
        }
        return $aRes;
    }

    /**
     * Возвращает количество переменных в текущей сессии.
     * @return integer.
     */
    public function size() {
        return count($this->getKeys());
    }

}