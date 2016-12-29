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
	protected function userCollections()
	{
		$desk = collect(['price' => 150, 'name' => 'Desk', 'description' => 'Office Desk']);
		$chair = collect(['price' => 100, 'name' => 'Chair', 'description' => 'Executive Office Chair']);
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

	public function hasProvider()
	{
		$users = $this->userCollections();

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
		$users = $this->userCollections();

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

		$users = $this->userCollections();

		$key = [key('jane.products.desk'), key('jane~address~p.o.box', '~'), key('john')];
		$this->assertTrue(Arr::has($users, $key));
		Arr::forget($users, $key);
		$this->assertFalse(Arr::has($users, $key));
	}

}