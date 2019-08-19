<?php
declare(strict_types=1);

namespace Sphream;

class DropWhile extends Method
{
	private $pointer;
	private $uniqueId;

	public function __construct($pointer, $uniqueId)
	{
		$this->pointer = $pointer;
		$this->uniqueId = $uniqueId;
	}

	public function beforeLoop()
	{
		return "\${$this->uniqueId} = true;";
	}

	public function reduce($body)
	{
		return "if (\${$this->uniqueId} && !({$this->pointer})(\$item)) { \${$this->uniqueId} = false; } if (!\${$this->uniqueId}) { $body }";
	}
}
