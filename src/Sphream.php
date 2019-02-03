<?php
declare(strict_types=1);

namespace Sphream;

class Sphream
{
	private $iterable;
	private $closed;

	private function __construct(iterable $iterable)
	{
		$this->iterable = $iterable;
		$this->closed = false;
	}

	public static function of(iterable $iterable): Sphream
	{
		return new self($iterable);
	}

	public static function mempty(): Sphream
	{
		return new self([]);
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

	public static function generate(callable $supplier): Sphream
	{
		$generator = function () use ($supplier) {
			while (true) {
				yield $supplier();
			}
		};
		return new self($generator());
	}

	public function isEmpty()
	{
		if (is_array($this->iterable)) {
			return sizeof($this->iterable) == 0;
		}
		return !$this->iterable->valid();
	}

	public function isClosed()
	{
		return $this->closed;
	}

	public function close(): self
	{
		$this->closed = true;
		return $this;
	}

	public function first()
	{
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$this->close();
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
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$this->close();
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
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$this->close();
		if (is_array($this->iterable)) {
			return sizeof($this->iterable);
		}
		return iterator_count($this->iterable);
	}

	public function toArray(): array
	{
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$this->close();
		if (is_array($this->iterable)) {
			return $this->iterable;
		}
		return iterator_to_array($this->iterable);
	}

	public function filter(callable $filter): self
	{
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$iterable = $this->iterable;
		$this->iterable = (function () use ($iterable, $filter) {
			foreach ($iterable as $item) {
				if ($filter($item)) {
					yield $item;
				}
			}
		})();
		return $this;
	}

	public function map(callable $map): self
	{
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$iterable = $this->iterable;
		$this->iterable = (function () use ($iterable, $map) {
			foreach ($iterable as $item) {
				yield $map($item);
			}
		})();
		return $this;
	}

	public function take(int $amount): self
	{
		if ($this->closed) {
			throw new ClosedSphream();
		}
		$iterable = $this->iterable;
		$this->iterable = (function () use ($iterable, $amount) {
			$i = 0;
			foreach ($iterable as $item) {
				if ($i < $amount) {
					yield $item;
					$i++;
					continue;
				}
			}
		})();
		return $this;
	}

	public function drop(int $amount): self
	{
		if ($this->closed) {
			throw new ClosedSphream();
		}
		$iterable = $this->iterable;
		$this->iterable = (function () use ($iterable, $amount) {
			$i = 0;
			foreach ($iterable as $item) {
				if ($i < $amount) {
					$i++;
					continue;
				}
				yield $item;
			}
		})();
		return $this;
	}
}
