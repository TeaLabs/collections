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
	 * Determine if an any of item exists in the collection.
	 *
	 * @param  iterable  $keys
	 * @return bool
	 */
	public function hasAny($keys)
	{
		foreach ($keys as $key)
			if($this->has($key))
				return true;

		return false;
	}

	/**
	 * Get a new collection with the items in the current merged with the given
	 * items. This method is identical to {@see \Tea\Collections\Collection::update()}
	 * except it creates a new instance with the merged items leaving the current
	 * untouched.
	 *
	 * @see   \Tea\Collections\Collection::update()
	 *
	 * @param  mixed  $items
	 * @param  bool   $strict
	 * @param  bool   $recursive
	 * @return static
	 */
	public function merge($items, $strict = false, $recursive = false)
	{
		return $this->copy()->update($items, $strict, $recursive);
	}

	/**
	 * Update the collection with another collection of items. If an item in the
	 * collection also exists in the new $items, it's value will be replaced by
	 * the value in the new $items. If an item exists in the new $items but not
	 * in the collection, it will be added to the collection as well.
	 *
	 * By default, only the items with string keys are updated. Those of numeric
	 * keys, are instead appended to the collection with (possibly) new key(s)
	 * even if the original collection contained item(s) with the same key.
	 * However, if $strict is passed as true, numerically indexed items will also
	 * be updated.
	 *
	 * Also, {@see \Tea\Collections\Collection::merge()}.
	 *
	 * When $strict is false or not given:
	 * @uses   array_merge() when $recursive is false (default)
	 * @uses   array_merge_recursive() when $recursive is true
	 *
	 * When $strict is true:
	 * @uses   array_replace() when $recursive is false (default)
	 * @uses   array_replace_recursive() when $recursive is true
	 *
	 * @param  mixed  $items
	 * @param  bool   $strict
	 * @param  bool   $recursive
	 * @return $this
	 */
	public function update($items, $strict = false, $recursive = false)
	{
		$items = $this->getArrayableItems($items);

		if($strict)
			if($recursive)
				$this->items = array_replace_recursive($this->items, $items);
			else
				$this->items = array_replace($this->items, $items);
		else
			if($recursive)
				$this->items = array_merge_recursive($this->items, $items);
			else
				$this->items = array_merge($this->items, $items);

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