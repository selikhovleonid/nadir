<?php

namespace core;

/**
 * This's а class of the View (the view in a strictly).
 * @property mixed $name The variable for a passing custom data from a controller 
 * to the view file.
 * @author coon.
 */
class View extends ACompositeView {

    /**
     * {@inheritdoc}
     */
    public function render() {
        include $this->filePath;
    }

}
