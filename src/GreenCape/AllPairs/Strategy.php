<?php

namespace GreenCape\AllPairs;

interface Strategy
{
	/**
	 * @param Reader $parameterDefinition
	 *
	 * @return array
	 */
	public function combine(Reader $parameterDefinition);
}
