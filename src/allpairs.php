<?php

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';

class Array2D
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

class AllPairs
{
	/** @var \GreenCape\AllPairs\Reader */
	private $reader;

	/** @var \GreenCape\AllPairs\Writer */
	private $writer;

	/** @var \GreenCape\AllPairs\Strategy */
	private $strategy;

	public function __construct(GreenCape\AllPairs\Strategy $strategy, GreenCape\AllPairs\Reader $reader, GreenCape\AllPairs\Writer $writer)
	{
		$this->strategy = $strategy;
		$this->reader   = $reader;
		$this->writer   = $writer;
	}

	public function execute()
	{
		return $this->writer->write($this->strategy->combine($this->reader->getParameters()));
	}
}

$base = '..';
/** @var  string $file */
// $file = $base . '/original/testData.txt';
$file = $base . '/tests/data/server.txt';
// $file = $base . '/tests/data/prime.txt';
// $file = $base . '/tests/data/big.txt';

print("\nBegin pair-wise test set generation\n\n");

$app    = new AllPairs(
	new GreenCape\AllPairs\QictStrategy(),
	new GreenCape\AllPairs\FileReader($file),
	new GreenCape\AllPairs\ConsoleWriter()
);
$result = $app->execute();

// Display results
print("\nGenerated " . count($result) . " test sets.\n");
