<?php
namespace Kaskus\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Kaskus\Client\ClientFactory;
use Kaskus\Client\OAuthFactory;
use Kaskus\Exceptions\KaskusClientException;
use Kaskus\Exceptions\KaskusServerException;
use Kaskus\Exceptions\ResourceNotFoundException;
use Kaskus\Exceptions\UnauthorizedException;
use Kaskus\General\Tests\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use \DynamicClass;

class BaseKaskusClientTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();

		$this->request = $this->getMockWithoutConstructor(RequestInterface::class);

		$this->clientFactory = $this->getMockWithoutConstructor(ClientFactory::class);
		$this->client = $this->getMockWithoutConstructor(Client::class);

		$this->expectedStatus = 200;
		$this->expectedBody = '';
	}

	private function createObject()
	{
		$this->response = $this->createResponse($this->expectedStatus, $this->expectedBody);

		$this->client->method('__call')->willReturn($this->response);

		$this->clientFactory->method('create')->willReturn($this->client);

		$oauth = $this->getMockBuilder('object')->setMethods(['__invoke'])->getMock();

		$this->oauthFactory = $this->getMockWithoutConstructor(OAuthFactory::class);
		$this->oauthFactory->method('create')->willReturn($oauth);

		return new BaseKaskusClient(
			$this->clientFactory,
			$this->oauthFactory
		);
	}

	private function createResponse($statusCode, $bodyContents)
	{
		$responseBody = new DynamicClass();
		$responseBody->getContents = function() use ($bodyContents) {
			return $bodyContents;
		};

		$response = $this->getMockWithoutConstructor(ResponseInterface::class);
		$response->method('getBody')->willReturn($responseBody);
		$response->method('getStatusCode')->willReturn($statusCode);

		return $response;
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

	public function test_send_RequestInterface_Success_ReturnCorrectInstance()
	{
		$request = $this->request;
		$options = ['options'];

		$expectedStatus = 200;
		$expectedBody = 'body';
		$response = $this->createResponse($expectedStatus, $expectedBody);
		$this->client->method('send')->willReturn($response);

		$baseKaskusClient = $this->createObject();
		$result = $baseKaskusClient->send($request, $options);

		$this->assertInstanceOf(ResponseInterface::class, $result);
	}

	/**
	 * @dataProvider errorProvider
	 */
	public function test_send_HasError_ThrowException($errorCode, $exceptionClass, $errorBody)
	{
		$this->expectException($exceptionClass);
		$this->client->method('send')->will($this->returnCallback(
			function() use ($errorCode, $errorBody) {
				$body = $errorBody;
				$response = $this->createResponse($errorCode, json_encode($body));
				$exception = $this->getMockWithoutConstructor(RequestException::class);
				$exception->method('getResponse')->willReturn($response);
				throw $exception;
			}
		));

		$request = $this->request;
		$options = ['options'];

		$baseKaskusClient = $this->createObject();
		$baseKaskusClient->send($request, $options);
	}

	/**
	 * @dataProvider errorProvider
	 */
	/* public function test_send_HasRuntimeError_ThrowException($errorCode, $exceptionClass, $errorBody)
	{
		$this->expectException($exceptionClass);
		$this->client->method('send')->will($this->returnCallback(
			function() use ($errorCode, $errorBody) {
				$body = $errorBody;
				$response = $this->createResponse($errorCode, json_encode($body));
				$exception = $this->getMockWithoutConstructor(RequestException::class);
				$exception->method('getResponse')->willReturn($response);
				throw $exception;
			}
		));

		$request = $this->request;
		$options = ['options'];

		$baseKaskusClient = $this->createObject();
		$baseKaskusClient->send($request, $options);
	} */

	public function errorProvider()
	{
		return [
			'error401' => [401, UnauthorizedException::class, ['errormessage' => 'error']],
			'error404' => [404, ResourceNotFoundException::class, ['errormessage' => 'error']],
			'error404_noerror_message' => [404, KaskusServerException::class, []],
			'error405' => [405, KaskusClientException::class, ['errormessage' => 'error']],
			'error500' => [500, KaskusServerException::class, ['errormessage' => 'error']]
		];
	}

	public function test_getAccessToken_notSetCredentials_ThrowKaskusClientException()
	{
		$this->expectException(KaskusClientException::class);
		$token = 'token';

		$baseKaskusClient = $this->createObject();
		$baseKaskusClient->getAccessToken();
	}

	public function test_getAccessToken_setCredentials_ReturnCorrectValue()
	{
		$tokenKey = 'token';
		$tokenSecret = 'token_1234secret';

		$this->expectedBody = 'token=token';
		$expectedParamArr = [];
		parse_str($this->expectedBody, $expectedParamArr);

		$baseKaskusClient = $this->createObject();
		$baseKaskusClient->setCredentials($tokenKey, $tokenSecret);
		$result = $baseKaskusClient->getAccessToken();

		$this->assertEquals($expectedParamArr, $result);
	}
}
