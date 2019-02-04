<?php
declare(strict_types=1);

use Sphream\ClosedSphream;
use Sphream\EmptySphream;
use Sphream\Sphream;

final class SphreamTest extends \PHPUnit\Framework\TestCase
{
	public function test_cannot_instantiate_Sphream_with_constructor()
	{
		$this->expectException(Error::class);
		new Sphream();
	}

	/**
	 * @dataProvider ofInvalidArgumentProvider
	 */
	public function test_if_of_throws_InvalidArgumentException_if_argument_is_not_iterable($input)
	{
		$this->expectException(InvalidArgumentException::class);
		Sphream::of($input);
	}

	public function ofInvalidArgumentProvider()
	{
		return [
			[ new Exception() ],
			[ 1 ],
			[ "Hello World" ]
		];
	}

	/**
	 * @dataProvider ofProvider
	 */
	public function test_if_of_creates_Sphream($toInput, $class)
	{
		$sphream = Sphream::of($toInput);
		$this->assertInstanceOf($class, $sphream);
	}

	public function ofProvider()
	{
		return [
			[ [1, 2], Sphream::class],
			[ (function () { yield 1; })(), Sphream::class],
		];
	}

	/**
	 * @dataProvider isEmptyProvider
	 */
	public function test_isEmpty_returns_if_Sphream_is_empty($ofInput, $expectedResult)
	{
		$sphream = Sphream::of($ofInput);
		$this->assertEquals($expectedResult, $sphream->isEmpty());
	}

	public function isEmptyProvider()
	{
		return [
			[ [], true ],
			[ [1, 2], false ],
			[ (function () { yield from []; })(), true],
			[ (function () { yield 1; })(), false],
		];
	}

	public function test_mempty_returns_empty_Sphream()
	{
		$sphream = Sphream::mempty();
		$this->assertTrue($sphream->isEmpty());
	}

	/**
	 * @dataProvider isClosedProvider
	 */
	public function test_if_isClosed_returns_if_Sphream_is_closed(Sphream $sphream, $expectedResult)
	{
		$this->assertEquals($expectedResult, $sphream->isClosed());
	}

	public function isClosedProvider()
	{
		$closedSphreamFromArray = Sphream::of([]);
		$closedSphreamFromArray->count();
		$unCountedSphreamFromArray = Sphream::of([]);
		$countedSphreamFromGenerator = Sphream::of((function () { yield from []; })());
		$countedSphreamFromGenerator->count();
		$unCountedSphreamFromGenerator = Sphream::of((function () { yield from []; })());
		return [
			[$closedSphreamFromArray, true],
			[$unCountedSphreamFromArray, false],
			[$countedSphreamFromGenerator, true],
			[$unCountedSphreamFromGenerator, false],
		];
	}

	public function test_if_close_closes_Sphream()
	{
		$sphream = Sphream::of([1, 2]);
		$sphream->close();
		$this->assertTrue($sphream->isClosed());
	}

	public function test_if_first_throws_ClosedSphream_on_closed_Sphream()
	{
		$sphream = Sphream::of([2])->close();
		$this->expectException(ClosedSphream::class);
		$sphream->first();
	}

	/**
	 * @dataProvider emptySphreamProvider
	 */
	public function test_if_first_throws_EmptySphream_from_empty_Sphream($ofInput)
	{
		$sphream = Sphream::of($ofInput);
		$this->expectException(EmptySphream::class);
		$sphream->first();
	}
	public function emptySphreamProvider()
	{
		$generator = function () {
			yield from [];
		};
		return [
			[ [] ],
			[ $generator() ],
		];
	}


	public function test_if_first_closes_Sphream()
	{
		$sphream = Sphream::of([1]);
		$sphream->first();
		$this->assertTrue($sphream->isClosed());
	}

	/**
	 * @dataProvider firstElementProvider
	 */
	public function test_if_first_returns_first_element_from_Sphream($toInput, $expected)
	{
		$sphream = Sphream::of($toInput);
		$this->assertEquals($expected, $sphream->first());
	}

	public function firstElementProvider()
	{
		return [
			[ ["a", "b", "5"], "a"],
			[ [83, 23523, 1], 83],
			[ ["Hello", new Exception(), "World"], "Hello"],
			[ [[], [1], [1, 2]], []],
			[ (function () {
				yield "a";
				yield "b";
				yield "5";
			})(), "a"],
			[ (function () {
				yield -99;
				yield 92137;
				yield 239;
			})(), -99],
			[ (function () {
				yield new Exception();
				yield new EmptySphream();
			})(), new Exception()],
			[ (function () {
				yield [];
				yield "ou";
				yield [[]];
			})(), []],
		];
	}

	public function test_if_last_throws_ClosedSphream_from_closed_Sphream()
	{
		$sphream = Sphream::of([2])->close();
		$this->expectException(ClosedSphream::class);
		$sphream->last();
	}

	/**
	 * @dataProvider emptySphreamProvider
	 */
	public function test_if_last_throws_EmptySphream_from_empty_Sphream($ofInput)
	{
		$sphream = Sphream::of($ofInput);
		$this->expectException(EmptySphream::class);
		$sphream->last();
	}

	public function test_if_last_closes_Sphream()
	{
		$sphream = Sphream::of([1]);
		$sphream->last();
		$this->assertTrue($sphream->isClosed());
	}

	/**
	 * @dataProvider lastElementProvider
	 */
	public function test_if_last_returns_last_element_from_Sphream($toInput, $expectedLast)
	{
		$sphream = Sphream::of($toInput);
		$this->assertEquals($expectedLast, $sphream->last());
	}

	public function lastElementProvider()
	{
		return [
			[ ["a", "b", "5"], "5"],
			[ [83, 23523, 1], 1],
			[ ["Hello", new Exception(), "World"], "World"],
			[ [[], [1], [1, 2]], [1, 2]],
			[ (function () {
				yield "a";
				yield "b";
				yield "5";
			})(), "5"],
			[ (function () {
				yield -99;
				yield 92137;
				yield 239;
			})(), 239],
			[ (function () {
				yield new Exception();
				yield new EmptySphream();
			})(), new EmptySphream()],
			[ (function () {
				yield [];
				yield "ou";
				yield [[]];
			})(), [[]]]
		];
	}

	public function test_if_count_throws_ClosedSphream_on_closed_Shpream()
	{
		$sphream = Sphream::of([2])->close();
		$this->expectException(ClosedSphream::class);
		$sphream->count();
	}

	/**
	 * @dataProvider countSizeProvider
	 */
	public function test_if_count_returns_size_of_Sphream($ofInput, $expectedSize)
	{
		$sphream = Sphream::of($ofInput);
		$this->assertEquals($expectedSize, $sphream->count());
	}

	public function countSizeProvider()
	{
		return [
			[ [], 0],
			[ [8], 1],
			[ [38, 182, 12, 12, 2], 5],
			[ (function () { yield from []; })(), 0],
			[ (function () {
				yield 1;
			})(), 1],
			[ (function () {
				yield 78;
				yield 234;
			})(), 2],
		];
	}

	public function test_if_count_closes_Sphream()
	{
		$sphream = Sphream::of([1]);
		$sphream->count();
		$this->assertTrue($sphream->isClosed());
	}

	public function test_if_toArray_throws_ClosedSphream_from_closed_Sphream()
	{
		$sphream = Sphream::of([2])->close();
		$this->expectException(ClosedSphream::class);
		$sphream->toArray();
	}

	/**
	 * @dataProvider toArrayProvider
	 */
	public function test_if_toArray_returns_empty_array_from_Sphream_created_form_empty_array($ofInput, $expectedArray)
	{
		$sphream = Sphream::of($ofInput);
		$this->assertEquals($expectedArray, $sphream->toArray());
	}

	public function toArrayProvider()
	{
		return [
			[ [], [] ],
			[ [7], [7] ],
			[ ["Bread", "crumbs"], ["Bread", "crumbs"] ],
			[ (function () { yield from []; })(), [] ],
			[ (function () {
				yield 7689;
				yield "Grant";
				yield new EmptySphream();
			})(), [7689, "Grant", new EmptySphream()] ],
		];
	}

	public function test_if_toArray_closes_Sphream()
	{
		$sphream = Sphream::of([1]);
		$sphream->toArray();
		$this->assertTrue($sphream->isClosed());
	}

	/**
	 * @dataProvider rangeExpectionProvider
	 */
	public function test_if_range_throws_InvalidArgumentException_if_first_argument_is_larger($from, $to)
	{
		$this->expectException(InvalidArgumentException::class);
		Sphream::range($from, $to);
	}

	public function rangeExpectionProvider()
	{
		return [
			[ 5, 4 ],
			[ 89, -14 ],
			[ -1242345234234, -988797897897897]
		];
	}

	/**
	 * @dataProvider rangeProvider
	 */
	public function test_if_range_returns_Sphream_with_correct_array($from, $to, $expectedArray)
	{
		$sphream = Sphream::range($from, $to);
		$this->assertEquals($expectedArray, $sphream->toArray());
	}

	public function rangeProvider()
	{
		return [
			[ 1, 2, [1] ],
			[ 67, 78, [67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77] ]
		];
	}

	/**
	 * @dataProvider repeatExceptionProvider
	 */
	public function test_if_repeat_throws_InvalidArgumentException_if_second_argument_is_negative($toRepeat, $amount)
	{
		$this->expectException(InvalidArgumentException::class);
		Sphream::repeat($toRepeat, $amount);
	}

	public function repeatExceptionProvider()
	{
		return [
			[ 'foo', -1 ],
			[ new Exception(), -2378940 ]
		];
	}

	/**
	 * @dataProvider repeatProvider
	 */
	public function tests_if_repeat_returns_Sphream_with_first_argument_repeated($toRepeat, $amount, $expectedArray)
	{
		$sphream = Sphream::repeat($toRepeat, $amount);
		$this->assertEquals($expectedArray, $sphream->toArray());
	}

	public function repeatProvider()
	{
		return [
			[ 'bar', 1, ['bar'] ],
			[ 987, 3, [ 987, 987, 987] ],
		];
	}

	public function test_if_generate_returns_Sphream()
	{
		$supplier = function () {
			return 4;
		};
		$sphream = Sphream::generate($supplier);
		$this->assertInstanceOf(Sphream::class, $sphream);
	}

	public function test_if_filter_throws_ClosedSphream_form_closed_Sphream()
	{
		$sphream = Sphream::of([3])->close();
		$this->expectException(ClosedSphream::class);
		$sphream->filter(function ($item) { return true; });
	}

	/**
	 * @dataProvider filterProvider
	 */
	public function test_if_filter_filters_elements_of_Sphream($ofInput, $filter, $expectedArray)
	{
		$sphream = Sphream::of($ofInput);
		$sphream->filter($filter);
		$this->assertEquals($expectedArray, $sphream->toArray());
	}

	public function filterProvider()
	{
		$allAllow = function ($item) {
			return true;
		};
		$allDeny = function ($item) {
			return false;
		};
		$filterOdd = function ($item) {
			return ($item % 2) == 0;
		};
		$generator = function () {
			yield 82;
			yield 93;
			yield 1;
		};
		return [
			[ [], $filterOdd, [] ],
			[ [2, 23, 5], $allAllow, [2, 23, 5] ],
			[ [782, 2, 1], $allDeny, [] ],
			[ [782, 91, 3, 5, 8], $filterOdd, [782, 8] ],
			[ $generator(), $allAllow, [82, 93, 1] ],
			[ $generator(), $filterOdd, [82] ]
		];
	}

	public function test_if_map_throws_ClosedSphream_form_closed_Sphream()
	{
		$sphream = Sphream::of([3])->close();
		$this->expectException(ClosedSphream::class);
		$sphream->map(function ($item) { return $item; });
	}

	/**
	 * @dataProvider mapProvider
	 */
	public function test_if_map_changes_elements_of_Sphream($ofInput, $map, $expectedArray)
	{
		$sphream = Sphream::of($ofInput);
		$sphream->map($map);
		$this->assertEquals($expectedArray, $sphream->toArray());
	}

	public function mapProvider()
	{
		$identity = function ($item) {
			return $item;
		};
		$addTwo = function ($item) {
			return $item + 2;
		};
		$generator = function () {
			yield -3;
			yield 97;
			yield 0;
		};
		return [
			[ [], $addTwo, [] ],
			[ [4, 5, 890], $identity, [4, 5, 890] ],
			[ [83, -12, 4], $addTwo, [85, -10, 6] ],
			[ $generator(), $identity, [-3, 97, 0] ],
			[ $generator(), $addTwo, [-1, 99, 2] ]
		];
	}

	public function test_if_take_throws_ClosedSphream_on_closed_Sphream()
	{
		$sphream = Sphream::of([1, 2])->close();
		$this->expectException(ClosedSphream::class);
		$sphream->take(1);
	}

	/**
	 * @dataProvider takeProvider
	 */
	public function test_if_take_takes_first_elements_of_Sphream($ofInput, $amount, $expectedArray)
	{
		$sphream = Sphream::of($ofInput);
		$sphream->take($amount);
		$this->assertEquals($expectedArray, $sphream->toArray());
	}

	public function takeProvider()
	{
		$generator = function () {
			yield "iow";
			yield 9;
			yield new Exception();
		};
		return [
			[ [], 10, [] ],
			[ [1, 2], 0, [] ],
			[ ["t", "c", "w", "p"], 2, ["t", "c"] ],
			[ $generator(), 0, [] ],
			[ $generator(), 2, ["iow", 9] ]
		];
	}

	public function test_if_drop_throws_ClosedSphream_form_closed_Sphream()
	{
		$sphream = Sphream::of([3])->close();
		$this->expectException(ClosedSphream::class);
		$sphream->drop(4);
	}

	/**
	 * @dataProvider dropProvider
	 */
	public function test_if_drop_removes_first_elements_of_Sphream($ofInput, $amount, $expectedArray)
	{
		$sphream = Sphream::of($ofInput);
		$sphream->drop($amount);
		$this->assertEquals($expectedArray, $sphream->toArray());
	}

	public function dropProvider()
	{
		$generator = function () {
			yield "iow";
			yield 9;
			yield new Exception();
		};
		return [
			[ [], 10, [] ],
			[ [1, 2], 0, [1, 2] ],
			[ ["t", "c", "w", "p"], 2, ["w", "p"] ],
			[ $generator(), 0, ["iow", 9, new Exception()] ],
			[ $generator(), 2, [new Exception()] ]
		];
	}

	public function test_if_dropWhile_throws_ClosedSphream_from_closed_Sphream()
	{
		$sphream = Sphream::of([])->close();
		$this->expectException(ClosedSphream::class);
		$sphream->dropWhile(function ($item) { return false; });
	}

	/**
	 * @dataProvider dropWhileProvider
	 */
	public function test_if_dropWhile_returns_correct_elements($ofInput, $callable, $expectedArray)
	{
		$sphream = Sphream::of($ofInput)->dropWhile($callable);
		$this->assertEquals($expectedArray, $sphream->toArray());
	}

	public function dropWhileProvider()
	{
		return [
			[ [], function ($item) { return $item > 5; }, [] ],
			[ [923, -1], function ($item) { return true; }, [] ],
			[ [-38, 28, 29], function ($item) { return false; }, [-38, 28, 29] ],
			[ [921, -4, -3 ], function ($item) { return $item > 0; }, [-4, -3] ],
			[ (function () {
				yield 892;
				yield 0;
			})(), function ($item) { return false; }, [892, 0] ]
		];
	}

	public function test_if_takeWhile_throws_ClosedSphream_from_closed_Sphream()
	{
		$sphream = Sphream::of([])->close();
		$this->expectException(ClosedSphream::class);
		$sphream->takeWhile(function ($item) { return false; });
	}

	/**
	 * @dataProvider takeWhileProvider
	 */
	public function test_if_takeWhile_returns_correct_elements($ofInput, $callable, $expectedArray)
	{
		$sphream = Sphream::of($ofInput)->takeWhile($callable);
		$this->assertEquals($expectedArray, $sphream->toArray());
	}

	public function takeWhileProvider()
	{
		return [
			[ [], function ($item) { return $item < 5; }, [] ],
			[ [923, -1], function ($item) { return true; }, [923, -1] ],
			[ [-38, 28, 29], function ($item) { return false; }, [] ],
			[ [921, -4, -3 ], function ($item) { return $item > 0; }, [921] ],
			[ (function () {
				yield 892;
				yield 0;
			})(), function ($item) { return true; }, [892, 0] ]
		];
	}
}
