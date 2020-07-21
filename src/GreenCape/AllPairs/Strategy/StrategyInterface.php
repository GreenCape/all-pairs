<?php

namespace GreenCape\AllPairs\Strategy;

use GreenCape\AllPairs\Parameter;

interface StrategyInterface
{
    /**
     * @param  Parameter[]  $parameterList
     * @param  int          $order
     *
     * @return array
     */
    public function combine($parameterList, $order = 2);
}
