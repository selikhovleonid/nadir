<?php

namespace nadir\core\validator;

use nadir\core\RunnableInterface;

/**
 * This is class for input data validation.
 * @author coon
 */
class Validator implements RunnableInterface
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
     * separated by points and reflecting the nesting hierarchy.
     * @param mixed[] $aData Input tree.
     * @param string $sKey The field name. The name of the nested field is formed by
     * the path of the tree the tiers of which are separated by the point.
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
        } elseif (isset($aData[$sKey])) {
            return $aData[$sKey];
        } else {
            throw new Exception('Undefined index: '.$sKey);
        }
    }

    /**
     * The method checks if the input tree contains an element with the specified
     * index (the index contains a point-separator of tiers)
     * @param mixed[] $aData Input tree.
     * @param string $sKey The field name. The name of the nested field is formed by
     * the path of the tree the tiers of which are separated by the point.
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
     * This method fills the validator with default set of rules (and options of rules)
     * such as rules for validating required fields, strings, numbers, arrays etc.
     */
    private function init()
    {
        $aData = $this->data;

        $this
            // Required value rules
            ->addRule('required',
                function($sFieldName) use ($aData) {
                if (\nadir\extensions\validator\Validator::isIndexSet($aData,
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
                if (\nadir\extensions\validator\Validator::isIndexSet($aData,
                        $sFieldName)) {
                    $mValue = \nadir\extensions\validator\Validator
                        ::getArrayItemByPointSeparatedKey($aData, $sFieldName);
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
                if (\nadir\extensions\validator\Validator::isIndexSet($aData,
                        $sFieldName)) {
                    $mValue = \nadir\extensions\validator\Validator
                        ::getArrayItemByPointSeparatedKey($aData, $sFieldName);
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
                if (\nadir\extensions\validator\Validator::isIndexSet($aData,
                        $sFieldName)) {
                    $mValue = \nadir\extensions\validator\Validator
                        ::getArrayItemByPointSeparatedKey($aData, $sFieldName);
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
                if (\nadir\extensions\validator\Validator::isIndexSet($aData,
                        $sFieldName)) {
                    $mValue = \nadir\extensions\validator\Validator
                        ::getArrayItemByPointSeparatedKey($aData, $sFieldName);
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
     * The method adds a set of fields and their corresponding rules and parameters
     * for validating the input data
     * @param array $aItem This set is an array whose first element is a string
     * with a field name (or an array of field names), the second element is the
     * name of the validation rule (always a string), the third element is an
     * optional array of validation options.
     * @return self.
     * @throws \extensions\validator\Exception.
     */
    public function addItem(array $aItem)
    {
        if (count($aItem) < 2) {
            throw new Exception('Invalid count of item elements.');
        }
        $this->items[] = $aItem;
        return $this;
    }

    /**
     * This is mass analog for addItem() method.
     * @param array $aItems The input array of sets.
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
     * The method adds a validation rule to the stack of validator rulesets.
     * @param type $sName The name of rule.
     * @param callable $funcCall The callback function that defines the functional
     * of the data validation rule. The first parameter is the name of the validated
     * field, the second optional parameter is the set of validation options,
     * and the context (closure) is the input data.
     * @param string|callable|null $mErrorMsg The error message or callable function
     * which generates this message. This parameter is optional.
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
     * The method adds the error message to the error stack which occurred during
     * validation.
     * @param string $sMsg.
     */
    protected function addError($sMsg)
    {
        $this->errors[] = $sMsg;
    }

    /**
     * The method adds default message to form a description of the validation
     * errors.
     * @param string $sFieldName The field name.
     * @return string[].
     */
    protected function addDefaultError($sFieldName)
    {
        return $this->addError("Invalid field '{$sFieldName}' value.");
    }

    /**
     * The method applies the validation rule to the validable field.
     * @param string $sFieldName The field name. The name of the nested field is
     * formed by the path of the tree the tiers of which are separated by the point.
     * @param string $sRuleName The validation rule name.
     * @param array $aOpt The validation options.
     * @throws \extensions\validator\Exception.
     */
    private function applyRuleToField($sFieldName, $sRuleName,
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
     * The main executable method.
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
                    self::applyRuleToField($sFieldName, $sRuleName, $mOpt);
                }
            }
        }
        return $this;
    }

    /**
     * It checks if processed input data is valid or not.
     * @return boolean.
     * @throws \extensions\validator\Exception.
     */
    public function isValid()
    {
        if (!$this->isRan) {
            throw new Exception("The validation wasn't ran.");
        }
        return empty($this->errors);
    }

    /**
     * The method returns the contents of the stack of validation errors of
     * input data.
     * @return string[] The array of validation errors.
     * @throws \extensions\validator\Exception.
     */
    public function getErrors()
    {
        if (!$this->isRan) {
            throw new Exception("The validation wasn't ran.");
        }
        return $this->errors;
    }
}