<?php
namespace Tea\Collections\Tests\Mocks;

class JsonSerializeObject implements \JsonSerializable
{
	public function jsonSerialize()
	{
		return ['foo' => 'bar'];
	}
}