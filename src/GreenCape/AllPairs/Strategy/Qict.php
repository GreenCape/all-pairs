<?php

namespace GreenCape\AllPairs;

class QictStrategy implements Strategy
{
	/** @var  int  Number of parameters */
	private $numberParameters;

	/** @var  string[]  One-dimensional array of all parameters */
	private $parameterLabels = array();

	/** @var  string[]  One-dimensional array of all parameter values */
	private $parameterValues = array();

	/** @var  array  List of pairs which have not yet been captured */
	private $unusedPairs = array();

	/** @var  array[]  In-memory representation of input file as integers */
	private $legalValues = array();

	/** @var  int[] $parameterPositions The parameter position for a given value. The indexes are parameter values, the cell values are positions within a testSet */
	private $parameterPositions = array();

	/** @var  int[]  Rectangular array; does not change, used to generate unusedCounts array */
	private $allPairsDisplay = array();

	/** @var  PairHash  Square array -- changes */
	private $unusedPairsSearch;

	/** @var  int[]  Count of each parameter value in unusedPairs List. The indexes are parameter values, cell values are counts of how many times the parameter value appears in the unusedPairs collection */
	private $unusedCounts = array();

	/**
	 * @param   Reader $parameterDefinition
	 *
	 * @return array
	 */
	public function combine(Reader $parameterDefinition)
	{
		$this->importParameters($parameterDefinition->getParameters());
		$this->createPairs();

		/** @var  int $poolSize Number of candidate testSet arrays to generate before picking one to add to testSets list */
		$poolSize = 20;
		$testSets = array();
		while (count($this->unusedPairs) > 0)
		{
			$testSets[] = $this->chooseBestTestSet($this->generateCandidates($poolSize));
		}

		return $this->formatReturnValue($testSets);
	}

	/**
	 * @param Parameter[] $parameterDefinition
	 */
	private function importParameters($parameterDefinition)
	{
		$this->numberParameters = count($parameterDefinition);

		/** @var  int $kk Points into parameterValues */
		$kk = 0;
		foreach ($parameterDefinition as $position => $parameter)
		{
			$this->parameterLabels[$position] = $parameter->getLabel();
			$values                           = array();
			for ($i = 0; $i < count($parameter); ++$i)
			{
				$values[$i]                    = $kk;
				$this->parameterValues[$kk]    = $parameter[$i];
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
		$this->unusedPairsSearch = new PairHash;
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

	/**
	 * Iterate through candidateSets to determine the best candidate
	 *
	 * @param $candidateSets
	 *
	 * @return array
	 */
	private function chooseBestTestSet($candidateSets)
	{
		$mostPairsCaptured = 0;
		$bestTestSet       = null;
		foreach ($candidateSets as $set)
		{
			$pairsCaptured = $this->countPairsCaptured($set, $this->unusedPairsSearch);
			if ($pairsCaptured >= $mostPairsCaptured)
			{
				$mostPairsCaptured = $pairsCaptured;
				$bestTestSet       = $set;
			}
		}
		$this->updateState($bestTestSet);

		return $bestTestSet;
	}

	/**
	 * @param $poolSize
	 *
	 * @return array
	 */
	private function generateCandidates($poolSize)
	{
		$candidateSets = array();
		for ($candidate = 0; $candidate < $poolSize; ++$candidate)
		{
			$candidateSets[$candidate] = $this->generateRandomTestSet();
		}

		return $candidateSets;
	}

	/**
	 * @param $testSets
	 *
	 * @return array
	 */
	private function formatReturnValue($testSets)
	{
		$result = array();
		foreach ($testSets as $set)
		{
			$parameters = array();
			for ($j = 0; $j < $this->numberParameters; ++$j)
			{
				$parameters[$this->parameterLabels[$j]] = $this->parameterValues[$set[$j]];
			}
			$result[] = $parameters;
		}

		return $result;
	}

	/**
	 * Get number of unused pairs captured by testSet ts
	 *
	 * @param          $ts
	 * @param PairHash $unusedPairsSearch
	 *
	 * @return int
	 */
	private function countPairsCaptured($ts, PairHash $unusedPairsSearch)
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
	 * Perform all updates
	 *
	 * @param $testSet
	 *
	 * @return void
	 */
	private function updateState($testSet)
	{
		$length = count($testSet);
		for ($i = 0; $i < $length - 1; ++$i)
		{
			for ($j = $i + 1; $j < $length; ++$j)
			{
				$v1 = $testSet[$i]; // value 1 of newly added pair
				$v2 = $testSet[$j]; // value 2 of newly added pair

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

	/**
	 * @return array
	 */
	private function generateRandomTestSet()
	{
		$testSet = array();
		$next    = $this->findNextPair();

		$firstPos  = $this->parameterPositions[$next[0]]; // position of first value from best unused pair
		$secondPos = $this->parameterPositions[$next[1]];
		$ordering  = $this->orderParameters($firstPos, $secondPos);

		// Place two parameter values from best unused pair into candidate testSet
		$testSet[$firstPos]  = $next[0];
		$testSet[$secondPos] = $next[1];

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
					if ($this->unusedPairsSearch->get($possibleValues[$j], $testSet[$ordering[$p]]) == 1)
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

		return $testSet;
	}

	/**
	 * Pick "best" unusedPair -- the pair which has the sum of the most unused values
	 *
	 * @return array
	 */
	private function findNextPair()
	{
		$bestWeight      = 0;
		$indexOfBestPair = 0;
		foreach ($this->unusedPairs as $i => $curr)
		{
			$weight = $this->unusedCounts[$curr[0]] + $this->unusedCounts[$curr[1]];
			if ($weight >= $bestWeight)
			{
				$bestWeight      = $weight;
				$indexOfBestPair = $i;
			}
		}

		return $this->unusedPairs[$indexOfBestPair];
	}

	/**
	 * Generate a random order to fill parameter positions
	 *
	 * @param $firstPos
	 * @param $secondPos
	 *
	 * @return array
	 */
	private function orderParameters($firstPos, $secondPos)
	{
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

		$this->shuffleOrdering($ordering);

		return $ordering;
	}

	/**
	 * Shuffle ordering[2] thru ordering[last]
	 *
	 * @param &$ordering
	 *
	 * @return void
	 */
	private function shuffleOrdering(&$ordering)
	{
		for ($i = 2; $i < $this->numberParameters; $i++)
		{
			// Knuth shuffle. start at i=2 because want first two slots left alone
			$j            = rand($i, $this->numberParameters - 1);
			$temp         = $ordering[$j];
			$ordering[$j] = $ordering[$i];
			$ordering[$i] = $temp;
		}
	}
}
