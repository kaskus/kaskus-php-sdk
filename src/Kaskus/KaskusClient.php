<?php
namespace Kaskus\Client;

use GuzzleHttp\Exception\ClientException;
use Kaskus\Client\ClientFactory;
use Kaskus\Client\OAuth1Factory;
use Kaskus\Exceptions\KaskusServerException;
use Kaskus\Exceptions\ResourceNotFoundException;
use Kaskus\Exceptions\UnauthorizedException;

class KaskusClient extends BaseKaskusClient
{
	public function __construct(
		$consumerKey,
		$consumerSecret,
		$baseUri = null
	) {
		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;

		if (isset($baseUri)) {
			$this->baseUri = $baseUri;
		}

		$clientFactory = new ClientFactory();
		$OAuthFactory = new OAuth1Factory();

		parent::__construct($clientFactory, $OAuthFactory);
	}
}
