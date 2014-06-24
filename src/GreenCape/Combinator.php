<?php

namespace GreenCape\AllPairs;

class Combinator
{
	/** @var Reader */
	private $reader;

	/** @var Writer */
	private $writer;

	/** @var Strategy */
	private $strategy;

	public function __construct(Strategy $strategy, Reader $reader, Writer $writer = null)
	{
		$this->strategy = $strategy;
		$this->reader   = $reader;
		$this->writer   = $writer;
	}

	public function combine()
	{
		$testSets = $this->strategy->combine($this->reader);

		return is_null($this->writer) ? $testSets : $this->writer->write($testSets);
	}
}
