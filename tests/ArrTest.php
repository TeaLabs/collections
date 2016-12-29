<?php
namespace Tea\Collections\Tests;

use ArrayObject;
use Tea\Collections\Arr;
use Tea\Collections\Key;
use Tea\Collections\Forest;
use Tea\Collections\Collection;

use function Tea\Collections\key;
use function Tea\Collections\collect;

class ArrTest extends TestCase
{
	protected function usrColl()
	{
		$meta = [
			'tags' => ['furniture', 'office', 'tables' ],
			'ratings' => 4.5,
			'reviews' => [
				'john' => [ 'rating' => 5, 'comment' => 'Perfect :)' ],
				'jane' => [ 'rating' => 4, 'comment' => 'Like it.' ],
			]
		];

		$desk = collect(['price' => 150, 'name' => 'Desk', 'description' => 'Office Desk', 'meta' => $meta ]);
		$chair = collect(['price' => 100, 'name' => 'Chair', 'description' => 'Executive Office Chair', 'meta' => $meta]);
		$products = collect(['desk' => $desk->copy(), 'chair' => $chair->copy() ]);

		$address = collect(['p.o.box' => 'P.O. Box 234', 'zip' => '254',
			'street' => 'Collect St.', 'city' => 'Tea', 'country' => 'Kenya']);

		$john = collect(['name' => 'John Doe', 'email' => 'johndoe@mail.com',
			'address' => $address->copy(), 'products' => $products->copy()]);
		$jane = collect(['name' => 'Jane Doe', 'email' => 'janedoe@mail.com',
			'address' => $address->copy(), 'products' => $products->copy()]);
		$users = collect(['john' => $john->copy(), 'jane' => $jane->copy()]);

		return $users->copy();
	}

	public function levelProvider()
	{
		$jane = $this->usrColl()->pull('jane')->toArray();
		return [
			[
				[
					'name' => 'Jane Doe',
					'email' => 'janedoe@mail.com',
					'address.p.o.box' => 'P.O. Box 234',
					'address.zip' => '254',
					'address.street' => 'Collect St.',
					'address.city' => 'Tea',
					'address.country' => 'Kenya',
					'products.desk.price' => 150,
					'products.desk.name' => 'Desk',
					'products.desk.description' => 'Office Desk',
					'products.desk.meta.tags' => ['furniture', 'office', 'tables'],
					'products.desk.meta.ratings' => 4.5,
					'products.desk.meta.reviews.john.rating' => 5,
					'products.desk.meta.reviews.john.comment' => 'Perfect :)',
					'products.desk.meta.reviews.jane.rating' => 4,
					'products.desk.meta.reviews.jane.comment' => 'Like it.',
					'products.chair.price' => 100,
					'products.chair.name' => 'Chair',
					'products.chair.description' => 'Executive Office Chair',
					'products.chair.meta.tags' =>  ['furniture', 'office', 'tables'],
					'products.chair.meta.ratings' => 4.5,
					'products.chair.meta.reviews.john.rating' => 5,
					'products.chair.meta.reviews.john.comment' => 'Perfect :)',
					'products.chair.meta.reviews.jane.rating' => 4,
					'products.chair.meta.reviews.jane.comment' => 'Like it.',
				],
				$jane, true, null, null
			],
			[
				[
					'name' => 'Jane Doe',
					'email' => 'janedoe@mail.com',
					'address.p.o.box' => 'P.O. Box 234',
					'address.zip' => '254',
					'address.street' => 'Collect St.',
					'address.city' => 'Tea',
					'address.country' => 'Kenya',
					'products.desk.price' => 150,
					'products.desk.name' => 'Desk',
					'products.desk.description' => 'Office Desk',
					'products.desk.meta' => array(
						'tags' => ['furniture', 'office', 'tables'],
						'ratings' => 4.5,
						'reviews' => array (
							'john' => array ( 'rating' => 5, 'comment' => 'Perfect :)'),
							'jane' => array ('rating' => 4,	'comment' => 'Like it.')
							)
						),
					'products.chair.price' => 100,
					'products.chair.name' => 'Chair',
					'products.chair.description' => 'Executive Office Chair',
					'products.chair.meta' => array(
						'tags' => ['furniture', 'office', 'tables'],
						'ratings' => 4.5,
						'reviews' => array (
							'john' => array ( 'rating' => 5, 'comment' => 'Perfect :)'),
							'jane' => array ('rating' => 4,	'comment' => 'Like it.')
							)
						),

				],
				$jane, true, 3, null
			],
			[
				[
					'name' => 'Jane Doe',
					'email' => 'janedoe@mail.com',
					'address.p.o.box' => 'P.O. Box 234',
					'address.zip' => '254',
					'address.street' => 'Collect St.',
					'address.city' => 'Tea',
					'address.country' => 'Kenya',
					'products.desk.price' => 150,
					'products.desk.name' => 'Desk',
					'products.desk.description' => 'Office Desk',
					'products.desk.meta.tags.0' => 'furniture',
					'products.desk.meta.tags.1' => 'office',
					'products.desk.meta.tags.2' => 'tables',
					'products.desk.meta.ratings' => 4.5,
					'products.desk.meta.reviews.john.rating' => 5,
					'products.desk.meta.reviews.john.comment' => 'Perfect :)',
					'products.desk.meta.reviews.jane.rating' => 4,
					'products.desk.meta.reviews.jane.comment' => 'Like it.',
					'products.chair.price' => 100,
					'products.chair.name' => 'Chair',
					'products.chair.description' => 'Executive Office Chair',
					'products.chair.meta.tags.0' => 'furniture',
					'products.chair.meta.tags.1' => 'office',
					'products.chair.meta.tags.2' => 'tables',
					'products.chair.meta.ratings' => 4.5,
					'products.chair.meta.reviews.john.rating' => 5,
					'products.chair.meta.reviews.john.comment' => 'Perfect :)',
					'products.chair.meta.reviews.jane.rating' => 4,
					'products.chair.meta.reviews.jane.comment' => 'Like it.',
				],
				$jane, false, null, null
			],
			[
				[
					'name' => 'Jane Doe',
					'email' => 'janedoe@mail.com',
					'address~p.o.box' => 'P.O. Box 234',
					'address~zip' => '254',
					'address~street' => 'Collect St.',
					'address~city' => 'Tea',
					'address~country' => 'Kenya',
					'products~desk~price' => 150,
					'products~desk~name' => 'Desk',
					'products~desk~description' => 'Office Desk',
					'products~desk~meta~tags~0' => 'furniture',
					'products~desk~meta~tags~1' => 'office',
					'products~desk~meta~tags~2' => 'tables',
					'products~desk~meta~ratings' => 4.5,
					'products~desk~meta~reviews~john~rating' => 5,
					'products~desk~meta~reviews~john~comment' => 'Perfect :)',
					'products~desk~meta~reviews~jane~rating' => 4,
					'products~desk~meta~reviews~jane~comment' => 'Like it.',
					'products~chair~price' => 100,
					'products~chair~name' => 'Chair',
					'products~chair~description' => 'Executive Office Chair',
					'products~chair~meta~tags~0' => 'furniture',
					'products~chair~meta~tags~1' => 'office',
					'products~chair~meta~tags~2' => 'tables',
					'products~chair~meta~ratings' => 4.5,
					'products~chair~meta~reviews~john~rating' => 5,
					'products~chair~meta~reviews~john~comment' => 'Perfect :)',
					'products~chair~meta~reviews~jane~rating' => 4,
					'products~chair~meta~reviews~jane~comment' => 'Like it.',
				],
				$jane, false, null, '~'
			]
		];
	}

	/**
	 * @dataProvider levelProvider
	 */
	public function testLevel($expected, $array, $assocOnly = true, $depth = null, $separator = null)
	{
		$result = Arr::level($array, $assocOnly, $depth, $separator);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @dataProvider levelProvider
	 */
	public function testDot($expected, $array, $assocOnly = false, $depth = null, $separator = null)
	{
		$result = Arr::dot($array, $assocOnly, $depth, $separator);
		$this->assertEquals($expected, $result);
	}

	public function hasProvider()
	{
		$users = $this->usrColl();

		return [
			[ true, $users->copy(), 'jane'],
			[ true, $users->copy(), 'john.products.chair'],
			[ true, $users->copy(), key('john.products.chair')],
			[ true, $users->copy(), key('john~products~chair', '~')],
			[ true, $users->copy(), [ 'john.products.desk', key('john.products.chair'), key('john~address~city', '~'), key('jane')] ],
			[ false, $users->copy(), key('john.products.chairs')],
			[ false, $users->copy(), 'john~products~chair'],
			[ false, $users->copy(), ['john.products.desk', key('john.products.chair'), key('john~address~city', '~'), 'foo.bar'] ],
		];
	}

	/**
	 * @dataProvider hasProvider
	 */
	public function testHas($expected, $array, $keys)
	{
		$result = Arr::has($array, $keys);
		$this->assertInternalType('boolean', $result);
		$this->assertEquals($expected, $result);
	}

	public function testForget()
	{
		$users = $this->usrColl();

		$key = 'john.products.chair';
		$this->assertTrue(Arr::has($users, $key));
		Arr::forget($users, $key);
		$this->assertFalse(Arr::has($users, $key));

		$key = key('jane.products.desk');
		$this->assertTrue(Arr::has($users, $key));
		Arr::forget($users, $key);
		$this->assertFalse(Arr::has($users, $key));

		$key = key('jane~address~p.o.box', '~');
		$this->assertTrue(Arr::has($users, $key));
		Arr::forget($users, $key);
		$this->assertFalse(Arr::has($users, $key));

		$users = $this->usrColl();

		$key = [key('jane.products.desk'), key('jane~address~p.o.box', '~'), key('john')];
		$this->assertTrue(Arr::has($users, $key));
		Arr::forget($users, $key);
		$this->assertFalse(Arr::has($users, $key));
	}

	public function getProvider()
	{
		$usr = $this->usrColl();
		return [
			[ 'abc', ['a' => ['b' => [ 'c' => 'abc']]], 'a.b.c' ],
			[ 'b', ['a' => ['b' => [ 'c' => ['a', 'b','c'] ]]], key('a.b.c.1') ],
			[ 'abc', ['a' => ['b' => [ 'c' => 'abc']]], key('a.b.c') ],
			[ 'abc', ['a' => ['b' => [ 'c' => 'abc']]], key('a~b~c', '~') ],
			[ 100, $usr->copy(), key('john.products.chair.price') ],
			[ $usr['jane']['address'], $usr->copy(), key('jane.address') ],
			[ $usr['jane']['address']['p.o.box'], $usr->copy(), key('jane~address~p.o.box', '~') ],
			[ 'table', $usr->copy(), key('john.products.table'), 'table' ],
		];
	}

	/**
	 * @dataProvider getProvider
	 */
	public function testGet($expected, $array, $key, $default = null)
	{
		$result = Arr::get($array, $key, $default);
		$this->assertEquals($expected, $result);
	}

	public function setProvider()
	{
		$usr = $this->usrColl();
		return [
			[ ['a' => ['b' => [ 'c' => 'abc']]], 'a.b.c.d', 'abcd' ],
			[ ['a' => []], 'a.b.c.d', 'abcd' ],
			[ ['a' => []], key('a~b~c~d', '~'), 'abcd' ],
			[ ['a' => []], key('a~b~c~d', '~'), 'abcd',
				function($key){
					return ['foo','bar'];
				},
				'a.b.c', ['foo', 'bar', 'd' => 'abcd'] ],
		];
	}

	/**
	 * @dataProvider setProvider
	 */
	public function testSet($array, $key, $value, $resolver = null, $resolvedKey = null, $resolvedExpected = null)
	{
		Arr::set($array, $key, $value, $resolver);
		$this->assertTrue(Arr::has($array, $key));
		$this->assertEquals($value, Arr::get($array, $key));
		if(!is_null($resolvedKey)){
			$this->assertEquals($resolvedExpected, Arr::get($array, $resolvedKey));
		}
	}

}