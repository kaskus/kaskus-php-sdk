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

		$this->consumerKey = 'e4de10dc4e4ac9a8bc2a14b6fa41df';
		$this->consumerSecret = '18f8bd62aea5314445574cbacf01d8';
		$this->baseUri = 'https://forum.kaskus.co.id/';
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
