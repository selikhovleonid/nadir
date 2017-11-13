<?php

namespace nadir\core;

/**
 * This is a view abstract class.
 * @author Leonid Selikhov
 */
abstract class AbstractView extends AbstractUserPropAccessor
{
    /** @var string The path to the file with view markup. */
    protected $filePath = '';

    /**
     * The constructor inits private object properties.
     * @param string $sViewFilePath The path to the file with view markup.
     * @return self.
     */
    public function __construct($sViewFilePath)
    {
        $this->setFilePath($sViewFilePath);
    }

    /**
     * This is method-accessor to the variable which contains the path to the file 
     * with view markup.
     * @return string.
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * It assigns the object with view file.
     * @param string $sViewFilePath The path to the file with view markup.
     * @throws Exception It throws if file isn't readable.
     */
    public function setFilePath($sViewFilePath)
    {
        if (!is_readable($sViewFilePath)) {
            throw new Exception("The View file {$sViewFilePath} isn't readable.");
        }
        $this->filePath = $sViewFilePath;
    }

    /**
     * The method provides massive assignment user's variables of the class.
     * @param array $aData The users's variables of the class.
     */
    public function setVariables(array $aData)
    {
        foreach ($aData as $sKey => $mValue) {
            $this->$sKey = $mValue;
        }
    }

    /**
     * It's an abstract method which renders the file of view.
     * @return void.
     */
    abstract public function render();
}