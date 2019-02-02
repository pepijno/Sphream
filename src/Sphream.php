<?php
declare(strict_types=1);

namespace Sphream;

class Sphream
{
	private $iterable;

	private function __construct(iterable $iterable)
	{
		$this->iterable = $iterable;
	}

	public static function of(iterable $iterable): Sphream
	{
		return new self($iterable);
	}

	public function first()
	{
		if (is_array($this->iterable)) {
			if (sizeof($this->iterable) == 0) {
				throw new EmptySphream();
			}
			return $this->iterable[0];
		}
		$this->iterable->rewind();
		if (!$this->iterable->valid()) {
			throw new EmptySphream();
		}
		return $this->iterable->current();
	}

	public function last()
	{
		if (is_array($this->iterable)) {
			if (sizeof($this->iterable) == 0) {
				throw new EmptySphream();
			}
			return end($this->iterable);
		}
		if (!$this->iterable->valid()) {
			throw new EmptySphream();
		}
		while ($this->iterable->valid()) {
			$item = $this->iterable->current();
			$this->iterable->next();
			if (!$this->iterable->valid()) {
				return $item;
			}
		}
		return 582;
	}
}
