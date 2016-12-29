<?php
namespace Tea\Collections;

use Countable;
use ArrayAccess;
use Tea\Uzi\Uzi;
use ArrayIterator;
use IteratorAggregate;


class Key implements Countable, ArrayAccess, IteratorAggregate
{
	/**
	 * @var string
	 */
	const SEPARATOR = '.';

	/**
	 * @var array
	*/
	protected $segments;

	/**
	 * @var string
	*/
	protected $separator = '.';

	/**
	 * Instantiate the Key instance.
	 *
	 * @param  mixed  $segments
	 * @param  string $separator
	 * @return void
	 */
	public function __construct($segments = null, $separator = null)
	{
		$this->separator = (string) ($separator ?: self::SEPARATOR);

		if (is_null($segments))
			$this->segments = [];
		elseif(is_array($segments))
			$this->segments = $segments;
		elseif ($segments instanceof self){
			$this->segments = $segments->segments();
			if(is_null($separator))
				$this->separator = $segments->separator();
		}
		else
			$this->segments = static::parse($segments, $this->separator);
	}

	/**
	 * Get the path string of the key.
	 *
	 * @return string
	 */
	public function path()
	{
		return static::implode($this->segments, $this->separator);
	}

	/**
	 * Get the segment at the given offset of $default if not set.
	 *
	 * @param  mixed  $offset
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function segment($offset, $default = null)
	{
		if($this->offsetExists($offset))
			return $this->segments[$offset];

		return value($default);
	}

	/**
	 * Get the segments of the key.
	 *
	 * @return array
	 */
	public function segments()
	{
		return $this->segments;
	}

	/**
	 * Get the key's separator
	 *
	 * @return string
	 */
	public function separator()
	{
		return $this->separator;
	}

	/**
	 * Pop the last segment from the key and return it.
	 *
	 * @return string
	 */
	public function pop()
	{
		return array_pop($this->segments);
	}

	/**
	 * Shift the first segment from the key and return it.
	 *
	 * @return string
	 */
	public function shift()
	{
		return array_shift($this->segments);
	}

	/**
	 * Get the string version of the key.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->path();
	}

	/**
	 * Returns the number of segments in the key.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->segments());
	}

	/**
	 * Get an iterator for the key segments
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->segments());
	}


	/**
	 * Determine if a segment exists at an offset.
	 *
	 * @param  mixed  $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->segments);
	}

	/**
	 * Get the segment at a given offset.
	 *
	 * @param  mixed  $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->segments[$offset];
	}

	/**
	 * Set the segment at a given offset.
	 *
	 * @param  mixed  $offset
	 * @param  mixed  $segment
	 * @return void
	 */
	public function offsetSet($offset, $segment)
	{
		if (is_null($offset)) {
			$this->segments[] = (string) $segment;
		} else {
			$this->segments[$offset] = (string) $segment;
		}
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param  string  $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->segments[$offset]);
	}

	/**
	 * Create a new Key instance.
	 *
	 * @param  mixed  $segments
	 * @param  string $separator
	 * @return static
	 */
	public static function make($segments = null, $separator = null)
	{
		return new static($segments, $separator);
	}

	/**
	 * Parse a key path string to an array of it's segments
	 *
	 * @param  mixed  $key
	 * @param  string $separator
	 * @return static
	 */
	public static function cast($key = null, $separator = null)
	{
		return ($key instanceof self) ? $key : new static($key, $separator);
	}

	/**
	 * Parse a key path string to an array of it's segments
	 *
	 * @param  string $path
	 * @param  string $separator
	 * @return array
	 */
	public static function parse($path, $separator = self::SEPARATOR)
	{
		return $path != "" ? explode((string) $separator, (string) $path) : [];
	}

	/**
	 * Join the segments of a key into a string
	 *
	 * @param  array  $segments
	 * @param  string $separator
	 * @return string
	 */
	public static function implode($segments, $separator = self::SEPARATOR)
	{
		return (string) Uzi::join($separator, $segments, true);
	}

}