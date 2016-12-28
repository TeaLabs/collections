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

		$this->set($key, $default);

		return $default;
	}

	/**
	 * Get the items in the collection that are not present in the given items.
	 *
	 * @param  mixed  $items
	 * @return static
	 */
	public function diffKey($items)
	{
		return new static(array_diff_key($this->items, $this->getArrayableItems($items)));
	}

	/**
	 * Replace items in the collection with new values and/or add new ones.
	 * It is similar to {@see \Tea\Collections\Collection::replace()} but
	 * updates items on the current instance instead.
	 *
	 * @uses   array_replace() when $recursive is FALSE or given.
	 * @uses   array_replace_recursive() when $recursive is TRUE.
	 *
	 * @param  mixed  $items
	 * @param  bool   $recursive
	 * @return $this
	 */
	public function extend($items, $recursive = false)
	{
		$items = $this->getArrayableItems($items);

		if($recursive)
			$this->items = array_replace_recursive($this->items, $items);
		else
			$this->items = array_replace($this->items, $items);

		return $this;
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
	 * Merge the collection with the given items. It is similar to
	 * {@see \Tea\Collections\Collection::update()} but instead of merging the
	 * new items into the current instance, it returns a new Collection with
	 * the merged items.
	 *
	 * @uses   \Tea\Collections\Collection::update()
	 *
	 * @param  mixed  $items
	 * @param  bool   $recursive
	 * @return static
	 */
	public function merge($items, $recursive = false)
	{
		return $this->copy()->update($items, $recursive);
	}

	/**
	 * Replace the items in the collection with new values. It is similar to
	 * {@see \Tea\Collections\Collection::extend()} but instead of replacing
	 * items in the current instance, it returns a new Collection with the
	 * replaced items.
	 *
	 * @uses   array_replace() when $recursive is FALSE or not given.
	 * @uses   array_replace_recursive() when $recursive is TRUE.
	 *
	 * @param  mixed  $items
	 * @param  bool   $recursive
	 * @return static
	 */
	public function replace($items, $recursive = false)
	{
		return $this->copy()->extend($items, $recursive);
	}

	/**
	 * Merge the collection with the given items. It is similar to
	 * {@see \Tea\Collections\Collection::merge()} but merges the new items into
	 * the current instance.
	 *
	 * @uses   array_merge() when $recursive is FALSE or not given
	 * @uses   array_merge_recursive() when $recursive is TRUE
	 *
	 * @param  mixed  $items
	 * @param  bool   $recursive
	 * @return $this
	 */
	public function update($items, $recursive = false)
	{
		$items = $this->getArrayableItems($items);

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