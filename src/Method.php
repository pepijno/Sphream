<?php
declare(strict_types=1);

namespace Sphream;

abstract class Method
{
	public function beforeLoop()
	{
		return "";
	}

	public function afterLoop()
	{
		return "";
	}

	public abstract function reduce($body);
}
