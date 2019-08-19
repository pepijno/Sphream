<?php
declare(strict_types=1);

namespace Sphream;

class Map extends Method
{
	private $pointer;

	public function __construct($pointer)
	{
		$this->pointer = $pointer;
	}

	public function reduce($body)
	{
		return "\$item = ({$this->pointer})(\$item); {$body}";
	}
}
