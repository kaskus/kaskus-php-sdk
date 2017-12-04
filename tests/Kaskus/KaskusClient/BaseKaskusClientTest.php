<?php
namespace Kaskus\Client;

use GuzzleHttp\Client;
use Kaskus\Client\ClientFactory;
use Kaskus\Client\OAuthFactory;
use Kaskus\General\Tests\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use \DynamicClass;


class BaseKaskusClientTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();
	}

	private function createObject()
	{
		$responseBody = new DynamicClass();
		$responseBody->getBody = function() {
			return '';
		};

		$response = $this->getMockWithoutConstructor(ResponseInterface::class);
		$response->method('getBody')->willReturn($responseBody);

		$client = $this->getMockWithoutConstructor(Client::class);
		$client->method('__call')->willReturn($response);

		$this->clientFactory = $this->getMockWithoutConstructor(ClientFactory::class);
		$this->clientFactory->method('create')->willReturn($client);

		$this->oauthFactory = $this->getMockWithoutConstructor(OAuthFactory::class);

		return new BaseKaskusClient(
			$this->clientFactory,
			$this->oauthFactory
		);
	}

	public function test_getRequestToken_ReturnCorrectValue()
	{
		$callback = 'asd';

		$baseKaskusClient = $this->createObject();
		$baseKaskusClient->getRequestToken($callback);
	}

	/**
	 * @dataProvider requestProvider
	 */
	public function test_request_ReturnCorrectInstance($requestFunction)
	{
		$uri = '/hot_thread';
		$options = ['options'];

		$baseKaskusClient = $this->createObject();
		$result = $baseKaskusClient->{$requestFunction}($uri, $options);

		$this->assertInstanceOf(ResponseInterface::class, $result);
	}

	public function requestProvider()
	{
		return [
			'get' => ['get'],
			'head' => ['head'],
			'put' => ['put'],
			'post' => ['post'],
			'patch' => ['patch'],
			'delete' => ['delete']
		];
	}

	/* public function test_getAccessToken_ReturnCorrectValue()
	{
		$token = 'token';

		$kaskusClient = $this->createObject();
		$kaskusClient->getAccessToken($token);
	} */

	/* public function test_handleException_ReturnCorrectValue()
	{
		$exception = $this->getMockWithoutConstructor(RequestException::class);

		$exceptionResponse = $this->getMockWithoutConstructor(ResponseInterface::class);
		$exceptionResponse->method('getStatusCode')->willReturn($this->expectedReturnCode);
		$exceptionResponse->method('getBody')->willReturn($expectedReturnCode);
		$exceptionResponse->method('json')->willReturn($this->expectedJson);
		$exception->method('getResponse')->willReturn($exceptionResponse);

		$kaskusClient = $this->createObject();
		$kaskusClient->handleException($exception);
	} */
}
