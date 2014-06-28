<?php

namespace GreenCape\AllPairs;

interface Strategy
{
	/**
	 * @param Parameter[] $parameterList
	 * @param int         $order
	 *
	 * @return array
	 */
	public function combine($parameterList, $order = 2);
}
