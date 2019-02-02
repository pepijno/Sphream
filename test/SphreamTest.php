<?php
declare(strict_types=1);

use Sphream\EmptySphream;
use Sphream\Sphream;

final class SphreamTest extends \PHPUnit\Framework\TestCase
{
	public function testCannotInstantiateSphreamWithConstructor()
	{
		$this->expectException(Error::class);
		new Sphream();
	}

	public function testOfCreatesSphreamFromArray()
	{
		$sphream = Sphream::of([]);
		$this->assertInstanceOf(Sphream::class, $sphream);
	}

	public function testOfCreatesSphreamOfGenerator()
	{
		$generator = function () {
			yield 74;
		};
		$sphream = Sphream::of($generator());
		$this->assertInstanceOf(Sphream::class, $sphream);
	}

	public function testIfFirstReturnsStringFromSphreamCreatedFromArrayWithFirstElementString()
	{
		$sphream = Sphream::of(["a", "b", "5"]);
		$this->assertEquals("a", $sphream->first());
	}

	public function testIfFirstReturnsIntFromSphreamCreatedFromArrayWithFirstElementInt()
	{
		$sphream = Sphream::of([83, 23523, 1]);
		$this->assertEquals(83, $sphream->first());
	}

	public function testIfFirstThrowsEmptySphreamFromSphreamCreatedFromEmptyArray()
	{
		$sphream = Sphream::of([]);
		$this->expectException(EmptySphream::class);
		$sphream->first();
	}

	public function testIfFirstReturnsFirstGeneratedItemFromGeneratedFromSphreamCreatedFromGenerator()
	{
		$generator = function () {
			yield 74;
			yield 9;
			yield new Exception();
		};
		$sphream = Sphream::of($generator());
		$this->assertEquals(74, $sphream->first());
	}

	public function testIfFirstThrowsEmptySphreamFromSphreamCreatedFormEmptyGenerator()
	{
		$generator = function () {
			yield from [];
		};
		$sphream = Sphream::of($generator());
		$this->expectException(EmptySphream::class);
		$sphream->first();
	}

	public function testIfLastReturnsLastFromArrayUsedToCreateSphream()
	{
		$sphream = Sphream::of([52, 785, 582]);
		$this->assertEquals(582, $sphream->last());
	}

	public function testIfLastThrowsEmptySphreamFromSphreamCreatedFromEmptyArray()
	{
		$sphream = Sphream::of([]);
		$this->expectException(EmptySphream::class);
		$sphream->last();
	}

	public function testIfLastReturnsLastFromGeneratorUsedToCreateSphream()
	{
		$generator = function () {
			yield "a";
			yield new Exception();
			yield 8;
		};
		$sphream = Sphream::of($generator());
		$this->assertEquals(8, $sphream->last());
	}

	public function testIfLastThrowsEmptySphreamFromSphreamCreatedFromEmptyGenerator()
	{
		$generator = function () {
			yield from [];
		};
		$sphream = Sphream::of($generator());
		$this->expectException(EmptySphream::class);
		$sphream->last();
	}
}
