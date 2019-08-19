<?php
declare(strict_types=1);

namespace Sphream;

class Filter extends Method
{
	private $pointer;

	public function __construct($pointer)
	{
		$this->pointer = $pointer;
	}

	public function reduce($body)
	{
		return "if (({$this->pointer})(\$item)) { {$body} }";
	}
}
