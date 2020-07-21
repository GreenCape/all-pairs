<?php

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';

$base  = '..';
$files = array(
    '/tests/data/server.txt',
    '/original/testData.txt',
    '/tests/data/prime.txt',
    '/tests/data/volume.txt',
    '/tests/data/hardware.txt',
    '/tests/data/big.txt',
    '/tests/data/joomla.txt',
);

foreach ($files as $file) {
    print("\nBegin pair-wise test set generation for {$file}\n\n");
    $time     = microtime(true);
    $allPairs = new GreenCape\AllPairs\Combinator(new GreenCape\AllPairs\DefaultStrategy(),
        new GreenCape\AllPairs\FileReader($base . $file),
        new GreenCape\AllPairs\ConsoleWriter());
    $result   = $allPairs->combine();
    $time     = microtime(true) - $time;

    // Display results
    print("\nGenerated " . count($result) . " test sets in {$time} seconds.\n");
    unset($allPairs, $result, $time);
}
