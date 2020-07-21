<?php

namespace GreenCape\AllPairs\Writer;

class VardumpWriter implements WriterInterface
{
    public function write($result)
    {
        /** @noinspection ForgottenDebugOutputInspection */
        print_r($result);

        return $result;
    }
}
