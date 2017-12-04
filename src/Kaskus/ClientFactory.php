<?php
namespace Kaskus\Client;

use GuzzleHttp\Client;

class ClientFactory
{
	public function create($config)
	{
		$client = new Client($config);

		return $client;
	}
}
