<?php

namespace GreenCape\AllPairs;

interface Strategy
{
	/**
	 * @param Reader $reader
	 *
	 * @return array
	 */
	public function combine(Reader $reader);
}
