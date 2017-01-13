<?php
namespace Tea\Collections\Tests;

use Tea\Collections\Key;
use Tea\Collections\Forest;
use Tea\Collections\Collection;
/**
*
*/
class ForestTest extends TestCase
{
	protected function create($items = [], $pathSeparator = null)
	{
		return new Forest($items, $pathSeparator);
	}

	public function testKeyFor()
	{
		$items = [
			'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
			'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT', 'bumble-bee' => 'BUMBLE BEE']]
			];
		$pathSeparator = null;
		$forest = $this->create($items, $pathSeparator);
		$key = $forest->keyFor('friuts.apple');
		$this->assertInstanceOf('Tea\Collections\Key', $key);
		$this->assertEquals('friuts.apple', $key);

		$forest = $this->create($items, '--');
		$path = 'animals--insects--bumble-bee';
		$key = $forest->keyFor($path);
		$this->assertInstanceOf('Tea\Collections\Key', $key);
		$this->assertEquals($path, $key);

		$forest = $this->create($items);
		$path = new Key('animals.insects.bee');
		$key = $forest->keyFor($path);
		$this->assertInstanceOf('Tea\Collections\Key', $key);
		$this->assertEquals($path, $key);

	}

	public function getProvider()
	{
		return [
			[
				'ANT',
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'animals.insects.ant'
			],
			[
				'ANT',
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'animals~insects~ant',
				null,
				'~'
			],
			[
				'ANT',
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => new Collection(['insects' => ['bee' =>'BEE', 'ant' => 'ANT']])
				],
				'animals.insects.ant'
			],
			[
				'ANTS',
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'animals.insects.ants',
				'ANTS'
			],
			[
				'ANTS',
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'animals.insects.ants',
				function() { return 'ANTS'; }
			],

		];
	}

	/**
	 * @dataProvider getProvider
	 */
	public function testGet($expected, $items, $path, $default = null, $pathSeparator = null)
	{
		$forest = $this->create($items, $pathSeparator);
		$result = $forest->get($path, $default);
		$this->assertEquals($expected, $result);
	}


	public function forgetProvider()
	{
		return [
			[
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE']]
				],
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'animals.insects.ant'
			],
			[
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE']]
				],
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'animals~insects~ant',
				'~'
			],
			[
				[
					'friuts' => ['banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE']]
				],
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				['animals~insects~ant', 'friuts~apple'],
				'~'
			],
			[
				[
					'friuts' => ['banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE']]
				],
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				new Collection(['animals~insects~ant', 'friuts~apple']),
				'~'
			],
		];
	}

	/**
	 * @dataProvider forgetProvider
	 */
	public function testForget($expected, $items, $paths, $pathSeparator = null)
	{
		$forest = $this->create($items, $pathSeparator);
		$forest->forget($paths);
		$this->assertEquals($expected, $forest->all());
	}

	public function pullProvider()
	{
		return [
			[
				[ 'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]],
				['apple' => 'APPLE', 'banana' => 'BANANA'],
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'friuts'
			],
			[
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['ant' => 'ANT']]
				],
				'BEE',
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'animals~insects~bee',
				null,
				'~'
			],
			[
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'SPIDER',
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'animals.insects.spider',
				'SPIDER'
			],
		];
	}

	/**
	 * @dataProvider pullProvider
	 */
	public function testPull($final, $pulled, $items, $path, $default = null, $pathSeparator = null)
	{
		$f = $this->create($items, $pathSeparator);
		$result = $f->pull($path, $default);
		$this->assertEquals($pulled, $result);
		$this->assertEquals($final, $f->all());
	}

	public function offsetGetProvider()
	{
		return [
			[
				['apple' => 'APPLE', 'banana' => 'BANANA'],
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'friuts'
			],
			[
				'ANT',
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'animals~insects~ant',
				'~'
			],
		];
	}

	/**
	 * @dataProvider offsetGetProvider
	 */
	public function testOffsetGet($expected, $items, $path, $pathSeparator = null)
	{
		$forest = $this->create($items, $pathSeparator);
		$result = $forest[$path];
		$this->assertEquals($expected, $result);
	}

	/**
	 * @expectedException \Tea\Exceptions\KeyError
	 */
	public function testOffsetGetThrowsKeyError()
	{
		$forest = $this->create();
		$result = $forest['foo'];
	}


	public function offsetExistsProvider()
	{
		return [
			[
				true,
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'animals.insects.ant'
			],
			[
				true,
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'animals~insects~ant',
				'~'
			],
			[
				true,
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => new Collection(['insects' => ['bee' =>'BEE', 'ant' => 'ANT']])
				],
				'animals.insects.ant'
			],
			[
				false,
				[
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'animals.insects.spider'
			],
		];
	}

	/**
	 * @dataProvider offsetExistsProvider
	 */
	public function testOffsetExists($expected, $items, $path, $pathSeparator = null)
	{
		$f = $this->create($items, $pathSeparator);
		$result = isset($f[$path]);
		$this->assertEquals($expected, $result);
	}


	public function offsetSetProvider()
	{
		return [
			[
				[
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']],
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
				],
				[
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']]
				],
				'friuts',
				['apple' => 'APPLE', 'banana' => 'BANANA'],
			],
			[
				[
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']],
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
				],
				[
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']],
					'friuts' => ['mango' => 'mangos', 'berry' => 'berry'],
				],
				'friuts',
				['apple' => 'APPLE', 'banana' => 'BANANA'],
			],
			[
				[
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']],
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
					'foo'
				],
				[
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']],
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
				],
				null,
				'foo'
			],
			[
				[
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT', 'spider' => 'SPIDER']],
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
				],
				[
					'animals' => ['insects' => ['bee' =>'BEE', 'ant' => 'ANT']],
					'friuts' => ['apple' => 'APPLE', 'banana' => 'BANANA'],
				],
				'animals.insects.spider',
				'SPIDER'
			],
		];
	}

	/**
	 * @dataProvider offsetSetProvider
	 */
	public function testOffsetSet($expected, $items, $path, $value, $pathSeparator = null)
	{
		$f = $this->create($items, $pathSeparator);
		$f[$path] = $value;
		$this->assertEquals($expected, $f->all());
	}



}