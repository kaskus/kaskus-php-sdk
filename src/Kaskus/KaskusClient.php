<?php
namespace Kaskus;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Kaskus\Exceptions\KaskusClientException;
use Kaskus\Exceptions\KaskusServerException;
use Kaskus\Exceptions\ResourceNotFoundException;
use Kaskus\Exceptions\UnauthorizedException;
use Psr\Http\Message\ResponseInterface;

class KaskusClient extends \GuzzleHttp\Client
{

    const BASE_URL = 'https://www.kaskus.co.id/api/oauth/';
    const OAUTH_HANDLER_NAME = 'oauth1_handler';

    /**
     * @var array
     */
    protected $oauthConfig;

    protected $unauthenticatedOauthListener;

    protected $authenticatedOauthListener;

    private $baseUrl;

    protected $handlerStack;

    public function __construct($consumerKey, $consumerSecret, $baseUrl = null)
    {
        $this->baseUrl = $baseUrl ? $baseUrl : self::BASE_URL;
        $this->oauthConfig = array(
            'consumer_key' => $consumerKey,
            'consumer_secret' => $consumerSecret
        );

        $this->handlerStack = HandlerStack::create();
        $this->unauthenticatedOauthListener = new Oauth1($this->oauthConfig);
        $this->handlerStack->push($this->unauthenticatedOauthListener, self::OAUTH_HANDLER_NAME);

        $config = array(
            'base_uri' => $this->baseUrl,
            'headers' => array(
                    'Return-Type' => 'text/json'
            ),
            'handler' => $this->handlerStack,
            'auth' => 'oauth'
        );
        parent::__construct($config);
    }

    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        try {
            return parent::request($method, $uri, $options);
        } catch (ClientException $e) {
            $this->handleException($e);
        }
    }

    public function setCredentials($tokenKey, $tokenSecret)
    {
        $config = array_merge($this->oauthConfig, array(
            'token' => $tokenKey,
            'token_secret' => $tokenSecret
        ));
        $this->authenticatedOauthListener = new Oauth1($config);
        $this->handlerStack->remove(self::OAUTH_HANDLER_NAME);
        $this->handlerStack->push($this->authenticatedOauthListener, self::OAUTH_HANDLER_NAME);
    }

    public function getRequestToken($callback)
    {
        $response = $this->get('token', ['query' => ['oauth_callback' => $callback]]);
        $tokenResponse = $response->getBody()->getContents();
        parse_str($tokenResponse, $requestToken);

        return $requestToken;
    }

    public function getAuthorizeUrl($token)
    {
        return $this->baseUrl . '/authorize?token=' . urlencode($token);
    }

    public function getAccessToken()
    {
        if (!$this->authenticatedOauthListener) {
            throw new KaskusClientException('You have to set credentials with authorized request token!');
        }

        $response = $this->get('accesstoken');
        $tokenResponse = $response->getBody()->getContents();
        parse_str($tokenResponse, $accessToken);

        return $accessToken;
    }

    protected function handleException(ClientException $exception)
    {
        $response = $exception->getResponse();
        
        if (!$exception->hasResponse()) {
            throw new KaskusServerException($exception->getMessage());
        }

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 500) {
            $bodyContent = $response->getBody()->getContents();
            throw new KaskusServerException(print_r($bodyContent, true), $statusCode);
        }

        try {
            $error = json_decode($response->getBody(), true);
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
