<?php

namespace core;

/**
 * The instance of the class is Snippet - an atomic element of the view.
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
