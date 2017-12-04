<?php
namespace Kaskus\Client;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Kaskus\Client\ClientFactory;
use Kaskus\Client\OAuthFactory;
use Kaskus\Exceptions\KaskusClientException;
use Kaskus\Exceptions\KaskusServerException;
use Kaskus\Exceptions\ResourceNotFoundException;
use Kaskus\Exceptions\UnauthorizedException;

class KaskusClient extends BaseKaskusClient
{
	protected $unauthenticatedOauthListener;
	protected $authenticatedOauthListener;

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

		$cientFactory = new ClientFactory();
		$OAuthFactory = new OAuthFactory();

		parent::__construct($cientFactory, $OAuthFactory);
	}

	public function setCredentials($tokenKey, $tokenSecret)
	{
		$this->tokenKey = $tokenKey;
		$this->tokenSecret = $tokenSecret;

		$this->removeUnauthenticatedListener();
		$this->addAuthenticatedListener();
	}

	public function getAuthorizeUrl($token)
	{
		$url = $this->baseUri . '/authorize?token=' . urlencode($token);
		return $url;
	}

	public function getAccessToken()
	{
		if (!$this->authenticatedOauthListener) {
			throw new KaskusClientException('You have to set credentials with authorized request token!');
		}

		$response = $this->client->get('accesstoken');
		$tokenResponse = $response->getBody()->getContents();
		parse_str($tokenResponse, $accessToken);

		return $accessToken;
	}

	protected function handleException(RequestException $exception)
	{
		$response = $exception->getResponse();
		$statusCode = $response->getStatusCode();

		if ($statusCode >= 500) {
			$bodyContent = $response->getBody()->getContents();
			throw new KaskusServerException(print_r($bodyContent, true), $statusCode);
		}

		try {
			$error = $response->json();
		} catch (\RuntimeException $e) {
			throw new KaskusServerException($e->getMessage());
		}

		if (isset($error['errormessage'])) {
			$errorMessage = $error['errormessage'];

			if ($statusCode === 401) {
				throw new UnauthorizedException($errorMessage);
			} elseif ($statusCode === 404) {
				throw new ResourceNotFoundException();
			}
			throw new KaskusClientException($errorMessage);
		}

		throw new KaskusServerException(print_r($error, true), $statusCode);
	}
}
