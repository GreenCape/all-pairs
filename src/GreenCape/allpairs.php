<?php

class Array2D
{
	private $data = array();

	public function set($i, $j, $value)
	{
		@$this->data[min($i, $j)][max($i, $j)] = $value;
	}

	public function get($i, $j)
	{
		if (!isset($this->data[min($i, $j)]))
		{
			throw new OutOfBoundsException();
		}
		if (!isset($this->data[min($i, $j)][max($i, $j)]))
		{
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
	/** @var  string[]  One-dimensional array of all parameters */
	private $parameterLabels;

	/** @var  int  Number of parameters */
	private $numberParameters;

	/** @var  string[]  One-dimensional array of all parameter values */
	private $parameterValues = array();

	/** @var  array[]  In-memory representation of input file as integers */
	private $legalValues = array();

	/** @var  int[] $parameterPositions The parameter position for a given value. The indexes are parameter values, the cell values are positions within a testSet */
	private $parameterPositions = array();

	/** @var  array  List of pairs which have not yet been captured */
	private $unusedPairs = array();

	/** @var  int[]  Rectangular array; does not change, used to generate unusedCounts array */
	private $allPairsDisplay = array();

	/** @var  Array2D  Square array -- changes */
	private $unusedPairsSearch;

	/** @var  int[]  Count of each parameter value in unusedPairs List. The indexes are parameter values, cell values are counts of how many times the parameter value appears in the unusedPairs collection */
	private $unusedCounts = array();

	public function execute($parameterDefinition)
	{
		/** @var  int $poolSize Number of candidate testSet arrays to generate before picking one to add to testSets list */
		$poolSize = 20;

		/** @var  array $testSets The main result data structure */
		$testSets = array();

		$this->tokenizeParameterDefinition($parameterDefinition);
		$this->createPairs();

		while (count($this->unusedPairs) > 0)
		{
			$candidateSets = array(); // holds candidate testSets

			for ($candidate = 0; $candidate < $poolSize; ++$candidate)
			{
				$testSet = array(); // make an empty candidate testSet

				// Pick "best" unusedPair -- the pair which has the sum of the most unused values
				$bestWeight      = 0;
				$indexOfBestPair = 0;
				for ($i = 0; $i < count($this->unusedPairs); ++$i)
				{
					$curr   = $this->unusedPairs[$i];
					$weight = $this->unusedCounts[$curr[0]] + $this->unusedCounts[$curr[1]];
					if ($weight >= $bestWeight)
					{
						$bestWeight      = $weight;
						$indexOfBestPair = $i;
					}
				}

				$best = $this->unusedPairs[$indexOfBestPair];

				$firstPos  = $this->parameterPositions[$best[0]]; // position of first value from best unused pair
				$secondPos = $this->parameterPositions[$best[1]];

				// Generate a random order to fill parameter positions
				$ordering = array();
				for ($i = 0; $i < $this->numberParameters; ++$i)
				{
					$ordering[$i] = $i;
				}

				// Put firstPos at ordering[0] && secondPos at ordering[1]
				$ordering[0]         = $firstPos;
				$ordering[$firstPos] = 0;

				$t                    = $ordering[1];
				$ordering[1]          = $secondPos;
				$ordering[$secondPos] = $t;

				// Shuffle ordering[2] thru ordering[last]
				for ($i = 2; $i < $this->numberParameters; $i++)
				{
					// Knuth shuffle. start at i=2 because want first two slots left alone
					$j            = rand($i, $this->numberParameters - 1);
					$temp         = $ordering[$j];
					$ordering[$j] = $ordering[$i];
					$ordering[$i] = $temp;
				}

				// Place two parameter values from best unused pair into candidate testSet
				$testSet[$firstPos]  = $best[0];
				$testSet[$secondPos] = $best[1];

				// For remaining parameter positions in candidate testSet, try each possible legal value, picking the one which captures the most unused pairs . . .
				for ($i = 2; $i < $this->numberParameters; ++$i)
				{ // start at 2 because first two parameter have been placed
					$currPos        = $ordering[$i];
					$possibleValues = $this->legalValues[$currPos];

					$highestCount = 0; // highest of these counts
					$bestJ        = 0; // index of the possible value which yields the highestCount
					for ($j = 0; $j < count($possibleValues); ++$j)
					{ // examine pairs created by each possible value and each parameter value already there
						$currentCount = 0; // count the unusedPairs grabbed by adding a possible value
						for ($p = 0; $p < $i; ++$p)
						{ // parameters already placed
							$candidatePair = array(
								$possibleValues[$j],
								$testSet[$ordering[$p]]
							);

							if ($this->unusedPairsSearch->get($candidatePair[0], $candidatePair[1]) == 1)
							{
								++$currentCount;
							}
						}
						if ($currentCount > $highestCount)
						{
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
			$indexOfBestCandidate = rand(0, count($candidateSets) - 1); // pick a random index as best
			$mostPairsCaptured    = $this->NumberPairsCaptured($candidateSets[$indexOfBestCandidate], $this->unusedPairsSearch);

			for ($i = 0; $i < count($candidateSets); ++$i)
			{
				$pairsCaptured = $this->NumberPairsCaptured($candidateSets[$i], $this->unusedPairsSearch);
				if ($pairsCaptured > $mostPairsCaptured)
				{
					$mostPairsCaptured    = $pairsCaptured;
					$indexOfBestCandidate = $i;
				}
			}

			// Add the best candidate to the main testSets List
			$bestTestSet = $candidateSets[$indexOfBestCandidate];
			$testSets[]  = $bestTestSet;

			// Now perform all updates
			for ($i = 0; $i < $this->numberParameters - 1; ++$i)
			{
				for ($j = $i + 1; $j < $this->numberParameters; ++$j)
				{
					$v1 = $bestTestSet[$i]; // value 1 of newly added pair
					$v2 = $bestTestSet[$j]; // value 2 of newly added pair

					--$this->unusedCounts[$v1];
					--$this->unusedCounts[$v2];

					$this->unusedPairsSearch->set($v1, $v2, 0);

					for ($p = 0; $p < count($this->unusedPairs); ++$p)
					{
						$curr = $this->unusedPairs[$p];

						if ($curr[0] == $v1 && $curr[1] == $v2)
						{
							unset($this->unusedPairs[$p]);
							$this->unusedPairs = array_values($this->unusedPairs);
						}
					}
				}
			}
		}

		$result = array();
		for ($i = 0; $i < count($testSets); ++$i)
		{
			$set  = array();
			$curr = $testSets[$i];
			for ($j = 0; $j < $this->numberParameters; ++$j)
			{
				$set[$this->parameterLabels[$j]] = $this->parameterValues[$curr[$j]];
			}
			$result[] = $set;
		}

		return $result;
	}

	private function NumberPairsCaptured($ts, Array2D $unusedPairsSearch) // number of unused pairs captured by testSet ts
	{
		$ans = 0;
		for ($i = 0; $i <= count($ts) - 2; ++$i)
		{
			for ($j = $i + 1; $j <= count($ts) - 1; ++$j)
			{
				if ($unusedPairsSearch->get($ts[$i], $ts[$j]) == 1)
				{
					++$ans;
				}
			}
		}

		return $ans;
	}

	/**
	 * @param $parameterDefinition
	 */
	private function tokenizeParameterDefinition($parameterDefinition)
	{
		$this->parameterLabels  = array_keys($parameterDefinition);
		$this->numberParameters = count($parameterDefinition);

		/** @var  int $kk Points into parameterValues */
		$kk = 0;

		foreach (array_values($parameterDefinition) as $position => $strValues)
		{
			$values = array();
			for ($i = 0; $i < count($strValues); ++$i)
			{
				$values[$i]                    = $kk;
				$this->parameterValues[$kk]    = $strValues[$i];
				$this->parameterPositions[$kk] = $position;
				++$kk;
			}
			$this->legalValues[] = $values;
		}
	}

	/**
	 * Process the legalValues array to populate the allPairsDisplay & unusedPairs & unusedPairsSearch collections
	 *
	 * @return  void
	 */
	private function createPairs()
	{
		$this->unusedPairsSearch = new Array2D;
		for ($i = 0; $i < $this->numberParameters - 1; ++$i)
		{
			for ($j = $i + 1; $j < $this->numberParameters; ++$j)
			{
				foreach ($this->legalValues[$i] as $x)
				{
					foreach ($this->legalValues[$j] as $y)
					{
						$this->unusedPairs[]     = array($x, $y);
						$this->allPairsDisplay[] = array($x, $y);
						$this->unusedPairsSearch->set($x, $y, 1);
						@$this->unusedCounts[$x]++;
						@$this->unusedCounts[$y]++;
					}
				}
			}
		}
	}
}

/** @var  string $file */
// $file = '../../original/testData.txt';
$file = '../../tests/data/server.txt';
// $file = '../../tests/data/prime.txt';

print("\nBegin pair-wise testset generation\n");

$app    = new AllPairs();
$result = $app->execute(getParameterDefinitionsFromFile($file));

// Display results
print("\nResult testsets: \n");
foreach ($result as $i => $set)
{
	printf("%3d: ", $i);
	print(implode(', ', $set));
	print("\n");
}
print("\nEnd\n");
