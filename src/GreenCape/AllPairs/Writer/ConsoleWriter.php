<?php

namespace GreenCape\AllPairs;

class ConsoleWriter implements  Writer
{
	protected $fieldSeparator = "\t";
	protected $lineSeparator = "\n";

	public function write($result)
	{
		print(implode($this->fieldSeparator, array_keys($result[0])) . $this->lineSeparator);
		foreach ($result as $set)
		{
			print(implode($this->fieldSeparator, $set) . $this->lineSeparator);
		}

		return $result;
	}
}
