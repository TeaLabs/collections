<?php
namespace Tea\Collections;


use Tea\Contracts\General\Jsonable;
use Tea\Contracts\General\Arrayable;
use Illuminate\Support\Collection as IlluminateCollection;

class Collection extends IlluminateCollection implements Arrayable, Jsonable
{

	/**
	 * Return a shallow copy of the Collection.
	 *
	 * @return static
	 */
	public function copy()
	{
		return new static($this->items);
	}

	/**
	 * If key is in the collection, return its value.
	 * If not, insert key with the given default as value and return default.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function default($key, $default = null)
	{
		if($this->has($key))
			return $this->get($key);

		$this->put($key, $default);

		return $default;
	}

	/**
	 * Get the items in the collection that are not present in the given items
	 * by comparing both the values and the keys
	 *
	 * @param  mixed  $items
	 * @return static
	 */
	public function diffAssoc($items)
	{
		return new static(array_diff_assoc($this->items, $this->getArrayableItems($items)));
	}

	/**
	 * Create a new collection consisting of every n-th element.
	 *
	 * @param  int  $step
	 * @param  int  $offset
	 * @return static
	 */
	public function every($step, $offset = 0, $depreciatedValue = null)
	{
		$new = [];
		$position = 0;
		foreach ($this->items as $item) {
			if ($position % $step === $offset) {
				$new[] = $item;
			}
			$position++;
		}
		return new static($new);
	}

	/**
	 * Determine if all or any of the given items exists in the collection by
	 * key. $keys can be a single item key or an iterable of keys. If $keys is an
	 * iterable and $any is false or not given, will return true if all keys in
	 * $keys are exist in the collection and false otherwise. If $keys is an
	 * iterable and $any is true, will return true if at least one of the keys
	 * in $key exists in the collection and false otherwise.
	 *
	 * @param  mixed|iterable  $keys
	 * @param  bool            $any
	 * @return bool
	 */
	public function has($keys, $any = false)
	{
		foreach ($this->getArrayableItems($keys) as $key) {
			$exists = $this->offsetExists($key);
			if(($any && $exists) || (!$any && !$exists))
				return $exists;
		}
		return $any ? false : true;
	}

	/**
	 * Get a new collection with the items in the current replaced with the given
	 * items. This method is identical to {@see \Tea\Collections\Collection::update()}
	 * except it returns a new instance with the replaced items leaving the current
	 * untouched.
	 *
	 * @uses   \Tea\Collections\Collection::update()
	 *
	 * @param  mixed  $items
	 * @param  bool   $recursive
	 * @return static
	 */
	public function replace($items, $recursive = false)
	{
		return $this->copy()->update($items, $recursive);
	}

	/**
	 * Update the collection with another collection of items. If an item in the
	 * collection also exists in the new $items, it's value will be replaced by
	 * the value in the new $items. If an item exists in the new $items but not
	 * in the collection, it will be added to the collection as well.
	 *
	 * @see    \Tea\Collections\Collection::replace()
	 * @uses   array_replace() when $recursive is false (default).
	 * @uses   array_replace_recursive() when $recursive is true.
	 *
	 * @param  mixed  $items
	 * @param  bool   $recursive
	 * @return $this
	 */
	public function update($items, $recursive = false)
	{
		$items = $this->getArrayableItems($items);

		if($recursive)
			$this->items = array_replace_recursive($this->items, $items);
		else
			$this->items = array_replace($this->items, $items);

		return $this;
	}

	/**
	 * Results array of items from Collection or Arrayable.
	 *
	 * @param  mixed  $items
	 * @return array
	 */
	protected function getArrayableItems($items)
	{
		return Arr::toArray($items);
	}
}