<?php
namespace Tea\Collections\Tests\Mocks;


class ToStringImplementation
{
	protected $foo = 'bar';

	public function __toString()
	{
		return "foo bar";
	}
}