<?php

namespace nadir\core;

/**
 * The instance of the class is Snippet - an atomic element of the view.
 * @author Leonid Selikhov
 */
class Snippet extends AbstractView
{

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        include $this->filePath;
    }
}
