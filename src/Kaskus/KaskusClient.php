<?php
namespace Kaskus;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Kaskus\Exceptions\KaskusClientException;
use Kaskus\Exceptions\KaskusServerException;
use Kaskus\Exceptions\ResourceNotFoundException;
use Kaskus\Exceptions\UnauthorizedException;

class KaskusClient extends \GuzzleHttp\Client
{

    const BASE_URL = 'https://www.kaskus.co.id/api/oauth/';

    /**
     * @var array
     */
    protected $oauthConfig;

    protected $unauthenticatedOauthListener;

    protected $authenticatedOauthListener;

    public function __construct($consumerKey, $consumerSecret, $baseUrl = null)
    {
        $config = array(
            'base_url' => $baseUrl ? $baseUrl : self::BASE_URL,
            'defaults' => array(
                'auth' => 'oauth',
                'headers' => array(
                    'Return-Type' => 'text/json'
                )
            )
        );
        parent::__construct($config);

        $this->oauthConfig = array(
            'consumer_key' => $consumerKey,
            'consumer_secret' => $consumerSecret
        );

        $this->unauthenticatedOauthListener = new Oauth1($this->oauthConfig);
        $this->getEmitter()->attach($this->unauthenticatedOauthListener);
    }

    public function send(RequestInterface $request)
    {
        try {
            return parent::send($request);
        } catch (RequestException $e) {
            $this->handleException($e);
        }
    }

    public function setCredentials($tokenKey, $tokenSecret)
    {
        $this->getEmitter()->detach($this->unauthenticatedOauthListener);
        $config = array_merge($this->oauthConfig, array(
            'token' => $tokenKey,
            'token_secret' => $tokenSecret
        ));
        $this->authenticatedOauthListener = new Oauth1($config);
        $this->getEmitter()->attach($this->authenticatedOauthListener);
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
        return $this->getBaseUrl() . '/authorize?token=' . urlencode($token);
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

    protected function handleException(RequestException $exception)
    {
        $response = $exception->getResponse();
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 500) {
            $body = $response->getBody();
            throw new KaskusServerException($body, $statusCode);
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
