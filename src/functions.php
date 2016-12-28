<?php
namespace Tea\Collections;

/**
 * Create a new Collection instance with the given items.
 *
 * @param  array  $items
 * @return \Tea\Collections\Collection
 */
function collect($items = [])
{
	return new Collection($items);
}

/**
 * Create a new Key instance.
 *
 * @param  mixed $segments
 * @param  string $separator
 * @return \Tea\Collections\Key
 */
function key($segments = null, $separator = null)
{
	return new Key($segments, $separator);
}