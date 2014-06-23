<?php

namespace GreenCape\AllPairs;

interface Reader
{
	public function getParameters();

	public function getSubModules();

	public function getConstraints();
}
