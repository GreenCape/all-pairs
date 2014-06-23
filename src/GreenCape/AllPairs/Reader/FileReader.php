<?php

namespace GreenCape\AllPairs;

class FileReader implements Reader
{
	private $file;

	public function __construct($file)
	{
		$this->file = $file;
	}

	/**
	 * @param string $labelDelimiter
	 * @param string $valueDelimiter
	 *
	 * @return Parameter[]
	 */
	public function getParameters($labelDelimiter = ':', $valueDelimiter = ',')
	{
		$parameterDefinition = array();
		foreach (file($this->file) as $line)
		{
			$line = trim($line);
			if (empty($line) || $line[0] == '#')
			{
				continue;
			}
			$lineTokens = explode($labelDelimiter, $line, 2);
			$values  = explode($valueDelimiter, $lineTokens[1]);
			for ($i = 0; $i < count($values); ++$i)
			{
				$values[$i] = trim($values[$i]);
			}
			$parameterDefinition[] = new Parameter($values, trim($lineTokens[0]));
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
