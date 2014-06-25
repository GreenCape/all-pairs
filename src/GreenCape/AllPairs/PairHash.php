<?php

namespace GreenCape\AllPairs;

class PairHash
{
	private $data = array();

	public function set($i, $j, $value)
	{
		@$this->data[min($i, $j)][max($i, $j)] = $value;
	}

	public function get($i, $j)
	{
		if (!isset($this->data[min($i, $j)]))
		{
			throw new \OutOfBoundsException();
		}
		if (!isset($this->data[min($i, $j)][max($i, $j)]))
		{
			throw new \OutOfBoundsException();
		}

		return $this->data[min($i, $j)][max($i, $j)];
	}
}
