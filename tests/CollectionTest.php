<?php
namespace Tea\Collections\Tests;

use Tea\Collections\Collection;

class CollectionTest extends TestCase
{

	public function testCopy()
	{
		$collection = new Collection(['a', 'b', 'c']);
		$this->assertEquals($collection->all(), $collection->copy()->all());
	}

	public function testDefault()
	{
		$value = 'value';
		$collection = new Collection;
		$this->assertFalse($collection->has('foo'));
		$result = $collection->default('foo', $value);
		$this->assertEquals($value, $result);
		$this->assertTrue($collection->has('foo'));
		$this->assertEquals($value, $collection->get('foo'));

		$value = null;
		$collection = new Collection;
		$this->assertFalse($collection->has('foo'));
		$result = $collection->default('foo', $value);
		$this->assertEquals($value, $result);
		$this->assertTrue($collection->has('foo'));
		$this->assertEquals($value, $collection->get('foo', 'foo'));


		$value = 'value';
		$collection = new Collection(['foo' => $value]);
		$result = $collection->default('foo', 'foo');
		$this->assertEquals($value, $result);
	}

	public function testDiffAssoc()
	{
		$c = new Collection(['id' => 1, 'foo' => 'foo', 'bar' => 'baz', ]);
		$this->assertEquals(['id' => 1, 'foo' => 'foo'],
			$c->diffAssoc(new Collection(['f' => 'foo', 'bar' => 'baz', 'key' => 1]))->all());
	}

	public function testHas()
	{
		$c = new Collection(['id' => 1, 'foo' => 'foo', 'bar' => 'bar']);
		$keys = ['baz', 'key', 'id'];
		$this->assertFalse($c->has($keys));
		$this->assertTrue($c->has($keys, true));
		$this->assertTrue($c->has('foo'));
		$this->assertTrue($c->has(['id', 'bar']));
	}

	public function updateProvider()
	{
		return [
			[
				[
					'firstname' => 'Jane',
					'lastname' => 'Doe',
					'age' => 35,
					'contact' => [
						'address' => '254 Tea ST',
						'city' => 'foo'
					],
					'emails' => [
						'jane@mail.com'
					]
				],
				[
					'firstname' => 'John',
					'lastname' => 'Doe',
					'age' => 20,
					'contact' => [
						'address' => '234 Tea ST',
						'zip' => '234',
						'city' => 'Collections'
					],
					'emails' => [
						'doe@mail.com', 'johndoe@gmail.com'
					]
				],
				[
					'firstname' => 'Jane',
					'age' => 35,
					'contact' => [
						'address' => '254 Tea ST',
						'city' => 'foo'
					],
					'emails' => [
						'jane@mail.com'
					]
				]
			],
			[
				[
					'firstname' => 'Jane',
					'lastname' => 'Doe',
					'age' => 35,
					'contact' => [
						'address' => '254 Tea ST',
						'zip' => '234',
						'city' => 'Foo',
						'country' => 'Nation'
					],
					'emails' => [
						'doe@mail.com', 'janedoe@mail.com'
					]
				],
				[
					'firstname' => 'John',
					'lastname' => 'Doe',
					'age' => 20,
					'contact' => [
						'address' => '234 Tea ST',
						'zip' => '234',
						'city' => 'Collections'
					],
					'emails' => [
						'doe@mail.com', 'johndoe@gmail.com'
					]
				],
				[
					'firstname' => 'Jane',
					'age' => 35,
					'contact' => [
						'address' => '254 Tea ST',
						'city' => 'Foo',
						'country' => 'Nation'
					],
					'emails' => [
						1 => 'janedoe@mail.com'
					]
				], true
			],
		];
	}

	/**
	 * @dataProvider updateProvider
	 */
	public function testUpdate($expected, $initial, $items, $recursive = false)
	{
		$c = new Collection($initial);
		$result = $c->update($items, $recursive);
		$this->assertSame($c, $result);
		$this->assertEquals($expected, $result->all());
	}

	/**
	 * @dataProvider updateProvider
	 */
	public function testReplace($expected, $initial, $items, $recursive = false)
	{
		$c = new Collection($initial);
		$result = $c->replace($items, $recursive);
		$this->assertEquals($expected, $result->all());
	}

}