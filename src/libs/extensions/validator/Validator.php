<?php

/**
 * Класс-валидатор входных данных.
 * @author coon
 */

namespace extensions\validator;

class Validator implements \core\IRunnable {

    /** @var mixed[] Входные данные для валидации. */
    protected $data = NULL;

    /** @var array Набор полей и соответствующих им правил для валидации. */
    protected $items = array();

    /** @var array Набор правил валидатора. */
    protected $rules = array();

    /** @var array Стек ошибок данных, возникших в ходе валидации. */
    protected $errors = array();

    /** @var boolean Флаг равен TRUE, когда валидация данных проведена. */
    protected $isRan = FALSE;

    /**
     * Конструктор инициализирует валидатор а также устанавливает входные данные
     * для валидации.
     * @param mied[] $aData Валидируемые данные.
     */
    public function __construct($aData) {
        if (is_array($aData)) {
            $this->data = $aData;
            $this->_init();
        } else {
            $this->isRan = TRUE;
            $this->addError('Invalid data set format.');
        }
    }

    /**
     * Метод возвращает элемент дерева по ключу, который формируется как строка,
     * разделенная точками и отражающая иерархию вложенности.
     * @param mixed[] $aData Данные - дерево.
     * @param string $sKey Имя поля. Имя вложенного поля формируется по пути дерева, 
     * ярусы которого разделены точкой.
     * @return mixed.
     * @throws \extensions\validator\Exception.
     */
    public static function getArrayItemByPointSeparatedKey(array $aData, $sKey) {
        if (preg_match('/\./', $sKey)) {
            preg_match('/([a-zA-Z0-9_\-]+)\.([a-zA-Z0-9_\-\.]+)/', $sKey, $aKey);
            return self::getArrayItemByPointSeparatedKey($aData[$aKey[1]], $aKey[2]);
        } else if (isset($aData[$sKey])) {
            return $aData[$sKey];
        } else {
            throw new Exception('Undefined index: ' . $sKey);
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
    public static function isIndexSet(array $aData, $sKey) {
        try {
            self::getArrayItemByPointSeparatedKey($aData, $sKey);
            return TRUE;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * Метод инициализирует валидатор умалчиваемым набором правил (правила для 
     * валидации обязательных полей, строк, чисел) и опций правил.
     */
    private function _init() {
        $aData = $this->data;

        $this
                // Required value rules
                ->addRule('required', function($sFieldName) use ($aData) {
                    if (Validator::isIndexSet($aData, $sFieldName)) {
                        return TRUE;
                    }
                    return FALSE;
                }, function($sFieldName) {
                    return "Field '{$sFieldName}' is required.";
                })
                // String rules
                ->addRule('string', function($sFieldName, array $aOpt = array()) use ($aData) {
                    if (\extensions\validator\Validator::isIndexSet($aData, $sFieldName)) {
                        $mValue = \extensions\validator\Validator::getArrayItemByPointSeparatedKey($aData, $sFieldName);
                        if (!is_string($mValue)) {
                            return FALSE;
                        }
                        if (isset($aOpt['notEmpty'])) {
                            $mTmp = trim($mValue);
                            if ($aOpt['notEmpty'] && empty($mTmp)) {
                                return FALSE;
                            }
                            if (!$aOpt['notEmpty'] && !empty($mTmp)) {
                                return FALSE;
                            }
                        }
                        if (isset($aOpt['pattern'])) {
                            if (!preg_match($aOpt['pattern'], $mValue)) {
                                return FALSE;
                            }
                        }
                        if (isset($aOpt['length'])) {
                            $nLength = mb_strlen($mValue, 'UTF-8');
                            if (isset($aOpt['length']['min'])) {
                                if ($nLength < $aOpt['length']['min']) {
                                    return FALSE;
                                }
                            }
                            if (isset($aOpt['length']['max'])) {
                                if ($nLength > $aOpt['length']['max']) {
                                    return FALSE;
                                }
                            }
                            if (isset($aOpt['length']['equal'])) {
                                if ($nLength != $aOpt['length']['equal']) {
                                    return FALSE;
                                }
                            }
                        }
                    }
                    return TRUE;
                })
                // Number rules
                ->addRule('number', function($sFieldName, array $aOpt = array()) use ($aData) {
                    if (Validator::isIndexSet($aData, $sFieldName)) {
                        $mValue = \extensions\validator\Validator::getArrayItemByPointSeparatedKey($aData, $sFieldName);
                        if (!is_numeric($mValue)) {
                            return FALSE;
                        }
                        if (isset($aOpt['float'])) {
                            if ($aOpt['float'] && !is_float($mValue)) {
                                return FALSE;
                            }
                            if (!$aOpt['float'] && is_float($mValue)) {
                                return FALSE;
                            }
                        }
                        if (isset($aOpt['integer'])) {
                            if ($aOpt['integer'] && !is_int($mValue)) {
                                return FALSE;
                            }
                            if (!$aOpt['integer'] && is_int($mValue)) {
                                return FALSE;
                            }
                        }
                        if (isset($aOpt['positive'])) {
                            if ($aOpt['positive'] && $mValue <= 0) {
                                return FALSE;
                            }
                            if (!$aOpt['positive'] && $mValue >= 0) {
                                return FALSE;
                            }
                        }
                        if (isset($aOpt['value'])) {
                            if (isset($aOpt['value']['equal'])) {
                                if ($mValue != $aOpt['value']['equal']) {
                                    return FALSE;
                                }
                            }
                            if (isset($aOpt['value']['min'])) {
                                if ($mValue < $aOpt['value']['min']) {
                                    return FALSE;
                                }
                            }
                            if (isset($aOpt['value']['max'])) {
                                if ($mValue > $aOpt['value']['max']) {
                                    return FALSE;
                                }
                            }
                        }
                    }
                    return TRUE;
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
    public function addItem(array $aItem) {
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
    public function setItems(array $aItems) {
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
    public function addRule($sName, $funcCall, $mErrorMsg = NULL) {
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
    protected function addError($sMsg) {
        $this->errors[] = $sMsg;
    }

    /**
     * Метод добавляет умалчиваемый текст для формирования описания ошибок
     * валидации.
     * @param string $sFieldName Имя поля.
     * @return string[].
     */
    protected function addDefaultError($sFieldName) {
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
    private function _applyRuleToField($sFieldName, $sRuleName, array $aOpt = array()) {
        if (!isset($this->rules[$sRuleName])) {
            throw new Exception('Undefined rule name.');
        }
        $funcCall = $this->rules[$sRuleName][0];
        if (!$funcCall($sFieldName, $aOpt)) {
            if (isset($this->rules[$sRuleName][1])) {
                if (is_callable($this->rules[$sRuleName][1])) {
                    // If message entity is function
                    $funcMsg = $this->rules[$sRuleName][1];
                    $this->addError($funcMsg($sFieldName));
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
    public function run() {
        if (!$this->isRan) {
            $this->isRan = TRUE;
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
    public function isValid() {
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
    public function getErrors() {
        if (!$this->isRan) {
            throw new Exception('Validator not ran.');
        }
        return $this->errors;
    }

}
