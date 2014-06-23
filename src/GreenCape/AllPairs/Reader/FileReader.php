<?php

namespace GreenCape\AllPairs;

class FileReader implements Reader
{
	private $file;

	public function __construct($file)
	{
		$this->file = $file;
	}

	public function getParameters()
	{
		$parameterDefinition = array();
		foreach (file($this->file) as $line)
		{
			$line = trim($line);
			if (empty($line) || $line[0] == '#')
			{
				continue;
			}
			$lineTokens = explode(':', $line);
			$strValues  = explode(',', $lineTokens[1]);
			for ($i = 0; $i < count($strValues); ++$i)
			{
				$strValues[$i] = trim($strValues[$i]);
			}
			$parameterDefinition[trim($lineTokens[0])] = $strValues;
		}

		return $parameterDefinition;
	}

	public function getSubModules()
	{
		throw new \RuntimeException('Not implemented');
	}

	public function getConstraints()
	{
		throw new \RuntimeException('Not implemented');
	}
}
