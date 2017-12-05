<?php
namespace Kaskus\Client;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Kaskus\Client\ClientFactory;
use Kaskus\Client\HasHandlerStackTrait;
use Kaskus\Client\OAuthFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class BaseKaskusClient
{
	const UNAUTHENTUCATED_STACK = 'unauthenticated';
	const AUTHENTUCATED_STACK = 'authenticated';

	use HasHandlerStackTrait;

	//todo: revert this
	//protected $baseUri = 'https://www.kaskus.co.id/api/oauth/';
	protected $baseUri = 'https://webbranches-forum.kaskus.co.id/api/live/';
	protected $consumerKey;
	protected $consumerSecret;

	protected $unauthenticatedOauthListener;
	protected $authenticatedOauthListener;

	public function __construct(
		ClientFactory $clientFactory,
		OAuthFactory $oauthFactory
	) {
		$this->oauthFactory = $oauthFactory;

		$this->addUnauthenticatedListener();

		$clientConfig = [
			'base_uri' => $this->baseUri,
			'handler' => $this->getHandlerStack(),
			'auth' => 'oauth',
			'headers' => [
				'Return-Type' => 'text/json'
			]
		];
		$this->client = $clientFactory->create($clientConfig);
	}

	public function get($uri, array $options = [])
	{
		$result = $this->client->get($uri, $options);
		return $result;
	}

	public function head($uri, array $options = [])
	{
		$result = $this->client->head($uri, $options);
		return $result;
	}

	public function put($uri, array $options = [])
	{
		$result = $this->client->put($uri, $options);
		return $result;
	}

	public function post($uri, array $options = [])
	{
		$result = $this->client->post($uri, $options);
		return $result;
	}

	public function patch($uri, array $options = [])
	{
		$result = $this->client->patch($uri, $options);
		return $result;
	}

	public function delete($uri, array $options = [])
	{
		$result = $this->client->delete($uri, $options);
		return $result;
	}

	public function send(RequestInterface $request, array $options = [])
	{
		try {
			$result = $this->client->send($request, $options);
		} catch (RequestException $e) {
			$this->handleException($e);
		}

		return $result;
	}

	public function getRequestToken($callback)
	{
		$options = [
			'query' => [
				'oauth_callback' => $callback
			]
		];

		$response = $this->client->get('token', $options);
		$tokenResponse = $response->getBody()->getContents();
		parse_str($tokenResponse, $requestToken);

		return $requestToken;
	}

	public function addUnauthenticatedListener()
	{
		$config = array(
			'consumer_key' => $this->consumerKey,
			'consumer_secret' => $this->consumerSecret,
			'token_secret' => null,
		);

		$this->unauthenticatedOAuthListener = $this->addListener(KaskusClient::UNAUTHENTUCATED_STACK, $config);
	}

	public function removeUnauthenticatedListener()
	{
		$this->removeListener(self::UNAUTHENTUCATED_STACK);
	}

	public function addAuthenticatedListener()
	{
		$config = array(
			'consumer_key' => $this->consumerKey,
			'consumer_secret' => $this->consumerSecret,
			'token' => $this->tokenKey,
			'token_secret' => $this->tokenSecret
		);

		$this->authenticatedOAuthListener = $this->addListener(self::AUTHENTUCATED_STACK, $config);

		$this->authenticatedOAuthListener;
	}

	public function removeAuthenticatedListener()
	{
		$this->removeListener(self::AUTHENTUCATED_STACK);
	}

	protected function addListener($identifier, $config)
	{
		$listener = $this->oauthFactory->create($config);
		$this->getHandlerStack()->push($listener, $identifier);

		return $listener;
	}

	protected function removeListener($identifier)
	{
		$this->getHandlerStack()->remove($identifier);
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
