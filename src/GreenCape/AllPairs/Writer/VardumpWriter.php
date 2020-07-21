<?php

namespace GreenCape\AllPairs;

class VardumpWriter implements Writer
{
    public function write($result)
    {
        /** @noinspection ForgottenDebugOutputInspection */
        print_r($result);

        return $result;
    }
}
