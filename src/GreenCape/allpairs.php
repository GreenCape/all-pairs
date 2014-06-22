<?php

class Array2D
{
	private $data = array();

	public function set($i, $j, $value)
	{
		@$this->data[min($i,$j)][max($i,$j)] = $value;
	}

	public function get($i, $j)
	{
		if (!isset($this->data[min($i, $j)])) {
			throw new OutOfBoundsException();
		}
		if (!isset($this->data[min($i, $j)][max($i, $j)])) {
			throw new OutOfBoundsException();
		}
		return $this->data[min($i, $j)][max($i, $j)];
	}
}

/**
 * @param $file
 *
 * @return array
 */
function getParameterDefinitionsFromFile($file)
{
	$parameterDefinition = array();
	foreach (file($file) as $line)
	{
		$lineTokens = explode(':', $line);
		$strValues  = explode(',', $lineTokens[1]);
		for ($i = 0; $i < count($strValues); ++$i)
		{
			$strValues[$i] = trim($strValues[$i]);
		}
		$parameterDefinition[$lineTokens[0]] = $strValues;
	}

	return $parameterDefinition;
}

class AllPairs
{
	public function execute($parameterDefinition)
	{
		/** @var  int  $numberParameterValues */
		$numberParameterValues = 0;

		/** @var  int  $numberPairs */
		$numberPairs = 0;

		/** @var  int  $poolSize  Number of candidate testSet arrays to generate before picking one to add to testSets list */
		$poolSize = 20;

		/** @var  int[][]  $legalValues  In-memory representation of input file as integers */
		$legalValues = array();

		/** @var  string[]  $parameterValues  One-dimensional array of all parameter values */
		$parameterValues = array();

		/** @var  int[]  $allPairsDisplay  Rectangular array; does not change, used to generate unusedCounts array */
		$allPairsDisplay = array();

		/** @var  array  $unusedPairs  List of pairs which have not yet been captured */
		$unusedPairs = array();

		/** @var  Array2D  $unusedPairsSearch  Square array -- changes */
		$unusedPairsSearch = new Array2D;

		/** @var  int[]  $parameterPositions  The parameter position for a given value. The indexes are parameter values, the cell values are positions within a testSet */
		$parameterPositions = array();

		/** @var  int[]  $unusedCounts  Count of each parameter value in unusedPairs List. The indexes are parameter values, cell values are counts of how many times the parameter value appears in the unusedPairs collection */
		$unusedCounts = array();

		/** @var  array  $testSets  The main result data structure */
		$testSets = array();

		try {
			print("\nBegin pair-wise testset generation\n");

			$numberTests = 1;
			$largest = array();
			$labels = array();
			$kk = 0; // $kk points into parameterValues
			$numberParameters = count($parameterDefinition);
			foreach ($parameterDefinition as $label => $strValues) {
				$count = count($strValues);
				$numberParameterValues += $count;
				$numberTests *= $count;
				$largest[] = $count;

				$values = array();
				for ($i = 0; $i < count($strValues); ++$i) {
					$values[$i] = $kk;
					$parameterValues[$kk] = $strValues[$i];
					++$kk;
				}

				$labels[] = $label;
				$legalValues[] = $values;
			}
			rsort($largest);

			print("There are " . $numberParameters . " parameters with " . $numberParameterValues . " parameter values.\n");
			print("There are " . $numberTests . " possible combinations.\n");
			print("The optimum (least possible) number of combinations covering all pairs is " . $largest[0] * $largest[1] . ".\n");

			// Determine the number of pairs for this input set
			for ($i = 0; $i <= count($legalValues) - 2; ++$i) {
				for ($j = $i + 1; $j <= count($legalValues) - 1; ++$j) {
					$numberPairs += count($legalValues[$i]) * count($legalValues[$j]);
				}
			}
			print("\nThere are " . $numberPairs . " pairs\n");

			// Process the legalValues array to populate the allPairsDisplay & unusedPairs & unusedPairsSearch collections
			$currPair = 0;
			for ($i = 0; $i <= count($legalValues) - 2; ++$i) {
				for ($j = $i + 1; $j <= count($legalValues) - 1; ++$j) {
					$firstRow = $legalValues[$i];
					$secondRow = $legalValues[$j];
					for ($x = 0; $x < count($firstRow); ++$x) {
						for ($y = 0; $y < count($secondRow); ++$y) {
							// Pair first value
							$allPairsDisplay[$currPair][0] = $firstRow[$x];

							// Pair second value
							$allPairsDisplay[$currPair][1] = $secondRow[$y];

							$aPair = array(
								$firstRow[$x],
								$secondRow[$y]
							);
							$unusedPairs[] = $aPair;

							$unusedPairsSearch->set($firstRow[$x], $secondRow[$y], 1);

							++$currPair;
						}
					}
				}
			}

			// Process legalValues to populate parameterPositions array
			$k = 0; // points into parameterPositions
			for ($i = 0; $i < count($legalValues); ++$i) {
				$curr = $legalValues[$i];
				for ($j = 0; $j < count($curr); ++$j) {
					$parameterPositions[$k++] = $i;
				}
			}

			// Process allPairsDisplay to determine unusedCounts array
			for ($i = 0; $i < count($allPairsDisplay); ++$i) {
				@$unusedCounts[$allPairsDisplay[$i][0]]++;
				@$unusedCounts[$allPairsDisplay[$i][1]]++;
			}

			print("\nComputing testsets which capture all possible pairs ...\n");
			while (count($unusedPairs) > 0) {
				$candidateSets = array(); // holds candidate testSets

				for ($candidate = 0; $candidate < $poolSize; ++$candidate) {
					$testSet = array(); // make an empty candidate testSet

					// Pick "best" unusedPair -- the pair which has the sum of the most unused values
					$bestWeight = 0;
					$indexOfBestPair = 0;
					for ($i = 0; $i < count($unusedPairs); ++$i) {
						$curr = $unusedPairs[$i];
						$weight = $unusedCounts[$curr[0]] + $unusedCounts[$curr[1]];
						if ($weight > $bestWeight) {
							$bestWeight = $weight;
							$indexOfBestPair = $i;
						}
					}

					$best = $unusedPairs[$indexOfBestPair];

					$firstPos = $parameterPositions[$best[0]]; // position of first value from best unused pair
					$secondPos = $parameterPositions[$best[1]];

					// Generate a random order to fill parameter positions
					$ordering = array();
					for ($i = 0; $i < $numberParameters; ++$i) {
						$ordering[$i] = $i;
					}

					// Put firstPos at ordering[0] && secondPos at ordering[1]
					$ordering[0] = $firstPos;
					$ordering[$firstPos] = 0;

					$t = $ordering[1];
					$ordering[1] = $secondPos;
					$ordering[$secondPos] = $t;

					// Shuffle ordering[2] thru ordering[last]
					for ($i = 2; $i < $numberParameters; $i++) {
						// Knuth shuffle. start at i=2 because want first two slots left alone
						$j = rand($i, $numberParameters-1);
						$temp = $ordering[$j];
						$ordering[$j] = $ordering[$i];
						$ordering[$i] = $temp;
					}

					// Place two parameter values from best unused pair into candidate testSet
					$testSet[$firstPos] = $best[0];
					$testSet[$secondPos] = $best[1];

					// For remaining parameter positions in candidate testSet, try each possible legal value, picking the one which captures the most unused pairs . . .
					for ($i = 2; $i < $numberParameters; ++$i) {// start at 2 because first two parameter have been placed
						$currPos = $ordering[$i];
						$possibleValues = $legalValues[$currPos];

						$highestCount = 0;  // highest of these counts
						$bestJ = 0;         // index of the possible value which yields the highestCount
						for ($j = 0; $j < count($possibleValues); ++$j) { // examine pairs created by each possible value and each parameter value already there
							$currentCount = 0;  // count the unusedPairs grabbed by adding a possible value
							for ($p = 0; $p < $i; ++$p) { // parameters already placed
								$candidatePair = array(
									$possibleValues[$j],
									$testSet[$ordering[$p]]
								);

								if ($unusedPairsSearch->get($candidatePair[0], $candidatePair[1]) == 1) {
									++$currentCount;
								}
							}
							if ($currentCount > $highestCount) {
								$highestCount = $currentCount;
								$bestJ = $j;
							}
						}

						// Place the value which captured the most pairs
						$testSet[$currPos] = $possibleValues[$bestJ];
					}

					// Add candidate testSet to candidateSets array
					$candidateSets[$candidate] = $testSet;
				}

				// Iterate through candidateSets to determine the best candidate
				$indexOfBestCandidate = rand(0, count($candidateSets) - 1); // pick a random index as best
				$mostPairsCaptured = $this->NumberPairsCaptured($candidateSets[$indexOfBestCandidate], $unusedPairsSearch);

				for ($i = 0; $i < count($candidateSets); ++$i) {
					$pairsCaptured = $this->NumberPairsCaptured($candidateSets[$i], $unusedPairsSearch);
					if ($pairsCaptured > $mostPairsCaptured) {
						$mostPairsCaptured = $pairsCaptured;
						$indexOfBestCandidate = $i;
					}
				}

				// Add the best candidate to the main testSets List
				$bestTestSet = $candidateSets[$indexOfBestCandidate];
				$testSets[] = $bestTestSet;

				// Now perform all updates
				for ($i = 0; $i <= $numberParameters - 2; ++$i) {
					for ($j = $i + 1; $j <= $numberParameters - 1; ++$j) {
						$v1 = $bestTestSet[$i]; // value 1 of newly added pair
						$v2 = $bestTestSet[$j]; // value 2 of newly added pair

						--$unusedCounts[$v1];
						--$unusedCounts[$v2];

						$unusedPairsSearch->set($v1,$v2, 0);

						for ($p = 0; $p < count($unusedPairs); ++$p) {
							$curr = $unusedPairs[$p];

							if ($curr[0] == $v1 && $curr[1] == $v2) {
								unset($unusedPairs[$p]);
								$unusedPairs = array_values($unusedPairs);
							}
						}
					}
				}
			}

			$result = array();
			for ($i = 0; $i < count($testSets); ++$i) {
				$set = array();
				$curr = $testSets[$i];
				for ($j = 0; $j < $numberParameters; ++$j) {
					$set[$labels[$j]] = $parameterValues[$curr[$j]];
				}
				$result[] = $set;
			}

			return $result;

		} catch (Exception $ex) {
			print("Fatal: " . $ex->getMessage());
		}
	}

	private function NumberPairsCaptured($ts, Array2D $unusedPairsSearch)  // number of unused pairs captured by testSet ts
	{
		$ans = 0;
		for ($i = 0; $i <= count($ts) - 2; ++$i) {
			for ($j = $i + 1; $j <= count($ts) - 1; ++$j) {
				if ($unusedPairsSearch->get($ts[$i], $ts[$j]) == 1) {
					++$ans;
				}
			}
		}
		return $ans;
	}
}

/** @var  string  $file */
// $file = '../../original/testData.txt';
// $file = '../../tests/data/server.txt';
$file = '../../tests/data/prime.txt';

$app = new AllPairs();
$result = $app->execute(getParameterDefinitionsFromFile($file));

// Display results
print("\nResult testsets: \n");
foreach ($result as $i => $set) {
	printf("%3d: ", $i);
	print(implode(', ', $set));
	print("\n");
}
print("\nEnd\n");
