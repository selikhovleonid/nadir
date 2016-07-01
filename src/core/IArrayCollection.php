<?php

namespace core;

/**
 * This's the array collection interface.
 * @author coon
 */
interface IArrayCollection {

    /**
     * It adds an item to the collection.
     * @param string $sKey The item name.
     * @param mixed $mValue The item value.
     */
    public function add($sKey, $mValue);

    /**
     * It adds the array of items to the collection. 
     * @param array $aPairs The name-value pairs array.
     */
    public function addAll(array $aPairs);

    /**
     * It returns the keys of the collection (the iterator analog). 
     */
    public function getKeys();

    /**
     * It returns the item of the collection by key.
     * @param string $sKey
     */
    public function get($sKey);

    /**
     * It returns all elements of the collection.
     */
    public function getAll();

    /**
     * It removes the collection item by key.
     * @param string $sKey
     */
    public function remove($sKey);

    /**
     * It removes all items of the collection.
     */
    public function removeAll();

    /**
     * It checks if the collection contains the element with passed key.
     * @param string $sKey The item name.
     */
    public function contains($sKey);

    /**
     * It checks if the collection is empty.
     */
    public function isEmpty();

    /**
     * It returns the size of the collection.
     */
    public function size();
}
