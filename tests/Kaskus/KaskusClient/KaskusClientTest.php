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
	private $baseUri;

	public function setUp()
	{
		parent::setUp();

		$this->consumerKey = 'e4de10dc4e2aea8bc2a14534ac9adf';
		$this->consumerSecret = '18f1444557acf08bd6b6fa414cb1d8';
		$this->baseUri = 'https://webbranches-forum.kaskus.co.id/api/live/';
	}

	private function createObject()
	{
		return new KaskusClient(
			$this->consumerKey,
			$this->consumerSecret,
			$this->baseUri
		);
	}

	public function test_setCredentials_HasCorrectAttributeValue()
	{
		$tokenKey = 'callback';
		$tokenSecret = 'callback';

		$kaskusClient = $this->createObject();
		$kaskusClient->setCredentials($tokenKey, $tokenSecret);

		$this->assertAttributeEquals($tokenKey, 'tokenKey', $kaskusClient);
		$this->assertAttributeEquals($tokenSecret, 'tokenSecret', $kaskusClient);
	}

	public function test_getAuthorizeUrl_ReturnCorrectValue()
	{
		$this->baseUri = 'kaskus.id/oauth';
		$token = 'token';
		$expectedUrl = $this->baseUri . '/authorize?token=' . urlencode($token);

		$kaskusClient = $this->createObject();
		$result = $kaskusClient->getAuthorizeUrl($token);

		$this->assertEquals($expectedUrl, $result);
	}
}
