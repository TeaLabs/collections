<?php

namespace Tea\Collections;

use ArrayAccess;
use Traversable;
use JsonSerializable;
use Illuminate\Support\Arr as IlluminateArr;
use Illuminate\Support\Collection as IlluminateCollection;
use Illuminate\Contracts\Support\Jsonable as IlluminateJsonable;
use Illuminate\Contracts\Support\Arrayable as IlluminateArrayable;

class Arr extends IlluminateArr
{

	/**
	 * Flatten a multi-dimensional array with the keys joined by dots or a given
	 * separator.
	 *
	 * @param  array        $array
	 * @param  bool         $assocOnly
	 * @param  int|null     $depth
	 * @param  string|null  $separator
	 * @return array
	 */
	public static function dot($array, $assocOnly = false, $depth = null, $separator = null)
	{
		return static::level($array, $assocOnly, $depth, $separator);
	}

	/**
	 * Flatten a multi-dimensional array with the keys joined by dots or a given
	 * separator.
	 *
	 * @param  array        $array
	 * @param  bool         $assocOnly
	 * @param  int|null     $depth
	 * @param  string|null  $separator
	 * @return array
	 */
	public static function level($array, $assocOnly = true, $depth = null, $separator = null, $p = '')
	{
		if($array instanceof IlluminateCollection)
			$array = $array->all();

		if($assocOnly && !static::isAssoc($array))
			return $array;

		if(is_null($depth))
			$depth = INF;

		$sep = $separator ?: Key::SEPARATOR;

		$results = [];

		foreach ($array as $key => $value) {
			$value = $value instanceof IlluminateCollection ? $value->all() : $value;

			if(!is_array($value) || empty($value))
				$results[$p.$key] = $value;
			elseif($assocOnly && !static::isAssoc($value))
				$results[$p.$key] = $value;
			elseif($depth === 1)
				$results[$p.$key] = $value;
			else
				$results = array_merge($results, static::level($value, $assocOnly, $depth-1, $sep, $p.$key.$sep ));
		}

		return $results;
	}


	/**
	 * Determine if the given key exists in the provided array.
	 *
	 * @param  \ArrayAccess|array  $array
	 * @param  string|int  $key
	 * @return bool
	 */
	public static function exists($array, $key)
	{
		if ($array instanceof ArrayAccess) {
			return $array->offsetExists( (string) $key);
		}

		return array_key_exists( (string) $key, $array);
	}

	/**
	 * Remove one or many array items from a given array using "dot" notation.
	 *
	 * @param  array  $array
	 * @param  array|string  $keys
	 * @return void
	 */
	public static function forget(&$array, $keys)
	{
		$original = &$array;

		$keys = static::toArray($keys);

		if (count($keys) === 0) {
			return;
		}

		foreach ($keys as $key) {
			// if the exact key exists in the top-level, remove it
			if (static::exists($array, $key)) {
				unset($array[$key]);

				continue;
			}

			$parts = static::toKey($key);

			// clean up before each pass
			$array = &$original;

			while (count($parts) > 1) {
				$part = $parts->shift();

				if (static::exists($array, $part) && static::accessible($array[$part]))
					$array = &$array[$part];
				else
					continue 2;
			}

			unset($array[$parts->shift()]);
		}
	}

	/**
	 * Get an item from an array using "dot" notation.
	 *
	 * @param  \ArrayAccess|array  $array
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function get($array, $key, $default = null)
	{
		if (! static::accessible($array)) {
			return value($default);
		}

		if (is_null($key)) {
			return $array;
		}

		if (static::exists($array, $key)) {
			return $array[$key];
		}

		foreach (static::toKey($key) as $segment) {
			if (static::accessible($array) && static::exists($array, $segment)) {
				$array = $array[$segment];
			} else {
				return value($default);
			}
		}

		return $array;
	}

	/**
	 * Check if an item or items exist in an array using "dot" notation.
	 *
	 * @param  \ArrayAccess|array  $array
	 * @param  string|array  $keys
	 * @return bool
	 */
	public static function has($array, $keys)
	{
		if(is_null($keys) || !$array)
			return false;

		$keys = static::toArray($keys);

		if($keys === [])
			return false;

		foreach ($keys as $key) {
			$subKeyArray = $array;

			if (static::exists($array, $key))
				continue;

			foreach (static::toKey($key) as $segment) {
				if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment))
					$subKeyArray = $subKeyArray[$segment];
				else
					return false;
			}
		}

		return true;
	}

	/**
	 * Pluck an array of values from an array.
	 *
	 * @param  array  $array
	 * @param  string|array  $value
	 * @param  string|array|null  $key
	 * @return array
	 */
	public static function pluck($array, $value, $key = null)
	{
		$results = [];

		list($value, $key) = static::explodePluckParameters($value, $key);

		foreach ($array as $item) {
			$itemValue = data_get($item, $value);

			// If the key is "null", we will just append the value to the array and keep
			// looping. Otherwise we will key the array using the value of the key we
			// received from the developer. Then we'll return the final array form.
			if (is_null($key)) {
				$results[] = $itemValue;
			} else {
				$itemKey = data_get($item, $key);

				$results[$itemKey] = $itemValue;
			}
		}

		return $results;
	}

	/**
	 * Explode the "value" and "key" arguments passed to "pluck".
	 *
	 * @param  string|array  $value
	 * @param  string|array|null  $key
	 * @return array
	 */
	protected static function explodePluckParameters($value, $key)
	{
		$value = is_null($key) || is_array($value) ? $value : static::toKey($value)->segments();
		$key = is_null($key) || is_array($key) ? $key : static::toKey($key)->segments();
		return [$value, $key];
	}

	/**
	 * Set an array item to a given value using "dot" notation.
	 *
	 * If no key is given to the method, the entire array will be replaced.
	 *
	 * @param  array|\ArrayAccess           $array
	 * @param  string|\Tea\Collections\Key  $key
	 * @param  mixed                        $value
	 * @param  callable                     $default
	 * @return array|\ArrayAccess
	 */
	public static function set(&$array, $key, $value, callable $default = null)
	{
		if (is_null($key))
			return $array = $value;

		$keys = static::toKey($key);

		while (count($keys) > 1) {
			$key = $keys->shift();

			// If the key doesn't exist at this depth, we will just create an empty array
			// to hold the next value, allowing us to create the arrays to hold final
			// values at the correct depth. Then we'll keep digging into the array.
			if (! isset($array[$key]) || ! is_array($array[$key]))
				$array[$key] = is_null($default) ? [] : $default($key, $array);

			$array = &$array[$key];
		}

		$array[$keys->shift()] = $value;

		return $array;
	}

	/**
	 * Sort the array using the given callback or "dot" notation.
	 *
	 * @param  array  $array
	 * @param  callable|string  $callback
	 * @return array
	 */
	public static function sort($array, $callback)
	{
		return Collection::make($array)->sortBy($callback)->all();
	}

	/**
	 * Cast the given object to an array
	 *
	 * @param  mixed  $object
	 * @return array
	 */
	public static function toArray($object)
	{
		if(is_array($object))
			return $object;
		elseif(!is_object($object))
			return (array) $object;
		elseif ($object instanceof IlluminateCollection)
			return $object->all();
		elseif ($object instanceof IlluminateArrayable)
			return $object->toArray();
		elseif(method_exists($object, '__toString'))
			return [$object];
		elseif ($items instanceof IlluminateJsonable)
			return (array) json_decode($items->toJson(), true);
		elseif ($items instanceof JsonSerializable)
			return (array) $items->jsonSerialize();
		elseif ($object instanceof Traversable)
			return iterator_to_array($object);
		else
			return (array) $object;
	}

	/**
	 * Cast the given value to an instance of Key.
	 *
	 * @param  mixed $key
	 * @param  string $separator
	 * @return \Tea\Collections\Key
	 */
	public static function toKey($key = null, $separator = null)
	{
		return new Key($key, $separator);
	}
}
