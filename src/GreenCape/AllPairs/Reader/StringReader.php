<?php

namespace GreenCape\AllPairs;

class StringReader implements Reader
{
	private $fileContent = array();

	public function __construct($content)
	{
		$this->parse($content);
	}

	private function parse($content)
	{
		$this->fileContent = preg_split('~[\r\n]+~', $content);
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
		foreach ($this->fileContent as $line)
		{
			$line = trim($line);
			if (empty($line) || $line[0] == '#')
			{
				continue;
			}
			$lineTokens = explode($labelDelimiter, $line, 2);
			if (count($lineTokens) != 2)
			{
				continue;
			}
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
		return null;
	}

	public function getConstraints()
	{
		return null;
	}
}
