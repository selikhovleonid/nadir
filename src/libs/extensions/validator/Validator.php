<?php

namespace extensions\validator;

/**
 * This is class for input data validation.
 * @author coon
 */
class Validator implements \core\RunnableInterface
{
    /** @var mixed[] Input data for validation. */
    protected $data = null;

    /** @var array Set of fields and their corresponding rules for validation. */
    protected $items = array();

    /** @var array Set of validation rules. */
    protected $rules = array();

    /** @var array Stack of data errors which occured during the validation. */
    protected $errors = array();

    /** @var boolean Flag is equal true when the data is validated. */
    protected $isRan = false;

    /**
     * The constructor initializes the validator and also sets the input data for
     * validation.
     * @param mixed[] $aData Input data.
     */
    public function __construct($aData)
    {
        if (is_array($aData)) {
            $this->data = $aData;
            $this->init();
        } else {
            $this->isRan = true;
            $this->addError('Invalid data set format.');
        }
    }

    /**
     * The method returns a tree element by a key, which is formed as a string
     * separated by dots and reflecting the nesting hierarchy.
     * @param mixed[] $aData Input tree.
     * @param string $sKey The field name. The name of the nested field is formed by
     * the path of the tree the tiers of which are separated by the dot.
     * @return mixed.
     * @throws \extensions\validator\Exception.
     */
    public static function getArrayItemByPointSeparatedKey(array & $aData, $sKey)
    {
        if (strpos($sKey, '.') !== false) {
            preg_match('/([a-zA-Z0-9_\-]+)\.([a-zA-Z0-9_\-\.]+)/', $sKey, $aKey);
            if (!isset($aData[$aKey[1]])) {
                throw new Exception('Undefined index: '.$aKey[1]);
            }
            if (!is_array($aData[$aKey[1]])) {
                throw new Exception("The element indexed {$aKey[1]} isn't an array.");
            }
            return self::getArrayItemByPointSeparatedKey($aData[$aKey[1]],
                    $aKey[2]);
        } else if (isset($aData[$sKey])) {
            return $aData[$sKey];
        } else {
            throw new Exception('Undefined index: '.$sKey);
        }
    }

    /**
     * Метод определяет, содержит ли входное дерево элемент с указанным индексом
     * (индекс содержит точку-разделитель ярусов).
     * @param mixed[] $aData Данные - дерево.
     * @param string $sKey Имя поля. Имя вложенного поля формируется по пути дерева, 
     * ярусы которого разделены точкой.
     * @return boolean.
     */
    public static function isIndexSet(array & $aData, $sKey)
    {
        try {
            self::getArrayItemByPointSeparatedKey($aData, $sKey);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Метод инициализирует валидатор умалчиваемым набором правил (правила для 
     * валидации обязательных полей, строк, чисел) и опций правил.
     */
    private function init()
    {
        $aData = $this->data;

        $this
            // Required value rules
            ->addRule('required',
                function($sFieldName) use ($aData) {
                if (\extensions\validator\Validator::isIndexSet($aData,
                        $sFieldName)) {
                    return true;
                }
                return false;
            },
                function($sFieldName) {
                return "Field '{$sFieldName}' is required.";
            })
            // String rules
            ->addRule('string',
                function($sFieldName, array $aOpt = array()) use ($aData) {
                if (\extensions\validator\Validator::isIndexSet($aData,
                        $sFieldName)) {
                    $mValue = \extensions\validator\Validator::getArrayItemByPointSeparatedKey($aData,
                            $sFieldName);
                    if (!is_string($mValue)) {
                        return false;
                    }
                    if (isset($aOpt['notEmpty'])) {
                        $mTmp = trim($mValue);
                        if ($aOpt['notEmpty'] && empty($mTmp)) {
                            return false;
                        }
                        if (!$aOpt['notEmpty'] && !empty($mTmp)) {
                            return false;
                        }
                    }
                    if (isset($aOpt['pattern'])) {
                        if (!preg_match($aOpt['pattern'], $mValue)) {
                            return false;
                        }
                    }
                    if (isset($aOpt['length'])) {
                        $nLength = mb_strlen($mValue, 'UTF-8');
                        if (isset($aOpt['length']['min'])) {
                            if ($nLength < $aOpt['length']['min']) {
                                return false;
                            }
                        }
                        if (isset($aOpt['length']['max'])) {
                            if ($nLength > $aOpt['length']['max']) {
                                return false;
                            }
                        }
                        if (isset($aOpt['length']['equal'])) {
                            if ($nLength != $aOpt['length']['equal']) {
                                return false;
                            }
                        }
                    }
                }
                return true;
            },
                function($sFieldName, array $aOpt = array()) {
                if (!empty($aOpt)) {
                    $aKeys = array_keys($aOpt);
                    $sKeys = implode(', ', $aKeys);
                    return "Invalid string field '{$sFieldName}' value. Validation options: {$sKeys}";
                }
                return "Invalid string field '{$sFieldName}' value.";
            })
            // Number rules
            ->addRule('number',
                function($sFieldName, array $aOpt = array()) use ($aData) {
                if (\extensions\validator\Validator::isIndexSet($aData,
                        $sFieldName)) {
                    $mValue = \extensions\validator\Validator::getArrayItemByPointSeparatedKey($aData,
                            $sFieldName);
                    if (!is_numeric($mValue)) {
                        return false;
                    }
                    if (isset($aOpt['float'])) {
                        if ($aOpt['float'] && !is_float($mValue)) {
                            return false;
                        }
                        if (!$aOpt['float'] && is_float($mValue)) {
                            return false;
                        }
                    }
                    if (isset($aOpt['integer'])) {
                        if ($aOpt['integer'] && !is_int($mValue)) {
                            return false;
                        }
                        if (!$aOpt['integer'] && is_int($mValue)) {
                            return false;
                        }
                    }
                    if (isset($aOpt['positive'])) {
                        if ($aOpt['positive'] && $mValue <= 0) {
                            return false;
                        }
                        if (!$aOpt['positive'] && $mValue >= 0) {
                            return false;
                        }
                    }
                    if (isset($aOpt['value'])) {
                        if (isset($aOpt['value']['equal'])) {
                            if ($mValue != $aOpt['value']['equal']) {
                                return false;
                            }
                        }
                        if (isset($aOpt['value']['min'])) {
                            if ($mValue < $aOpt['value']['min']) {
                                return false;
                            }
                        }
                        if (isset($aOpt['value']['max'])) {
                            if ($mValue > $aOpt['value']['max']) {
                                return false;
                            }
                        }
                    }
                }
                return true;
            },
                function($sFieldName, array $aOpt = array()) {
                if (!empty($aOpt)) {
                    $aKeys = array_keys($aOpt);
                    $sKeys = implode(', ', $aKeys);
                    return "Invalid number field '{$sFieldName}' value. Validation options: {$sKeys}";
                }
                return "Invalid number field '{$sFieldName}' value.";
            })
            // Array rules
            ->addRule('array',
                function($sFieldName, array $aOpt = array()) use ($aData) {
                if (\extensions\validator\Validator::isIndexSet($aData,
                        $sFieldName)) {
                    $mValue = \extensions\validator\Validator::getArrayItemByPointSeparatedKey($aData,
                            $sFieldName);
                    if (!is_array($mValue)) {
                        return false;
                    }
                    if (isset($aOpt['assoc'])) {
                        $funcIsAssoc = function(array $aArray) {
                            // return false if array is empty
                            return (bool) count(array_filter(array_keys($aArray),
                                        'is_string'));
                        };
                        if ($aOpt['assoc'] && !$funcIsAssoc($mValue)) {
                            return false;
                        }
                        if (!$aOpt['assoc'] && $funcIsAssoc($mValue)) {
                            return false;
                        }
                        unset($funcIsAssoc);
                    }
                    if (isset($aOpt['length'])) {
                        $nLength = count($mValue);
                        if (isset($aOpt['length']['min'])) {
                            if ($nLength < $aOpt['length']['min']) {
                                return false;
                            }
                        }
                        if (isset($aOpt['length']['max'])) {
                            if ($nLength > $aOpt['length']['max']) {
                                return false;
                            }
                        }
                        if (isset($aOpt['length']['equal'])) {
                            if ($nLength != $aOpt['length']['equal']) {
                                return false;
                            }
                        }
                    }
                }
                return true;
            },
                function($sFieldName, array $aOpt = array()) {
                if (!empty($aOpt)) {
                    $aKeys = array_keys($aOpt);
                    $sKeys = implode(', ', $aKeys);
                    return "Invalid array field '{$sFieldName}' value. Validation options: {$sKeys}";
                }
                return "Invalid array field '{$sFieldName}' value.";
            })
            // Boolean rules
            ->addRule('boolean',
                function($sFieldName, array $aOpt = array()) use ($aData) {
                if (\extensions\validator\Validator::isIndexSet($aData,
                        $sFieldName)) {
                    $mValue = \extensions\validator\Validator::getArrayItemByPointSeparatedKey($aData,
                            $sFieldName);
                    if (!is_bool($mValue)) {
                        return false;
                    }
                    if (isset($aOpt['isTrue'])) {
                        if ($aOpt['isTrue'] && !$mValue) {
                            return false;
                        }
                        if (!$aOpt['isTrue'] && $mValue) {
                            return false;
                        }
                    }
                }
                return true;
            },
                function($sFieldName, array $aOpt = array()) {
                if (!empty($aOpt)) {
                    $aKeys = array_keys($aOpt);
                    $sKeys = implode(', ', $aKeys);
                    return "Invalid boolean field '{$sFieldName}' value. Validation options: {$sKeys}";
                }
                return "Invalid boolean field '{$sFieldName}' value.";
            });
    }

    /**
     * Метод добавляет набор полей и соответствующие им правила и опции валидации
     * входных данных.
     * @param array $aItem Набор представляет собой массив, первый элемент которого
     * это строка с именем поля (или массив имен полей), второй элемент - имя
     * правила валидации (всегда строка), третий - опциональный элемент - массив 
     * опций валидации.
     * @return self.
     * @throws \extensions\validator\Exception.
     */
    public function addItem(array $aItem)
    {
        if (count($aItem) < 2) {
            throw new Exception('Invalid count of item elems.');
        }
        $this->items[] = $aItem;
        return $this;
    }

    /**
     * Метод для массовой установки наборов полей и соответствующих им правил и 
     * опций валидации.
     * @param array $aItems Массив наборов.
     * @return self.
     */
    public function setItems(array $aItems)
    {
        foreach ($aItems as $aItem) {
            $this->addItem($aItem);
        }
        return $this;
    }

    /**
     * Метод добавляет правило валидации в стек набора правил валидатора.
     * @param type $sName Имя правила.
     * @param callable $funcCall Функция обратного вызова, определяющая функционал
     * правила валидации данных. Первым параметром в нее передается имя 
     * валидируемого поля, вторым - опциональным - параметром - набор опций валидации,
     * контекстом (замыканием) - входные данные.
     * @param string|callable|null $mErrorMsg Текст сообщения об ошибке валидации,
     * либо функция, вычисляющая текст (параметр опционален).
     * @return self.
     * @throws \extensions\validator\Exception.
     */
    public function addRule($sName, $funcCall, $mErrorMsg = null)
    {
        if (is_callable($funcCall)) {
            $this->rules[$sName] = array($funcCall, $mErrorMsg);
        } else {
            throw new Exception("Rule isn't callable.");
        }
        return $this;
    }

    /**
     * Добавляет текст ошибки в стек ошибок, возникших при валидации.
     * @param string $sMsg.
     */
    protected function addError($sMsg)
    {
        $this->errors[] = $sMsg;
    }

    /**
     * Метод добавляет умалчиваемый текст для формирования описания ошибок
     * валидации.
     * @param string $sFieldName Имя поля.
     * @return string[].
     */
    protected function addDefaultError($sFieldName)
    {
        return $this->addError("Invalid field '{$sFieldName}' value.");
    }

    /**
     * Метод применяет правило валидации к валидируемому полю входных данных.
     * @param string $sFieldName Имя валидируемого поля. Имя вложенного поля
     * формируется по пути дерева, ярусы которого разделены точкой.
     * @param string $sRuleName Имя правила валидации.
     * @param array $aOpt Опции валидации.
     * @throws \extensions\validator\Exception.
     */
    private function _applyRuleToField($sFieldName, $sRuleName,
                                       array $aOpt = array())
    {
        if (!isset($this->rules[$sRuleName])) {
            throw new Exception('Undefined rule name.');
        }
        $funcCall = $this->rules[$sRuleName][0];
        if (!$funcCall($sFieldName, $aOpt)) {
            if (isset($this->rules[$sRuleName][1])) {
                if (is_callable($this->rules[$sRuleName][1])) {
                    // If message entity is function
                    $funcMsg = $this->rules[$sRuleName][1];
                    $this->addError($funcMsg($sFieldName, $aOpt));
                } else {
                    // If message entity is string
                    $this->addError((string) $this->rules[$sRuleName][1]);
                }
            } else {
                // If message entity isn't set
                $this->addDefaultError($sFieldName);
            }
        }
    }

    /**
     * Запускает валидацию данных на исполнение.
     * @return self.
     */
    public function run()
    {
        if (!$this->isRan) {
            $this->isRan = true;
            foreach ($this->items as $aItem) {
                $mOpt      = isset($aItem[2]) ? $aItem[2] : array();
                $sRuleName = $aItem[1];
                foreach (is_array($aItem[0]) ? $aItem[0] : array($aItem[0]) as $sFieldName) {
                    self::_applyRuleToField($sFieldName, $sRuleName, $mOpt);
                }
            }
        }
        return $this;
    }

    /**
     * Метод определяет, прошли ли входные данные валидацию или нет.
     * @return boolean.
     * @throws \extensions\validator\Exception.
     */
    public function isValid()
    {
        if (!$this->isRan) {
            throw new Exception('Validator not ran.');
        }
        return empty($this->errors);
    }

    /**
     * Метод возвращает содержимое стека ошибок валидации входных данных.
     * @return string[] Массив ошибок валидации.
     * @throws \extensions\validator\Exception.
     */
    public function getErrors()
    {
        if (!$this->isRan) {
            throw new Exception('Validator not ran.');
        }
        return $this->errors;
    }
}