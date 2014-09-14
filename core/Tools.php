<?php

/**
 * Класс инструментов разработчика.
 * @author coon.
 */

namespace core;

class Tools {
    /** @var integer Константа, определяющая количество пробелов на отступ. */

    const SPACES_PER_TAB = 4;

    /**
     * @ignore.
     */
    public function __construct() {
        // nothing here
    }

    /**
     * Возвращает строковое представление массива на текущем ярусе дерева.
     * Рекурсивный метод.
     * @param type $mVar Переменная.
     * @param type $nDepth Optional максимальная глубина печати дерева.
     * @param type $nLevel Optional текущий уровень дерева (ярус).
     * @param mixed[] $aObjects Optional массив объектов переменной.
     * @return string.
     */
    private static function _getDumpArrayIteration(&$mVar, $nDepth = 10, 
            $nLevel = 0, array& $aObjects = array()
    ) {
        $sOut       = '';
        $sSpacesOut = str_repeat(' ', self::SPACES_PER_TAB * $nLevel);
        if ($nDepth <= $nLevel) {
            $sOut .= "array\n{$sSpacesOut}(...)";
        } elseif (empty($mVar)) {
            $sOut .= 'array()';
        } else {
            $sSpacesIn = $sSpacesOut . str_repeat(' ', self::SPACES_PER_TAB);
            $sOut .= "array\n{$sSpacesOut}(";
            foreach ($mVar as $sKey => $mValue) {
                $sOut .= "\n{$sSpacesIn}"
                        . self::_getDumpIteration($sKey, $nDepth, 0, $aObjects)
                        . ' => '
                        . self::_getDumpIteration($mValue, $nDepth, $nLevel + 1, 
                            $aObjects);
            }
            $sOut .= "\n{$sSpacesOut})";
        }
        return $sOut;
    }

    /**
     * Возвращает строковое представление объекта на текущем уровне дерева.
     * Рекурсивный метод.
     * @param type $mVar Переменная.
     * @param type $nDepth Optional максимальная глубина печати дерева.
     * @param type $nLevel Optional текущий уровень дерева (ярус).
     * @param mixed[] $aObjects Optional массив объектов переменной.
     * @return string.
     */
    private static function _getDumpObjIteration(&$mVar, $nDepth = 10, 
        $nLevel = 0, array& $aObjects = array()
    ) {
        $sOut       = '';
        $sClassName = get_class($mVar);
        $sSpacesOut = str_repeat(' ', self::SPACES_PER_TAB * $nLevel);
        if (($iObj       = array_search($mVar, $aObjects, TRUE)) !== FALSE) {
            $sOut .= "{$sClassName} object #"
                    . ($iObj + 1)
                    . "\n{$sSpacesOut}(...)";
        } elseif ($nDepth <= $nLevel) {
            $sOut .= "{$sClassName} object"
                    . "\n{$sSpacesOut}(...)";
        } else {
            // Возвращает модификаторы свойств объекта.
            $funcGetPropMod = function(\ReflectionProperty $oProp) {
                        if ($oProp->isPublic()) {
                            $sOut = 'public';
                        } elseif ($oProp->isProtected()) {
                            $sOut = 'protected';
                        } else {
                            $sOut = 'private';
                        }
                        if ($oProp->isStatic()) {
                            $sOut .= ' static';
                        }
                        return $sOut;
                    };

            $iObj        = array_push($aObjects, $mVar);
            $sSpacesIn   = $sSpacesOut . str_repeat(' ', self::SPACES_PER_TAB);
            $oReflection = new \ReflectionClass($sClassName);
            $aProps      = $oReflection->getProperties();
            $sOut .= "{$sClassName} object #{$iObj}\n{$sSpacesOut}(";
            foreach ($aProps as $oProp) {
                $oProp->setAccessible(TRUE);
                $sOut .= "\n{$sSpacesIn}"
                        . '['
                        . self::_getDumpIteration($oProp->getName(), $nDepth, 0, 
                            $aObjects)
                        . ':' . $funcGetPropMod($oProp)
                        . '] => '
                        . self::_getDumpIteration($oProp->getValue($mVar), 
                            $nDepth, $nLevel + 1, $aObjects);
                if (!$oProp->isPublic()) {
                    $oProp->setAccessible(FALSE);
                }
            }
            $sOut .= "\n{$sSpacesOut})";
            unset($funcGetPropMod);
            unset($oReflection);
        }
        return $sOut;
    }

    /**
     * Метод возвращает строковое представление текущего уровня дерева переменной.
     * Является рекурсивным.
     * @param type $mVar Переменная.
     * @param type $nDepth Optional максимальная глубина печати дерева.
     * @param type $nLevel Optional текущий уровень дерева (ярус).
     * @param mixed[] $aObjects Optional массив объектов переменной.
     * @return string.
     */
    private static function _getDumpIteration(&$mVar, $nDepth = 10, $nLevel = 0, 
        array& $aObjects = array()
    ) {
        $sOut = '';
        switch (gettype($mVar)) {
            case 'NULL':
                $sOut .= 'NULL';
                break;
            case 'boolean':
                $sOut .= $mVar ? 'TRUE' : 'FALSE';
                break;
            case 'integer':
            case 'double':
                $sOut .= (string) $mVar;
                break;
            case 'string':
                $sOut .= "'" . addslashes($mVar) . "'";
                break;
            case 'unknown type':
                $sOut .= '{unknown}';
                break;
            case 'resource':
                $sOut .= '{resource}';
                break;
            case 'array':
                $sOut .= self::_getDumpArrayIteration($mVar, $nDepth, $nLevel, 
                    $aObjects);
                break;
            case 'object':
                $sOut .= self::_getDumpObjIteration($mVar, $nDepth, $nLevel, 
                    $aObjects);
                break;
            default:
                break;
        }
        return $sOut;
    }

    /**
     * Возвращает читабельную информацию о переменной (дамп).
     * @param type $mVar Переменная.
     * @param integer $nDepth Optional максимальная глубина печати дерева.
     * @return string.
     */
    public static function getDumpVar(&$mVar, $nDepth = 10) {
        return self::_getDumpIteration($mVar, $nDepth);
    }

    /**
     * Печатает читабельную информацию о переменной (дамп) с подсвеченным 
     * синтаксисом.
     * @param mixed $mVar Переменная.
     * @param integer $nDepth Optional максимальная глубина печати дерева.
     */
    public static function dumpVar(&$mVar, $nDepth = 10) {
        $sOut = self::getDumpVar($mVar, $nDepth);
        $sRaw = highlight_string("<?php\n{$sOut}", TRUE);
        echo preg_replace('#&lt;\?php<br \/>#', '', $sRaw, 1);
    }

}

