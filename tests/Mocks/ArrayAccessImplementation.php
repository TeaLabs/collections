<?php
namespace Tea\Collections\Tests\Mocks;


class ArrayAccessImplementation implements \ArrayAccess
{
	private $arr;

	public function __construct($arr)
	{
		$this->arr = $arr;
	}

	public function offsetExists($offset)
	{
		return isset($this->arr[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->arr[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->arr[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->arr[$offset]);
	}
}
