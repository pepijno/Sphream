<?php
declare(strict_types=1);

namespace Sphream;

class First extends Method
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
		return "$body \${$this->uniqueId} = true; break;";
	}

	public function afterLoop()
	{
		return "if (!\${$this->uniqueId}) { throw new Sphream\EmptySphream(); }";
	}
}
