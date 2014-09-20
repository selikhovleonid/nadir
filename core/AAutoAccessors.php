<?php

/**
 * Класс обеспечивает функционал генерации автоматических аксессоров (get-, set-
 * и isSet- методов) к публичным свойствам класса-наследника.
 * @author coon
 */

namespace core;

class AAutoAccessors {

    /**
     * Рефлексивный метод, осуществляющий проверку на наличие и общедоступность
     * свойства класса-наследника.
     * @param type $sPropName Имя метода.
     * @return boolean
     */
    private function _isPropChecked($sPropName) {
        $fRes        = FALSE;
        $sClassName  = get_class($this);
        $oReflection = new \ReflectionClass($sClassName);
        if ($oReflection->hasProperty($sPropName) 
            && $oReflection->getProperty($sPropName)->isPublic()
        ) {
            $fRes = TRUE;
        }
        unset($oReflection);
        return $fRes;
    }

    /**
     * Метод-перехватчик вызовов необъявленных методов класса. В случае вызова
     * метода, имя которого попадает под шаблоны setProperty, getProperty или
     * isPropertySet и наличия у вызывающего класса соответствующего публичного
     * свойства property, осуществляется вызов нужного аксессора, как
     * если бы он был описан в самом классе-наследнике. В противном случае 
     * генерируются исключения.
     * @param string $sName Имя метода.
     * @param mixed[] $aArgs Массив переданных методу аргументов.
     * @return mixed|boolean Результат действия аксессора - mixed для геттеров и
     * сеттеров, boolean для isSet. 
     * @throws Exception.
     */
    public function __call($sName, $aArgs) {
        // Lambda-function
        $funcGenException = function($sClassName, $sPropName) {
                    throw new Exception('Undefined or non public property' 
                        . "{$sClassName}::\${$sPropName}");
                };
                
        if (preg_match('#^get(\w+)$#', $sName, $aMatches)) {
            $sPropName = lcfirst($aMatches[1]);
            if ($this->_isPropChecked($sPropName)) {
                return $this->$sPropName;
            } else {
                $funcGenException(get_class($this), $sPropName);
            }
        } elseif (preg_match('#^set(\w+)$#', $sName, $aMatches)) {
            $sPropName = lcfirst($aMatches[1]);
            if ($this->_isPropChecked($sPropName)) {
                $this->$sPropName = $aArgs[0];
                return $aArgs[0];
            } else {
                $funcGenException(get_class($this), $sPropName);
            }
        } elseif (preg_match('#^is(\w+)Set$#', $sName, $aMatches)) {
            $sPropName = lcfirst($aMatches[1]);
            if ($this->_isPropChecked($sPropName)) {
                return !is_null($this->$sPropName);
            } else {
                $funcGenException(get_class($this), $sPropName);
            }
        } else {
            $sClassName = get_class($this);
            throw new Exception("Call to undefined method {$sClassName}::{$sName}");
        }
    }

}
