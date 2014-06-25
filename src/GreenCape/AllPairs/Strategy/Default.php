<?php

namespace GreenCape\AllPairs;

class DefaultStrategy implements Strategy
{
	private $ordering;
	private $tokenizedParameterList = array();
	private $tuples = array();
	private $token = array();
	private $label = array();

	/**
	 * @param Reader $reader
	 *
	 * @return array
	 */
	public function combine(Reader $reader)
	{
		$parameterList = $reader->getParameters();
		$subModules    = $reader->getSubModules();
		$constraints   = $reader->getConstraints();

		return $this->generateTestSets($parameterList, $subModules, $constraints);
	}

	/**
	 * @param Parameter[] $parameterList
	 * @param             $subModules
	 * @param             $constraints
	 *
	 * @return array
	 */
	private function generateTestSets($parameterList, $subModules, $constraints)
	{
		$this->tokenizeParameterList($parameterList);

		foreach ($this->tokenizedParameterList as $parameterPosition1 => $parameter1)
		{
			foreach ($this->tokenizedParameterList as $parameterPosition2 => $parameter2)
			{
				if ($parameterPosition2 <= $parameterPosition1)
				{
					continue;
				}
				foreach ($parameter1 as $value1)
				{
					foreach ($parameter2 as $value2)
					{
						$this->createPair($value1, $value2);
					}
				}
			}
		}

		$testSets = array();
		foreach ($this->tokenizedParameterList[$this->ordering[0]] as $value1)
		{
			foreach ($this->tokenizedParameterList[$this->ordering[1]] as $value2)
			{
				$testSet                     = array_fill(0, count($this->tokenizedParameterList), null);
				$testSet[$this->ordering[0]] = $value1;
				$testSet[$this->ordering[1]] = $value2;

				$testSets[] = $this->completeTestset($testSet);
			}
		}

		foreach ($this->tokenizedParameterList as $parameterPosition1 => $parameter1)
		{
			foreach ($this->tokenizedParameterList as $parameterPosition2 => $parameter2)
			{
				if ($parameterPosition2 <= $parameterPosition1)
				{
					continue;
				}
				foreach ($parameter1 as $value1)
				{
					foreach ($parameter2 as $value2)
					{
						$hash     = $this->hash(array($value1, $value2));
						$useCount = $this->tuples[$hash]['useCount'];
						if ($useCount == 0)
						{
							$testSet                                     = array_fill(0, count($this->tokenizedParameterList), null);
							$testSet[$this->token[$value1]['parameter']] = $value1;
							$testSet[$this->token[$value2]['parameter']] = $value2;
							$testSets[]                                  = $this->completeTestset($testSet);
						}
					}
				}
			}
		}

		/*
		usort($testSets, function($a, $b) {
			for ($i = 0; $i < count($a); $i++)
			{
				if ($a[$i] != $b[$i])
				{
					return strcmp($a[$i], $b[$i]);
				}
			}
			return 0;
		});
		*/

		$result = array();
		foreach ($testSets as $set)
		{
			$parameters = array();
			for ($j = 0; $j < count($set); ++$j)
			{
				$parameters[$this->label[$j]] = $this->token[$set[$j]]['value'];
			}
			$result[] = $parameters;
		}

		return $result;
	}

	private function hash(array $a)
	{
		sort($a);

		return '{' . implode(', ', $a) . '}';
	}

	/**
	 * @param $testSet
	 *
	 * @return array
	 */
	private function completeTestset($testSet)
	{
		for ($position = 0; $position < count($this->ordering); $position++)
		{
			if (!is_null($testSet[$this->ordering[$position]]))
			{
				continue;
			}
			$threshold = PHP_INT_MAX;
			$bestValue = null;
			foreach ($this->tokenizedParameterList[$this->ordering[$position]] as $value)
			{
				$pairScore = 0;
				for ($i = 0; $i < $position; $i++)
				{
					$pairScore += $this->tuples[$this->hash(array(
						$testSet[$this->ordering[$i]],
						$value
					))]['useCount'];
				}
				$score = $pairScore;
				if ($score < $threshold)
				{
					$threshold = $score;
					$bestValue = $value;
				}
			}
			$testSet[$this->ordering[$position]] = $bestValue;
		}
		$this->updateUseCount($testSet);

		return $testSet;
	}

	/**
	 * @param $testSet
	 *
	 * @return int
	 */
	private function updateUseCount($testSet)
	{
		for ($i = 0; $i < count($testSet) - 1; $i++)
		{
			for ($j = $i + 1; $j < count($testSet); $j++)
			{
				$this->tuples[$this->hash(array($testSet[$i], $testSet[$j]))]['useCount']++;
			}
		}
	}

	/**
	 * @param $parameterList
	 */
	private function tokenizeParameterList($parameterList)
	{
		$tokenKey = 0;
		$ordering = array();
		foreach ($parameterList as $parameterPosition => $parameter)
		{
			$this->label[$parameterPosition] = $parameter->getLabel();
			$ordering[$parameterPosition]    = count($parameter);
			foreach ($parameter as $valuePosition => $value)
			{
				$this->token[$tokenKey] = array(
					'value'     => $value,
					'parameter' => $parameterPosition
				);

				$this->tokenizedParameterList[$parameterPosition][$valuePosition] = $tokenKey;
				$tokenKey++;
			}
		}
		arsort($ordering);
		$this->ordering = array_keys($ordering);
	}

	/**
	 * @param $value1
	 * @param $value2
	 */
	private function createPair($value1, $value2)
	{
		$pair                             = array($value1, $value2);
		$this->tuples[$this->hash($pair)] = array(
			'value'    => $pair,
			'useCount' => 0
		);
	}

	private function foreachParameterPair(callable $callable)
	{
		foreach ($this->tokenizedParameterList as $parameterPosition1 => $parameter1)
		{
			foreach ($this->tokenizedParameterList as $parameterPosition2 => $parameter2)
			{
				if ($parameterPosition2 <= $parameterPosition1)
				{
					continue;
				}
				foreach ($parameter1 as $value1)
				{
					foreach ($parameter2 as $value2)
					{
						$callable($value1, $value2);
					}
				}
			}
		}
	}
}
