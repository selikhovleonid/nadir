<?php

namespace nadir\core;

/**
 * This is а class of the View (the view in a strictly).
 * @property mixed $name The variable for a passing custom data from a controller
 * to the view file.
 * @author Leonid Selikhov.
 */
class View extends AbstractCompositeView
{

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        include $this->filePath;
    }
}
