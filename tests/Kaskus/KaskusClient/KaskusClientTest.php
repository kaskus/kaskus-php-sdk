<?php
namespace Kaskus\Client;

use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Kaskus\General\Tests\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use \DynamicClass;

class KaskusClientTest extends TestCase
{
	private $consumerKey;
	private $consumerSecret;
	private $baseUrl;

	public function setUp()
	{
		parent::setUp();

		$this->consumerKey = 'e4de10dc4e2aea8bc2a14534ac9adf';
		$this->consumerSecret = '18f1444557acf08bd6b6fa414cb1d8';
		$this->baseUrl = 'https://webbranches-forum.kaskus.co.id/api/live/';
	}

	private function createObject()
	{
		return new KaskusClient(
			$this->consumerKey,
			$this->consumerSecret,
			$this->baseUrl
		);
	}

	/* public function test_send_Return__()
	{
		$request = $this->getMockWithoutConstructor(RequestInterface::class);
		$request->method('getConfig')->will(
			$this->returnCallback(function(){
				$config = new DynamicClass();
				$config->get = function() {
					return true;
				};

				return $config;
			})
		);
		$kaskusClient = $this->createObject();
		$kaskusClient->send($request);

		$this->assertTrue(true);
		//$this->assertEquals($this->message, $this->exception->getErrorMessage());
	} */

	public function test_setCredentials_PassCorrectParameter()
	{
		$tokenKey = 'callback';
		$tokenSecret = 'callback';

		$kaskusClient = $this->createObject();
		$kaskusClient->setCredentials($tokenKey, $tokenSecret);
	}

	public function test_getAuthorizeUrl_ReturnCorrectValue()
	{
		$token = 'token';

		$kaskusClient = $this->createObject();
		$kaskusClient->getAuthorizeUrl($token);
	}

	/* public function test_addListener_ReturnCorrectValue()
	{
		$config = [];

		$baseKaskusClient = $this->createObject();
		$result = $baseKaskusClient->addListener($config);

		$this->assertInstanceOf(Oauth1::class, $result);
	} */

	/* public function test_removeAuthenticatedListener_ReturnCorrectValue()
	{
		$Oauth = $this->getMockWithoutConstructor(Oauth1::class);

		$kaskusClient = $this->createObject();
		$kaskusClient->removeAuthenticatedListener($Oauth);
	} */

	/* public function test_getAccessToken_ReturnCorrectValue()
	{
		$token = 'token';

		$kaskusClient = $this->createObject();
		$kaskusClient->getAccessToken($token);
	} */
}
