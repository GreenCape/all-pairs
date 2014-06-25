<?php

namespace GreenCape\AllPairs;

class VardumpWriter implements  Writer
{
	public function write($result)
	{
		print_r($result);

		return $result;
	}
}
