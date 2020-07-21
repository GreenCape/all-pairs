<?php

namespace GreenCape\AllPairs\Reader;

use GreenCape\AllPairs\Parameter;

interface ReaderInterface
{
    /**
     * @param  string  $labelDelimiter
     * @param  string  $valueDelimiter
     *
     * @return Parameter[]
     */
    public function getParameters($labelDelimiter = ':', $valueDelimiter = ',');

    public function getSubModules();

    public function getConstraints();
}
