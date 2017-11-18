<?php

namespace nadir\core;

/**
 * This is the class of layout.
 * @property mixed $name The variable for passing custom data from the controller
 * to the layout file.
 * @author Leonid Selikhov
 */
class Layout extends AbstractCompositeView
{
    /** @var \nadir\core\View The view object. */
    public $view = null;

    /**
     * It assigns the oblect of current class with the file of Layout and indirectly
     * (through the View object) with the file of view markup.
     * @param string $sLayoutFilePath The path to the file with the layout markup.
     * @param \nadir\core\View|null $oView The object of view.
     */
    public function __construct($sLayoutFilePath, View $oView)
    {
        parent::__construct($sLayoutFilePath);
        $this->view = $oView;
    }

    /**
     * The method returns the view object binded with the layout.
     * @return \nadir\core\View|null
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        include $this->filePath;
    }
}
