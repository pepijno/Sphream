<?php
declare(strict_types=1);

namespace Sphream;

class IsEmpty extends Method
{
	public function reduce($body)
	{
		return "{$body} break;";
	}
}
