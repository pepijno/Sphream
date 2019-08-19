<?php
declare(strict_types=1);

namespace Sphream;

class Sphream
{
	private $iterable;
	private $closed;
	private $callables;
	private $operations;

	private function __construct($iterable)
	{
		$this->iterable = $iterable;
		$this->closed = false;
		$this->callables = [];
		$this->operations = [];
	}

	public static function of($iterable): Sphream
	{
		if (!is_array($iterable) && !($iterable instanceof \Traversable)) {
			throw new \InvalidArgumentException("Input must be an array or an instance of a Traversable.");
		}
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
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$this->close();
		$this->operations[] = new IsEmpty();
		$value = true;
		$this->compile(function ($item) use (&$value) {
			$value = false;
		});
		return $value;
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
		$this->operations[] = new First("first" . sizeof($this->operations));
		$value;
		$this->compile(function ($item) use (&$value) {
			$value = $item;
		});
		return $value;
	}

	public function last()
	{
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$this->close();
		$this->operations[] = new Last("last" . sizeof($this->operations));
		$value;
		$this->compile(function ($item) use (&$value) {
			$value = $item;
		});
		return $value;
	}

	public function count(): int
	{
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$this->close();
		$total = 0;
		$this->compile(function ($item) use (&$total) {
			$total++;
		});
		return $total;
	}

	public function toArray(): array
	{
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$this->close();
		$array = [];
		$this->compile(function ($item) use (&$array) {
			$array[] = $item;
		});
		return $array;
	}

	public function filter(callable $filter): self
	{
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$pointer = "\$this->callables[" . sizeof($this->callables) . "]";
		$this->callables[] = $filter;
		$this->operations[] = new Filter($pointer);
		return $this;
	}

	public function map(callable $map): self
	{
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$pointer = "\$this->callables[" . sizeof($this->callables) . "]";
		$this->callables[] = $map;
		$this->operations[] = new Map($pointer);
		return $this;
	}

	public function take(int $amount): self
	{
		if ($this->closed) {
			throw new ClosedSphream();
		}
		$this->operations[] = new Take($amount, "take" . sizeof($this->operations));
		return $this;
	}

	public function drop(int $amount): self
	{
		if ($this->closed) {
			throw new ClosedSphream();
		}
		$this->operations[] = new Drop($amount, "drop" . sizeof($this->operations));
		return $this;
	}

	public function dropWhile(callable $callable): Sphream
	{
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$pointer = "\$this->callables[" . sizeof($this->callables) . "]";
		$this->callables[] = $callable;
		$this->operations[] = new DropWhile($pointer, "dropWhile" . sizeof($this->operations));
		return $this;
	}

	public function takeWhile(callable $callable): Sphream
	{
		if ($this->isClosed()) {
			throw new ClosedSphream();
		}
		$pointer = "\$this->callables[" . sizeof($this->callables) . "]";
		$this->callables[] = $callable;
		$this->operations[] = new TakeWhile($pointer, "takeWhile" . sizeof($this->operations));
		return $this;
	}

	private function compile(callable $callable)
	{
		$beforeLoop = array_reduce(array_reverse($this->operations), function ($body, $operation) {
			return "{$body} " . $operation->beforeLoop();
		});

		$afterLoop = array_reduce(array_reverse($this->operations), function ($body, $operation) {
			return "{$body} " . $operation->afterLoop();
		});

		$reduced = array_reduce(array_reverse($this->operations), function ($body, $operation) {
			return $operation->reduce($body);
		}, "\$callable(\$item);");

		$program = "{$beforeLoop} foreach (\$this->iterable as \$item) { {$reduced} } {$afterLoop}";

		eval($program);
	}
}
