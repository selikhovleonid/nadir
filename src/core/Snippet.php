<?php

namespace core;

/**
 * Экземпляром класса является сниппет - атомарный элемент представления.
 * @author coon
 */
class Snippet extends AView {

    /**
     * {@inheritdoc}
     */
    public function render() {
        include $this->filePath;
    }

}
