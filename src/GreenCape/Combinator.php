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

		// Compute subsets
		$subSets = array();
		foreach ($subModules as $moduleIndex => $module)
		{
			$moduleLabel            = uniqid('Module');
			$moduleId[$moduleLabel] = $moduleIndex;
			$subParameters          = array();
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

		// Generate testsets
		$testSets = $this->strategy->combine(array_values($parameters), 2);

		if (!empty($moduleId))
		{
			// Resolve subsets
			$resolvedSets = array();
			foreach ($testSets as $set)
			{
				$resolved = array();
				foreach ($set as $label => $value)
				{
					if (isset($moduleId[$label]))
					{
						$resolved += $subSets[$moduleId[$label]][$value];
					}
				}
				$resolvedSets[] = $resolved;
			}
			$testSets = $resolvedSets;
		}

		// Reduce
		$labels = array_keys($testSets[0]);
		print("Labels: " . print_r($labels, true));

		for ($i = 1; $i < count($testSets); ++$i)
		{
			$remove = true;
			$debug  = "Set $i: {" . implode(', ', $testSets[$i]) . "}:\n";;
			for ($x = 0; $x < count($labels) - 1; ++$x)
			{
				for ($y = $x + 1; $y < count($labels); ++$y)
				{
					$found = false;
					for ($j = 0; $j < $i; ++$j)
					{
						if (!isset($testSets[$j]))
						{
							continue;
						}
						if ($testSets[$i][$labels[$x]] == $testSets[$j][$labels[$x]] && $testSets[$i][$labels[$y]] == $testSets[$j][$labels[$y]])
						{
							$debug .= "{" . $testSets[$i][$labels[$x]] . ", " . $testSets[$i][$labels[$y]] . "} found in set $j\n";
							$found = true;
							break;
						}
					}
					if (!$found)
					{
						$remove = false;
						break 2;
					}
				}
			}
			if ($remove)
			{
				// All pairs were found, remove testSet
				print($debug);
				unset($testSets[$i]);
			}
		}

		return is_null($this->writer) ? $testSets : $this->writer->write($testSets);
	}
}
