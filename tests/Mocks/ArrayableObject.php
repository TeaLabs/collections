<?php
namespace Tea\Collections\Tests\Mocks;

use Tea\Contracts\General\Arrayable;

class ArrayableObject implements Arrayable
{
	public function toArray()
	{
		return ['foo' => 'bar'];
	}
}