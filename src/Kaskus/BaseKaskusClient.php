<?php
namespace Kaskus\Client;

use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Kaskus\Client\ClientFactory;
use Kaskus\Client\HasHandlerStackTrait;
use Kaskus\Client\OAuthFactory;
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

class BaseKaskusClient
{
	use HasHandlerStackTrait;

	//protected $baseUrl = 'https://www.kaskus.co.id/api/oauth/';
	protected $baseUrl = 'https://webbranches-forum.kaskus.co.id/api/live/';
	protected $consumerKey;
	protected $consumerSecret;

	public function __construct(
		ClientFactory $clientFactory,
		OAuthFactory $oauthFactory
	) {
		$this->oauthFactory = $oauthFactory;

		$clientConfig = [
			'base_url' => $this->baseUrl,
			'defaults' => array(
				'auth' => 'oauth',
				'headers' => array(
					'Return-Type' => 'text/json'
				)
			),
			'handler' => $this->getHandlerStack()
		];
		$this->client = $clientFactory->create($clientConfig);
	}

	public function get($uri, array $options = [])
	{
		try {
			$result = $this->client->get($uri, $options);
		} catch (Exception $e) {
			$this->handleException($e);
		}

		return $result;
	}

	public function head($uri, array $options = [])
	{
		return $this->client->head($uri, $options);
	}

	public function put($uri, array $options = [])
	{
		return $this->client->put($uri, $options);
	}

	public function post($uri, array $options = [])
	{
		return $this->client->post($uri, $options);
	}

	public function patch($uri, array $options = [])
	{
		return $this->client->patch($uri, $options);
	}

	public function delete($uri, array $options = [])
	{
		return $this->client->delete($uri, $options);
	}

	public function send(RequestInterface $request, array $options = [])
	{
		try {
			$result = $this->client->send($request, $options);
		} catch (RequestException $e) {
			$this->handleException($e);
		}
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

	protected function addListener($config)
	{
		$listener = $this->oauthFactory->create($config);
		$this->getHandlerStack()->push($listener);

		return $listener;
	}

	protected function removeListener(OAuth1 $listener)
	{
		$this->getHandlerStack()->remove($listener);
	}
}
