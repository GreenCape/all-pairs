<?php

namespace GreenCape\AllPairs;

interface Reader
{
	/**
	 * @param string $labelDelimiter
	 * @param string $valueDelimiter
	 *
	 * @return Parameter[]
	 */
	public function getParameters();

	public function getSubModules();

	public function getConstraints();
}
