<?php
namespace Tea\Collections\Tests\Mocks;


class IterableObject implements \IteratorAggregate
{
	public function getIterator()
	{
		return new \ArrayIterator(['foo' => 'bar']);
	}
}