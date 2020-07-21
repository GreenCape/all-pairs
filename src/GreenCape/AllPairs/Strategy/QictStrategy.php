<?php

namespace GreenCape\AllPairs\Strategy;

use GreenCape\AllPairs\Parameter;

class QictStrategy implements StrategyInterface
{
    /**
     * @param  Parameter[]  $parameterDefinition
     * @param  int          $order
     *
     * @return array
     */
    public function combine($parameterDefinition, $order = 2)
    {
        /** @var  int Number of candidate testSet arrays to generate before picking one to add to
         *            testSets list */
        $poolSize = 20;

        /** @var  int[][]  In-memory representation of input file as integers */
        $legalValues = array();

        /** @var  string[] One-dimensional array of all parameter values */
        $parameterValues = array();

        /** @var  int[] Rectangular array; does not change, used to generate unusedCounts array */
        $allPairsDisplay = array();

        /** @var  array List of pairs which have not yet been captured */
        $unusedPairs = array();

        /** @var  array Square array -- changes */
        $unusedPairsSearch = array();

        /** @var  int[] The parameter position for a given value. The indexes are parameter values, the cell values are positions within a testSet */
        $parameterPositions = array();

        /** @var  int[] Count of each parameter value in unusedPairs List. The indexes are parameter values, cell values are counts of how many times the parameter value appears in the unusedPairs collection */
        $unusedCounts = array();

        /** @var  array The main result data structure */
        $testSets = array();

        $labels           = array();
        $kk               = 0; // points into parameterValues
        $numberParameters = count($parameterDefinition);
        
        foreach ($parameterDefinition as $strValues) {
            $values = array();

            for ($i = 0, $iMax = count($strValues); $i < $iMax; ++$i) {
                $values[$i]           = $kk;
                $parameterValues[$kk] = $strValues[$i];
                ++$kk;
            }

            $labels[]      = $strValues->getLabel();
            $legalValues[] = $values;
        }

        // Process the legalValues array to populate the allPairsDisplay & unusedPairs & unusedPairsSearch collections
        $currPair = 0;
        
        for ($i = 0; $i <= count($legalValues) - 2; ++$i) {
            for ($j = $i + 1; $j <= count($legalValues) - 1; ++$j) {
                $firstRow  = $legalValues[$i];
                $secondRow = $legalValues[$j];

                foreach ($firstRow as $x => $xValue) {
                    foreach ($secondRow as $y => $yValue) {
                        $allPairsDisplay[$currPair][0] = $xValue;        // Pair first value
                        $allPairsDisplay[$currPair][1] = $yValue;        // Pair second value

                        $aPair         = array(
                            $firstRow[$x],
                            $secondRow[$y],
                        );
                        $unusedPairs[] = $aPair;

                        $unusedPairsSearch[$this->hash($xValue, $yValue)] = 1;

                        ++$currPair;
                    }
                }
            }
        }

        // Process legalValues to populate parameterPositions array
        $k = 0;                // points into parameterPositions
        foreach ($legalValues as $i => $iValue) {
            $curr = $iValue;
            for ($j = 0, $jMax = count($curr); $j < $jMax; ++$j) {
                $parameterPositions[$k++] = $i;
            }
        }

        // Process allPairsDisplay to determine unusedCounts array
        foreach ($allPairsDisplay as $iValue) {
            @$unusedCounts[$iValue[0]]++;
            @$unusedCounts[$iValue[1]]++;
        }

        while (count($unusedPairs) > 0) {
            $candidateSets = array();                                      // holds candidate testSets

            for ($candidate = 0; $candidate < $poolSize; ++$candidate) {
                $testSet = array(); // make an empty candidate testSet

                // Pick "best" unusedPair -- the pair which has the sum of the most unused values
                $bestWeight      = 0;
                $indexOfBestPair = 0;
                foreach ($unusedPairs as $i => $curr) {
                    $weight = $unusedCounts[$curr[0]] + $unusedCounts[$curr[1]];
                    if ($weight > $bestWeight) {
                        $bestWeight      = $weight;
                        $indexOfBestPair = $i;
                    }
                }

                $best = $unusedPairs[$indexOfBestPair];

                $firstPos  = $parameterPositions[$best[0]]; // position of first value from best unused pair
                $secondPos = $parameterPositions[$best[1]];

                // Generate a random order to fill parameter positions
                $ordering = array();
                for ($i = 0; $i < $numberParameters; ++$i) {
                    $ordering[$i] = $i;
                }

                // Put firstPos at ordering[0] && secondPos at ordering[1]
                $ordering[0]         = $firstPos;
                $ordering[$firstPos] = 0;

                $t                    = $ordering[1];
                $ordering[1]          = $secondPos;
                $ordering[$secondPos] = $t;

                // Shuffle ordering[2] thru ordering[last]
                for ($i = 2, $iMax = count($ordering); $i < $iMax; $i++) {
                    // Knuth shuffle. start at i=2 because want first two slots left alone
                    $j            = mt_rand($i, $numberParameters - 1);
                    $temp         = $ordering[$j];
                    $ordering[$j] = $ordering[$i];
                    $ordering[$i] = $temp;
                }

                // Place two parameter values from best unused pair into candidate testSet
                $testSet[$firstPos]  = $best[0];
                $testSet[$secondPos] = $best[1];

                // For remaining parameter positions in candidate testSet, try each possible legal value, picking the one which captures the most unused pairs . . .
                for ($i = 2; $i < $numberParameters; ++$i) { // start at 2 because first two parameter have been placed
                    $currPos        = $ordering[$i];
                    $possibleValues = $legalValues[$currPos];

                    $highestCount = 0; // highest of these counts
                    $bestJ        = 0; // index of the possible value which yields the highestCount

                    foreach ($possibleValues as $j => $jValue) { // examine pairs created by each possible value and each parameter value already there
                        $currentCount = 0;                            // count the unusedPairs grabbed by adding a possible value

                        for ($p = 0; $p < $i; ++$p) { // parameters already placed
                            $candidatePair = array(
                                $possibleValues[$j],
                                $testSet[$ordering[$p]],
                            );

                            if ($unusedPairsSearch[$this->hash($candidatePair[0], $candidatePair[1])] === 1) {
                                ++$currentCount;
                            }
                        }

                        if ($currentCount > $highestCount) {
                            $highestCount = $currentCount;
                            $bestJ        = $j;
                        }
                    }

                    // Place the value which captured the most pairs
                    $testSet[$currPos] = $possibleValues[$bestJ];
                }

                // Add candidate testSet to candidateSets array
                $candidateSets[$candidate] = $testSet;
            }

            // Iterate through candidateSets to determine the best candidate
            $indexOfBestCandidate = mt_rand(0, count($candidateSets) - 1); // pick a random index as best
            $mostPairsCaptured    = $this->numberPairsCaptured($candidateSets[$indexOfBestCandidate],
                $unusedPairsSearch);

            foreach ($candidateSets as $i => $iValue) {
                $pairsCaptured = $this->numberPairsCaptured($iValue, $unusedPairsSearch);

                if ($pairsCaptured > $mostPairsCaptured) {
                    $mostPairsCaptured    = $pairsCaptured;
                    $indexOfBestCandidate = $i;
                }
            }

            // Add the best candidate to the main testSets List
            $bestTestSet = $candidateSets[$indexOfBestCandidate];
            $testSets[]  = $bestTestSet;

            // Now perform all updates
            for ($i = 0; $i <= $numberParameters - 2; ++$i) {
                for ($j = $i + 1; $j <= $numberParameters - 1; ++$j) {
                    $v1 = $bestTestSet[$i]; // value 1 of newly added pair
                    $v2 = $bestTestSet[$j]; // value 2 of newly added pair

                    --$unusedCounts[$v1];
                    --$unusedCounts[$v2];

                    $unusedPairsSearch[$this->hash($v1, $v2)] = 0;

                    foreach ($unusedPairs as $p => $pValue) {
                        $curr = $pValue;

                        if ($curr[0] === $v1 && $curr[1] === $v2) {
                            unset($unusedPairs[$p]);
                            $unusedPairs = array_values($unusedPairs);
                        }
                    }
                }
            }
        }

        $result = array();
        foreach ($testSets as $set) {
            $parameters = array();
            for ($j = 0, $jMax = count($set); $j < $jMax; ++$j) {
                $parameters[$labels[$j]] = $parameterValues[$set[$j]];
            }
            $result[] = $parameters;
        }

        return $result;
    }

    private function numberPairsCaptured($ts, $unusedPairsSearch) // number of unused pairs captured by testSet ts
    {
        $ans = 0;
        for ($i = 0; $i <= count($ts) - 2; ++$i) {
            for ($j = $i + 1; $j <= count($ts) - 1; ++$j) {
                if ($unusedPairsSearch[$this->hash($ts[$i], $ts[$j])] === 1) {
                    ++$ans;
                }
            }
        }

        return $ans;
    }

    private function hash($a, $b)
    {
        $values = array($a, $b);
        sort($values);

        return '{' . implode(', ', $values) . '}';
    }
}
