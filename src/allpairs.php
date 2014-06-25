<?php

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';

$base = '..';
/** @var  string $file */
// $file = $base . '/original/testData.txt';
$file = $base . '/tests/data/server.txt';
// $file = $base . '/tests/data/prime.txt';
// $file = $base . '/tests/data/big.txt';

print("\nBegin pair-wise test set generation\n\n");

$allPairs    = new GreenCape\AllPairs\Combinator(
	new GreenCape\AllPairs\QictStrategy(),
	new GreenCape\AllPairs\FileReader($file),
	new GreenCape\AllPairs\ConsoleWriter()
);
$result = $allPairs->combine();

// Display results
print("\nGenerated " . count($result) . " test sets.\n");
