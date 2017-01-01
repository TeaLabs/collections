<?php
namespace Tea\Collections\Tests;

use Tea\Collections\Key;

class KeyTest extends TestCase
{

	public function assertIsKey($object)
	{
		$this->assertInstanceOf("Tea\Collections\Key", $object);
	}

	public function make($segments = null, $separator = null)
	{
		return Key::make($segments, $separator);
	}

	public function createProvider()
	{
		return [
			[ [], null ],
			[ [], '' ],
			[ [], [], '~' ],
			[ ['foo', 'bar', 'baz'], 'foo.bar.baz'],
			[ ['foo', 'bar', 'baz'], 'foo.bar.baz', '.' ],
			[ ['foo', 'bar', 'baz'], ['foo', 'bar', 'baz'], null ],
			[ ['foo', 'bar', 'baz'], $this->make('foo~bar~baz', '~'), null, '~' ],
			[ ['foo', 'bar', 'baz'], $this->make('foo~bar~baz', '~'), '.', '.' ],
		];
	}

	/**
	 * @dataProvider createProvider()
	 */
	public function testCreate($expected, $segments = null, $separator = null, $expectedSeparator = null)
	{
		$key = new Key($segments, $separator);
		$this->assertIsKey($key);
		$this->assertEquals($expected, $key->segments());
		if(is_null($expectedSeparator))
			$expectedSeparator = $separator ?: Key::SEPARATOR;

		$this->assertEquals($expectedSeparator, $key->separator());
	}

	/**
	 * @dataProvider createProvider()
	 */
	public function testMake($expected, $segments = null, $separator = null, $expectedSeparator = null)
	{
		$key = Key::make($segments, $separator);
		$this->assertIsKey($key);
		$this->assertEquals($expected, $key->segments());
		if(is_null($expectedSeparator))
			$expectedSeparator = $separator ?: Key::SEPARATOR;

		$this->assertEquals($expectedSeparator, $key->separator());
	}

	public function testCast()
	{
		$object = $this->make('foo.bar');
		$key = Key::cast($object);
		$this->assertIsKey($key);
		$this->assertSame($object, $key);


		$object = 'foo.bar';
		$key = Key::cast($object);
		$this->assertIsKey($key);
		$this->assertEquals($object, (string) $key);

		$object = ['foo', 'bar'];
		$key = Key::cast($object);
		$this->assertIsKey($key);
		$this->assertEquals($object, $key->segments());

	}

	public function pathProvider()
	{
		return [
			[ '', null ],
			[ 'foo.bar.baz', 'foo.bar.baz', '.' ],
			[ 'foo~bar~baz', 'foo~bar~baz', '~' ],
			[ 'foo~bar~baz', ['foo', 'bar', 'baz'], '~' ],
		];
	}

	/**
	 * @dataProvider pathProvider()
	 */
	public function testPath($expected, $segments = null, $separator = null)
	{
		$key = $this->make($segments, $separator);
		$this->assertIsKey($key);
		$result = $key->path();
		$this->assertInternalType('string', $result);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @dataProvider pathProvider()
	 */
	public function testToString($expected, $segments = null, $separator = null)
	{
		$key = $this->make($segments, $separator);
		$this->assertIsKey($key);
		$result = (string) $key;
		$this->assertInternalType('string', $result);
		$this->assertEquals($expected, $result);
	}

	public function countProvider()
	{
		return [
			[ 2, 'foo~bar.baz', '~' ],
			[ 3, 'foo.bar.baz', '.' ],
		];
	}

	/**
	 * @dataProvider countProvider()
	 */
	public function testCount($expected, $segments = null, $separator = null)
	{
		$key = $this->make($segments, $separator);
		$this->assertIsKey($key);
		$result = $key->count();
		$this->assertInternalType('integer', $result);
		$this->assertEquals(count($key), $result);
		$this->assertEquals($expected, $result);
	}

	public function offsetExistsProvider()
	{
		return [
			[ true, 0, 'foo~bar.baz', '~' ],
			[ true, 1, 'foo~bar.baz', '~' ],
			[ true , 2, ['foo', 'bar', 'baz']],
			[ false, 2, 'foo~bar.baz', '~' ],
			[ false , 3, ['foo', 'bar', 'baz']],
		];
	}

	/**
	 * @dataProvider offsetExistsProvider()
	 */
	public function testOffsetExists($expected, $offset, $segments = null, $separator = null)
	{
		$key = $this->make($segments, $separator);
		$this->assertIsKey($key);
		$result = $key->offsetExists($offset);
		$this->assertInternalType('boolean', $result);
		$this->assertEquals(isset($key[$offset]), $result);
		$this->assertEquals($expected, $result);
	}

	public function offsetGetProvider()
	{
		return [
			[ 'foo.bar', 0, 'foo.bar~baz', '~' ],
			[ 'bar.baz', 1, 'foo~bar.baz', '~' ],
			[ 'baz' , 2, ['foo', 'bar', 'baz']],
		];
	}

	/**
	 * @dataProvider offsetGetProvider()
	 */
	public function testOffsetGet($expected, $offset, $segments = null, $separator = null)
	{
		$key = $this->make($segments, $separator);
		$this->assertIsKey($key);
		$result = $key->offsetGet($offset);
		$this->assertEquals($key[$offset], $result);
		$this->assertEquals($expected, $result);
	}

	public function segmentProvider()
	{
		return [
			[ 'foo.bar', 0, null,'foo.bar~baz', '~' ],
			[ 'bar', 1, null, 'foo.bar.baz' ],
			[ null, 5, null, 'foo.bar.baz' ],
			[ 'DEFAULT', 5, 'DEFAULT', 'foo.bar.baz' ],
			[ 'Closure', 5, function(){ return 'Closure'; }, 'foo.bar.baz' ]
		];
	}

	/**
	 * @dataProvider segmentProvider()
	 */
	public function testSegment($expected, $offset, $default = null, $segments = null, $separator = null)
	{
		$key = $this->make($segments, $separator);
		$this->assertIsKey($key);
		$result = $key->segment($offset, $default);
		$this->assertEquals($expected, $result);
	}

	public function testPop()
	{
		$key = $this->make('foo.bar.baz');

		$this->assertEquals(3, $key->count());

		$baz = $key->pop();
		$this->assertEquals('baz', $baz);
		$this->assertEquals(2, $key->count());

		$bar = $key->pop();
		$this->assertEquals('bar', $bar);
		$this->assertEquals(1, $key->count());
	}

	public function testShift()
	{
		$key = $this->make('foo.bar.baz');

		$this->assertEquals(3, $key->count());

		$foo = $key->shift();
		$this->assertEquals('foo', $foo);
		$this->assertEquals(2, $key->count());

		$bar = $key->shift();
		$this->assertEquals('bar', $bar);
		$this->assertEquals(1, $key->count());
	}

	public function testOffsetSet()
	{
		$key = $this->make('baz');
		$key->offsetSet(0, 'foo');
		$key->offsetSet(null, 'bar');
		$this->assertEquals(['foo', 'bar'], $key->segments());

		$key = $this->make('baz');
		$key[] = 'bar';
		$key[0] = 'foo';
		$this->assertEquals(['foo', 'bar'], $key->segments());
	}

	public function testOffsetUnset()
	{
		$key = $this->make('baz');
		$this->assertEquals(1, count($key));
		$key->offsetUnset(0);
		$this->assertEquals(0, count($key));
		$key[] = 'foo';
		$key[] = 'bar';
		$this->assertTrue($key->offsetExists(1));
		$key->offsetUnset(1);
		$this->assertFalse($key->offsetExists(1));


		$key = $this->make('baz');
		$this->assertEquals(1, count($key));
		unset($key[0]);
		$this->assertEquals(0, count($key));
		$key[] = 'foo';
		$key[] = 'bar';
		$this->assertTrue($key->offsetExists(1));
		unset($key[1]);
		$this->assertFalse($key->offsetExists(1));
	}

	public function testIteration()
	{
		$key = $this->make('foo.bar.baz');
		$results = [];
		foreach ($key as $k => $v){
			$results[$k] = $v;
			$this->assertEquals($key[$k], $v);
		}

		$this->assertEquals($key->segments(), $results);

	}

	public function sliceProvider()
	{
		return [
			[ ['foo', 'bar', 'baz'], 'foo.bar.baz' ],
			[ ['bar', 'baz'], 'foo.bar.baz', 1 ],
			[ ['bar'], 'foo.bar.baz', 1, 1],
			[ ['bar'], 'foo.bar.baz', -2, 1],
			[ [ 'foo', 'bar'], 'foo.bar.baz', 0, -1],
		];
	}

	/**
	 * @dataProvider sliceProvider()
	 */
	public function testSlice($expected, $segments, $offset = 0, $length = null, $separator = null)
	{
		$key = $this->make($segments, $separator);
		$result = $key->slice($offset, $length);
		$this->assertEquals($expected, $result);
	}


}