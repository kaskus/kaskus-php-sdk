<?php
namespace Kaskus\Client;

use GuzzleHttp\HandlerStack;

trait HasHandlerStackTrait
{
	private $handlerStack;

	public function getHandlerStack()
	{
		if (!$this->handlerStack) {
			$this->handlerStack = HandlerStack::create();
		}

		return $this->handlerStack;
	}
}
