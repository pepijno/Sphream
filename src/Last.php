<?php
declare(strict_types=1);

namespace Sphream;

class Last extends Method
{
	private $uniqueId;

	public function __construct($uniqueId)
	{
		$this->uniqueId = $uniqueId;
	}

	public function beforeLoop()
	{
		return "\${$this->uniqueId} = false;";
	}

	public function reduce($body)
	{
		return "$body \${$this->uniqueId} = true;";
	}

	public function afterLoop()
	{
		return "if (!\${$this->uniqueId}) { throw new Sphream\EmptySphream(); }";
	}
}
