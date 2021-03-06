<?php

namespace GreenCape\AllPairs;

use OutOfBoundsException;

class PairHash
{
    private $data = array();

    public function set($i, $j, $value)
    {
        if (!isset($this->data[min($i, $j)])) {
            $this->data[min($i, $j)] = array();
        }
        $this->data[min($i, $j)][max($i, $j)] = $value;
    }

    public function get($i, $j)
    {
        if (!isset($this->data[min($i, $j)])) {
            throw new OutOfBoundsException("No value for [$i,$j]");
        }
        
        if (!isset($this->data[min($i, $j)][max($i, $j)])) {
            throw new OutOfBoundsException("No value for [$i,$j]");
        }

        return $this->data[min($i, $j)][max($i, $j)];
    }
}
