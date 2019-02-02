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

	public static function range(int $from, int $to): Sphream
	{
		if ($from > $to) {
			throw new \InvalidArgumentException("First argument must be smaller than second argument");
		}
		$generator = function () use ($from, $to) {
			for ($i = $from; $i < $to; $i++) {
				yield $i;
			}
		};
		return self::of($generator());
	}

	public static function repeat($toRepeat, int $repeatAmount): Sphream
	{
		if ($repeatAmount < 0) {
			throw new \InvalidArgumentException("Amount to repeat cannot be negative");
		}
		$generator = function () use ($toRepeat, $repeatAmount) {
			for ($i = 0; $i < $repeatAmount; $i++) {
				yield $toRepeat;
			}
		};
		return new self($generator());
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
	}

	public function count(): int
	{
		if (is_array($this->iterable)) {
			return sizeof($this->iterable);
		}
		return iterator_count($this->iterable);
	}

	public function toArray(): array
	{
		if (is_array($this->iterable)) {
			return $this->iterable;
		}
		return iterator_to_array($this->iterable);
	}
}
