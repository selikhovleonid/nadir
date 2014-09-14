<?php

/**
 * Интерфейс коллекций массивов.
 * @author coon
 */

namespace core;

interface IArrayCollection {

    /**
     * Добавляет элемент в коллекцию.
     * @param string $sKey.
     * @param mixed $mValue.
     */
    public function add($sKey, $mValue);

    /**
     * Добавляет массив элементов в коллекцию.
     * @param array $aPairs.
     */
    public function addAll(array $aPairs);

    /**
     * Возвращает ключи коллекции (аналог итератора).
     */
    public function getKeys();

    /**
     * Возвращает элемент коллекции по ключу.
     * @param string $sKey
     */
    public function get($sKey);

    /**
     * Возвращает все элементы коллекции.
     */
    public function getAll();

    /**
     * Удаляет элемент коллекции по ключу.
     * @param string $sKey
     */
    public function remove($sKey);

    /**
     * Удаляет все элементы коллекции.
     */
    public function removeAll();

    /**
     * Определяет, содержится ли в коллекции элемент с данным ключом.
     * @param string $sKey.
     */
    public function contains($sKey);

    /**
     * Определяет, является ли коллекция пустой.
     */
    public function isEmpty();

    /**
     * Возвращает количество элементов в коллекции.
     */
    public function size();
}
