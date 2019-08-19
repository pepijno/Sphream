<?php
declare(strict_types=1);

namespace Sphream;

class Drop extends Method
{
	private $amount;
	private $uniqueId;

	public function __construct($amount, $uniqueId)
	{
		$this->amount = $amount;
		$this->uniqueId = $uniqueId;
	}

	public function beforeLoop()
	{
		return "\${$this->uniqueId}_i = 0;";
	}

	public function reduce($body)
	{
		return "if (\${$this->uniqueId}_i >= {$this->amount}) { $body } \${$this->uniqueId}_i++;";
	}
}
