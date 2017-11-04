<?php

namespace core;

/**
 * This's composite class of the View (the view in a broad sense), which 
 * entities may contains the atomic view units - the snippets.
 * @author coon
 */
abstract class AbstractCompositeView extends AView
{
    /** @var \core\Snippet[] The snippet map. */
    protected $snippets = array();

    /**
     * It adds snippet to the view object.
     * @param string $sSnptName
     */
    public function addSnippet($sSnptName)
    {
        $oSnpt                      = ViewFactory::createSnippet($sSnptName);
        $this->snippets[$sSnptName] = $oSnpt;
    }

    /**
     * It returns assigned snippet object by the name. If the name not presents, 
     * then it returns the map with all View-assigned snippets.
     * @param string $sSnptName
     * @return \core\Snippet|\core\Snippet[]|null
     */
    public function getSnippet($sSnptName = '')
    {
        if (empty($sSnptName)) {
            return $this->snippets;
        } else {
            return isset($this->snippets[$sSnptName]) ? $this->snippets[$sSnptName]
                    : null;
        }
    }

    /**
     * The method returns the View-assigned snippet map.
     * @return \core\Snippet[]
     */
    public function getAllSnippets()
    {
        return $this->getSnippet();
    }
}