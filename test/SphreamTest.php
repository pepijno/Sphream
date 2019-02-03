<?php
declare(strict_types=1);

use Sphream\EmptySphream;
use Sphream\Sphream;

final class SphreamTest extends \PHPUnit\Framework\TestCase
{
	public function test_cannot_instantiate_Sphream_with_constructor()
	{
		$this->expectException(Error::class);
		new Sphream();
	}

	public function test_of_creates_Sphream_from_array()
	{
		$sphream = Sphream::of([]);
		$this->assertInstanceOf(Sphream::class, $sphream);
	}

	public function test_of_creates_Sphream_of_generator()
	{
		$generator = function () {
			yield 74;
		};
		$sphream = Sphream::of($generator());
		$this->assertInstanceOf(Sphream::class, $sphream);
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

	/**
	 * @dataProvider firstElementFromArrayProvider
	 */
	public function test_if_first_returns_first_element_from_Sphream_created_from_array($array, $expected)
	{
		$sphream = Sphream::of($array);
		$this->assertEquals($expected, $sphream->first());
	}

	public function firstElementFromArrayProvider()
	{
		return [
			[ ["a", "b", "5"], "a"],
			[ [83, 23523, 1], 83],
			[ ["Hello", new Exception(), "World"], "Hello"],
			[ [[], [1], [1, 2]], []],
		];
	}

	/**
	 * @dataProvider firstElementFromGeneratorProvider
	 */
	public function test_if_first_returns_first_generated_item_from_generated_Sphream($generator, $expectedFirst)
	{
		$sphream = Sphream::of($generator());
		$this->assertEquals($expectedFirst, $sphream->first());
	}

	public function firstElementFromGeneratorProvider()
	{
		return [
			[ function () {
				yield "a";
				yield "b";
				yield "5";
			}, "a"],
			[ function () {
				yield -99;
				yield 92137;
				yield 239;
			}, -99],
			[ function () {
				yield new Exception();
				yield new EmptySphream();
			}, new Exception()],
			[ function () {
				yield [];
				yield "ou";
				yield [[]];
			}, []],
		];
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

	/**
	 * @dataProvider lastElementFromArrayProvider
	 */
	public function test_if_last_returns_last_element_from_array_used_to_create_Sphream($array, $expectedLast)
	{
		$sphream = Sphream::of($array);
		$this->assertEquals($expectedLast, $sphream->last());
	}

	public function lastElementFromArrayProvider()
	{
		return [
			[ ["a", "b", "5"], "5"],
			[ [83, 23523, 1], 1],
			[ ["Hello", new Exception(), "World"], "World"],
			[ [[], [1], [1, 2]], [1, 2]],
		];
	}

	/**
	 * @dataProvider lastElementFromGeneratorProvider
	 */
	public function test_if_last_returns_last_generated_item_from_Sphream()
	{
		$generator = function () {
			yield 4;
			yield 91;
			yield 8;
		};
		$sphream = Sphream::of($generator());
		$this->assertEquals(8, $sphream->last());
	}

	public function lastElementFromGeneratorProvider()
	{
		return [
			[ function () {
				yield "a";
				yield "b";
				yield "5";
			}, "5"],
			[ function () {
				yield -99;
				yield 92137;
				yield 239;
			}, 239],
			[ function () {
				yield new Exception();
				yield new EmptySphream();
			}, new EmptySphream()],
			[ function () {
				yield [];
				yield "ou";
				yield [[]];
			}, [[]]],
		];
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
}
