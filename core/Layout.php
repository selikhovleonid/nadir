<?php

/**
 * Класс макета.
 * @property mixed $name Переменная для передачи пользовательских данных из 
 * контроллера в файл макета.
 * @author coon
 */

namespace core;

class Layout extends ACompositeView {

    /** @var \core\View Объект представления. */
    public $view = NULL;

    /**
     * Связывает объект с файлом макета и косвенно,через объект представления, с
     * файлом разметки представления.
     * @param string $sLayoutFilePath Путь к файлу с разметкой макета.
     * @param \core\View|null $oView Объект представления.
     */
    public function __construct($sLayoutFilePath, View $oView) {
        parent::__construct($sLayoutFilePath);
        $this->view = $oView;
    }

    /**
     * {@inheritdoc}
     */
    public function render() {
        include $this->filePath;
    }

}
