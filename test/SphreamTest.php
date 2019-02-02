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

	public function test_if_first_returns_string_from_Sphream_created_from_array_with_first_element_string()
	{
		$sphream = Sphream::of(["a", "b", "5"]);
		$this->assertEquals("a", $sphream->first());
	}

	public function test_if_first_returns_int_from_Sphream_created_from_array_with_first_element_int()
	{
		$sphream = Sphream::of([83, 23523, 1]);
		$this->assertEquals(83, $sphream->first());
	}

	public function test_if_first_throws_EmptySphream_from_Sphream_created_from_empty_array()
	{
		$sphream = Sphream::of([]);
		$this->expectException(EmptySphream::class);
		$sphream->first();
	}

	public function test_if_first_returns_first_generated_item_from_generated_from_Sphream_created_from_generator()
	{
		$generator = function () {
			yield 74;
			yield 9;
			yield new Exception();
		};
		$sphream = Sphream::of($generator());
		$this->assertEquals(74, $sphream->first());
	}

	public function test_if_first_throws_EmptySphream_from_Sphream_created_form_empty_generator()
	{
		$generator = function () {
			yield from [];
		};
		$sphream = Sphream::of($generator());
		$this->expectException(EmptySphream::class);
		$sphream->first();
	}

	public function test_if_last_returns_last_int_from_array_of_ints_used_to_create_Sphream()
	{
		$sphream = Sphream::of([52, 785, 582]);
		$this->assertEquals(582, $sphream->last());
	}

	public function test_if_last_returns_last_string_from_array_of_strings_used_to_create_Sphream()
	{
		$sphream = Sphream::of(["Hell", "o W", "orld!"]);
		$this->assertEquals("orld!", $sphream->last());
	}

	public function test_if_last_throws_EmptySphream_from_Sphream_created_from_empty_array()
	{
		$sphream = Sphream::of([]);
		$this->expectException(EmptySphream::class);
		$sphream->last();
	}

	public function test_if_last_returns_last_int_from_generator_of_ints_used_to_create_Sphream()
	{
		$generator = function () {
			yield 4;
			yield 91;
			yield 8;
		};
		$sphream = Sphream::of($generator());
		$this->assertEquals(8, $sphream->last());
	}

	public function test_if_last_returns_last_string_from_generator_of_trings_used_to_create_Sphream()
	{
		$generator = function () {
			yield "Existential";
			yield "Crab";
			yield "Bread";
		};
		$sphream = Sphream::of($generator());
		$this->assertEquals("Bread", $sphream->last());
	}

	public function test_if_last_throws_EmptySphream_from_Sphream_created_from_empty_generator()
	{
		$generator = function () {
			yield from [];
		};
		$sphream = Sphream::of($generator());
		$this->expectException(EmptySphream::class);
		$sphream->last();
	}

	public function test_if_count_returns_zero_with_Sphream_created_from_empty_array()
	{
		$sphream = Sphream::of([]);
		$this->assertEquals(0, $sphream->count());
	}

	public function test_if_count_returns_the_size_of_array_used_to_create_Sphream()
	{
		$sphream = Sphream::of([5, 876, 2]);
		$this->assertEquals(3, $sphream->count());
	}

	public function test_if_count_returns_zero_with_Sphream_created_from_empty_generator()
	{
		$generator = function () {
			yield from [];
		};
		$sphream = Sphream::of($generator());
		$this->assertEquals(0, $sphream->count());
	}

	public function test_if_count_returns_size_of_generator_used_to_create_Sphream()
	{
		$generator = function () {
			yield from [54, 323, 235];
		};
		$sphream = Sphream::of($generator());
		$this->assertEquals(3, $sphream->count());
	}

	public function test_if_toArray_returns_empty_array_from_Sphream_created_form_empty_array()
	{
		$sphream = Sphream::of([]);
		$this->assertEquals([], $sphream->toArray());
	}

	public function test_if_toArray_returns_empty_array_from_Sphream_created_from_empty_generator()
	{
		$generator = function () {
			yield from [];
		};
		$sphream = Sphream::of($generator());
		$this->assertEquals([], $sphream->toArray());
	}

	public function test_if_toArray_returns_array_used_to_create_Sphream()
	{
		$sphream = Sphream::of([432, 234, 2]);
		$this->assertEquals([432, 234, 2], $sphream->toArray());
	}

	public function test_if_toArray_returns_elements_form_generator_used_to_create_Sphream()
	{
		$generator = function () {
			yield from [432, 234, 2];
		};
		$sphream = Sphream::of($generator());
		$this->assertEquals([432, 234, 2], $sphream->toArray());
	}

	public function test_if_range_returns_EmptySphream_with_inputs_equal()
	{
		$sphream = Sphream::range(4, 4);
		$this->assertEquals(0, $sphream->count());
	}

	public function test_if_range_returns_Sphream_with_integers()
	{
		$sphream = Sphream::range(3, 9);
		$this->assertEquals([3, 4, 5, 6, 7, 8], $sphream->toArray());
	}

	public function test_if_range_throws_InvalidArgumentException_if_first_argument_is_larger()
	{
		$this->expectException(InvalidArgumentException::class);
		Sphream::range(5, 4);
	}

	public function test_if_repeat_throws_invalid_argument_if_second_argument_is_negative()
	{
		$this->expectException(InvalidArgumentException::class);
		Sphream::repeat('foo', -1);
	}

	public function tests_if_repeat_returns_Sphream_with_first_argument_repeated()
	{
		$sphream = Sphream::repeat('foo', 3);
		$this->assertEquals(['foo', 'foo', 'foo'], $sphream->toArray());
	}

	public function test_if_generate_returns_Sphream()
	{
		$supplier = function () {
			return 4;
		};
		$sphream = Sphream::generate($supplier);
		$this->assertInstanceOf(Sphream::class, $sphream);
	}
}
