<?php

namespace GreenCape\AllPairs;

use ArrayAccess;
use Countable;
use Iterator;

class Parameter implements ArrayAccess, Countable, Iterator
{
    private $label;

    private $values;

    private $current = 0;

    public function __construct(array $values, $label = null)
    {
        if (is_null($label)) {
            $label = uniqid('', true);
        }
        
        $this->label  = $label;
        $this->values = array_values($values);
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getValues()
    {
        return $this->values;
    }

    /*
     *  ArrayAccess Interface
     */

    /**
     * Whether a offset exists
     *
     * @param  mixed  $offset  An offset to check for.
     *
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return (array_key_exists($offset, $this->values));
    }

    /**
     * Offset to retrieve
     *
     * @param  mixed  $offset  The offset to retrieve.
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }

    /**
     * Offset to set
     *
     * @param  mixed  $offset  The offset to assign the value to.
     * @param  mixed  $value   The value to set.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @param  mixed  $offset  The offset to unset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    /*
     * Countable Interface
     */

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return count($this->values);
    }

    /*
     * Iterator Interface
     */

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->values[$this->current];
    }

    /**
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
        $this->current++;
    }

    /**
     * Return the key of the current element
     *
     * @return int
     */
    public function key()
    {
        return $this->current;
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     *                 Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->values[$this->current]);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void
     */
    public function rewind()
    {
        $this->current = 0;
    }
}
