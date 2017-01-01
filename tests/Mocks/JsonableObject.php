<?php
namespace Tea\Collections\Tests\Mocks;

use Tea\Contracts\General\Jsonable;

class JsonableObject implements Jsonable
{
	public function toJson($options = 0)
	{
		return '{"foo":"bar"}';
	}
}