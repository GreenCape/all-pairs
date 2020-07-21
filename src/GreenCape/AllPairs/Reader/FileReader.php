<?php

namespace GreenCape\AllPairs;

class FileReader extends StringReader
{
    public function __construct($file)
    {
        parent::__construct(file_get_contents($file));
    }
}
