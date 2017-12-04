<?php

namespace Kaskus\General\Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
	public function getMockWithoutConstructor($class)
	{
		return $this->createMock($class);
	}
}
