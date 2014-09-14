<?php

/**
 * Класс представления.
 * @property mixed $name Переменная для передачи пользовательских данных из 
 * контроллера в файл представления.
 * @author coon.
 */

namespace core;

class View extends AView {

    /**
     * {@inheritdoc}
     */
    public function render() {
        include $this->filePath;
    }

}

