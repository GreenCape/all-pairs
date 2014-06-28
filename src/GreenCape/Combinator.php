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
		$parameters       = $this->reader->getParameters();
		$subModules       = $this->reader->getSubModules();
		$moduleParameters = array();
		$moduleId         = array();

		$subSets = array();
		foreach ($subModules as $moduleIndex => $module)
		{
			$moduleLabel            = uniqid('Module');
			$moduleId[$moduleLabel] = $moduleIndex;
			$subParameters = array();
			foreach ($parameters as $parameterIndex => $parameter)
			{
				if (in_array($parameter->getLabel(), $module['parameters']))
				{
					$subParameters[]                  = $parameter;
					$moduleParameters[$moduleIndex][] = $parameter;
					unset($parameters[$parameterIndex]);
				}
			}
			$subSet                = $this->strategy->combine($subParameters, $module['order']);
			$subSets[$moduleIndex] = $subSet;
			$parameters[]          = new Parameter(array_keys($subSet), $moduleLabel);
		}
		$testSets = array();
		foreach ($this->strategy->combine(array_values($parameters), 2) as $set)
		{
			$resolved = array();
			foreach ($set as $label => $value)
			{
				if (isset($moduleId[$label]))
				{
					$resolved += $subSets[$moduleId[$label]][$value];
				}
			}
			$testSets[] = $resolved;
		}

		return is_null($this->writer) ? $testSets : $this->writer->write($testSets);
	}
}
