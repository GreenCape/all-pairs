<?php

namespace GreenCape\AllPairs;

class Parameter implements \ArrayAccess, \Countable
{
	private $label;

	private $values;

	public function __construct(array $values, $label = null)
	{
		if (is_null($label))
		{
			$label = uniqid();
		}
		$this->label = $label;
		$this->values = array_values($values);
	}

	public function getLabel()
	{
		return $this->label;
	}

	/*
	 *  ArrayAccess Interface
	 */

	/**
	 * Whether a offset exists
	 *
	 * @param mixed $offset An offset to check for.
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
	 * @param mixed $offset The offset to retrieve.
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
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value  The value to set.
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
	 * @param mixed $offset The offset to unset.
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
}
