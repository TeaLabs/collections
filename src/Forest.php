<?php
namespace Tea\Collections;



class Forest extends Collection
{
	/**
	 * @var string
	*/
	protected $pathSeparator;

	/**
	 * Instantiate the Forest instance.
	 *
	 * @param  mixed   $items
	 * @param  string  $pathSeparator
	 * @return void
	 */
	public function __construct($items = [], $pathSeparator = null)
	{
		parent::__construct($items);
		$this->pathSeparator = (string) ($pathSeparator ?: Key::SEPARATOR);
	}


	/**
	 * Create a new Forest instance if the value isn't one already.
	 *
	 * @param  mixed   $items
	 * @param  string  $pathSeparator
	 * @return static
	 */
	public static function make($items = [], $pathSeparator = null)
	{
		return new static($items, $pathSeparator);
	}

	/**
	 * Get the Key instance for the given path.
	 *
	 * @param  mixed $path
	 * @return \Tea\Collections\Key
	 */
	public function keyFor($path)
	{
		return new Key($path, $this->pathSeparator);
	}

	/**
	 * Get the path separator.
	 *
	 * @return string
	 */
	public function getPathSeparator()
	{
		return $this->pathSeparator;
	}

	/**
	 * Set the path separator.
	 *
	 * @param  string $separator
	 * @return $this
	 */
	public function setPathSeparator($separator)
	{
		$this->pathSeparator = (string) $separator;

		return $this;
	}

	/**
	 * Remove an item from the collection by key.
	 *
	 * @param  string|array  $keys
	 * @return $this
	 */
	public function forget($keys)
	{
		foreach ($this->getArrayableItems($keys) as $key)
			$this->offsetUnset($key);

		return $this;
	}

	/**
	 * Get an item from the collection by path.
	 *
	 * @param  mixed  $path
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function get($path, $default = null)
	{
		return Arr::get($this->items, $this->keyFor($key), $default);
	}


	/**
	 * Determine if an item exists at an offset.
	 *
	 * @param  mixed  $path
	 * @return bool
	 */
	public function offsetExists($path)
	{
		return Arr::has($this->items, $this->keyFor($path));
	}

	/**
	 * Get an item at a given offset.
	 *
	 * @param  mixed  $path
	 * @return mixed
	 */
	public function offsetGet($path)
	{
		return $this->get($path, function() use($path) {
			throw KeyError::create($path, $this);
		});
	}

	/**
	 * Set the item at a given offset.
	 *
	 * @param  mixed  $path
	 * @param  mixed  $value
	 * @return void
	 */
	public function offsetSet($path, $value)
	{
		if (is_null($path)) {
			$this->items[] = $value;
		} else {
			Arr::set($this->items, $this->keyFor($path), $value);
		}
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function offsetUnset($path)
	{
		Arr::forget($this->keyFor($path));
	}


}