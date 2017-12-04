<?php
namespace Kaskus\Client;

use GuzzleHttp\Subscriber\Oauth\Oauth1;

class OAuthFactory
{
	public function create($config)
	{
		$OAuth = new Oauth1($config);

		return $OAuth;
	}
}
