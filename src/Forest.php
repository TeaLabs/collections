<?php
namespace Tea\Collections;



class Forest extends Collection
{
	/**
	 * @var string
	*/
	protected $notation = '.';

	/**
	 * Instantiate the Forest instance.
	 *
	 * @param  mixed   $items
	 * @param  string  $notation
	 * @return void
	 */
	public function __construct($items = [], $notation = '.')
	{
		parent::__construct($items);
		$this->notation = $notation;
	}


	/**
	 * Create a new Forest instance if the value isn't one already.
	 *
	 * @param  mixed   $items
	 * @param  string  $notation
	 * @return static
	 */
	public static function make($items = [], $notation = '.')
	{
		return new static($items, $notation);
	}
}