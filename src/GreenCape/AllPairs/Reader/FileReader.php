<?php

namespace GreenCape\AllPairs\Reader;

class FileReader extends StringReader
{
    public function __construct($file)
    {
        parent::__construct(file_get_contents($file));
    }
}
