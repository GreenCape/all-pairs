<?php

namespace GreenCape\AllPairs;

interface Strategy
{
	/**
	 * @param $parameterDefinition
	 *
	 * @return array
	 */
	public function combine($parameterDefinition);
}
