<?php

namespace GreenCape\AllPairs;

use GreenCape\AllPairs\Reader\ReaderInterface;
use GreenCape\AllPairs\Strategy\StrategyInterface;
use GreenCape\AllPairs\Writer\WriterInterface;

class Combinator
{
    /** @var ReaderInterface */
    private $reader;

    /** @var WriterInterface */
    private $writer;

    /** @var StrategyInterface */
    private $strategy;

    public function __construct(StrategyInterface $strategy, ReaderInterface $reader, WriterInterface $writer = null)
    {
        $this->strategy = $strategy;
        $this->reader   = $reader;
        $this->writer   = $writer;
    }

    public function combine()
    {
        $parameters       = $this->reader->getParameters();
        $subModules       = $this->reader->getSubModules();
        $moduleId         = array();

        // Compute subsets
        $subSets = array();

        foreach ($subModules as $moduleIndex => $module) {
            $moduleLabel            = uniqid('Module', true);
            $moduleId[$moduleLabel] = $moduleIndex;
            $subParameters          = array();

            foreach ($parameters as $parameterIndex => $parameter) {
                if (in_array($parameter->getLabel(), $module['parameters'], true)) {
                    $subParameters[]                  = $parameter;
                    unset($parameters[$parameterIndex]);
                }
            }
            $subSet                = $this->strategy->combine($subParameters, $module['order']);
            $subSets[$moduleIndex] = $subSet;
            $parameters[]          = new Parameter(array_keys($subSet), $moduleLabel);
        }

        // Generate testsets
        $testSets = $this->strategy->combine(array_values($parameters), 2);

        if (!empty($moduleId)) {
            // Resolve subsets
            $resolvedSets = array();

            foreach ($testSets as $set) {
                $resolved = array();

                foreach ($set as $label => $value) {
                    if (isset($moduleId[$label])) {
                        $resolved += $subSets[$moduleId[$label]][$value];
                    }
                }

                $resolvedSets[] = $resolved;
            }

            $testSets = $resolvedSets;
        }

        // Reduce
        $labels = array_keys($testSets[0]);

        for ($i = 1, $iMax = count($testSets); $i < $iMax; ++$i) {
            $remove = true;

            for ($x = 0; $x < count($labels) - 1; ++$x) {
                for ($y = $x + 1, $yMax = count($labels); $y < $yMax; ++$y) {
                    $found = false;
                    for ($j = 0; $j < $i; ++$j) {
                        if (!isset($testSets[$j])) {
                            continue;
                        }
                        if ($testSets[$i][$labels[$x]] === $testSets[$j][$labels[$x]] && $testSets[$i][$labels[$y]] === $testSets[$j][$labels[$y]]) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $remove = false;
                        break 2;
                    }
                }
            }
            if ($remove) {
                // All pairs were found, remove testSet
                unset($testSets[$i]);
            }
        }

        return is_null($this->writer) ? $testSets : $this->writer->write($testSets);
    }
}
