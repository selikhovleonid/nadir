<?php

namespace core;

/**
 * Класс композитного представления (представления в широком смысле), экземпляры
 * которого могут содержать атомарные единицы представления - сниппеты.
 * @author coon
 */
abstract class ACompositeView extends AView {

    /** @var \core\Snippet[] Карта сниппетов представления. */
    protected $snippets = array();

    /**
     * Добавляет сниппет в объект представления.
     * @param string $sSnptName
     */
    public function addSnippet($sSnptName) {
        $oSnpt                      = ViewFactory::createSnippet($sSnptName);
        $this->snippets[$sSnptName] = $oSnpt;
    }

    /**
     * Возвращает объект связанного сниппета по имени. Если имя не указано, то
     * возвращается вся карта связанных со View сниппетов.
     * @param string $sSnptName
     * @return \core\Snippet|\core\Snippet[]|null
     */
    public function getSnippet($sSnptName = '') {
        if (empty($sSnptName)) {
            return $this->snippets;
        } else {
            return isset($this->snippets[$sSnptName]) 
                ? $this->snippets[$sSnptName] 
                : NULL;
        }
    }

    /**
     * Метод возвращает карту связанных со View сниппетов.
     * @return \core\Snippet[]
     */
    public function getAllSnippets() {
        return $this->getSnippet();
    }

}
