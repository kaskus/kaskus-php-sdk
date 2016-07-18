<?php
namespace Kaskus;

use GuzzleHttp\Client;
use GuzzleHttp\Message\RequestInterface;
use Sainsburys\Guzzle\Oauth2\GrantType\ClientCredentials;
use Sainsburys\Guzzle\Oauth2\Oauth2Subscriber;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Kaskus\Exceptions\KaskusClientException;
use Kaskus\Exceptions\KaskusServerException;
use Kaskus\Exceptions\ResourceNotFoundException;
use Kaskus\Exceptions\UnauthorizedException;

class KaskusClientOauth2
{
    const BASE_URL = 'https://www.kaskus.co.id/';

    public $client;
    public $oauth2Subscriber;

    public function __construct($clientId, $clientSecret, $baseUrl = null, $accessToken = null)
    {
        $parsedUrl = $this->parseApiUrl($baseUrl);
        
        $this->client = new Client(['base_url' => $parsedUrl['apiUrl']]);

        $config = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'token_url' => $parsedUrl['accessTokenUrl'],
        ];
        
        $clientCredentials = new ClientCredentials($this->client, $config);
        $this->oauth2Subscriber = new Oauth2Subscriber($clientCredentials);

        if ($accessToken) {
            $this->oauth2Subscriber->setAccessToken($accessToken['accessToken'], 'Bearer', $accessToken['expires']);  
        }
        
        $this->client->setDefaultOption('debug', 'true');
        $this->client->setDefaultOption('auth', 'oauth2');
        $this->client->setDefaultOption('subscribers', [$this->oauth2Subscriber]);
        $this->client->setDefaultOption('headers', array('Return-Type' => 'text/json'));      
    }
    
    public function getClient()
    {
        return $this->client;
    }
    
    public function getAccessToken()
    {
        $accessToken = $this->oauth2Subscriber->getAccessToken();

        $token['accessToken'] = $accessToken->getToken();
        $token['expires'] = $accessToken->getExpires()->getTimestamp();
        
        return $token;
    }
    
    public function get($url = null, $options = [])
    {
        return $this->send($this->client->createRequest('GET', $url, $options));
    }

    public function head($url = null, array $options = [])
    {
        return $this->send($this->client->createRequest('HEAD', $url, $options));
    }

    public function delete($url = null, array $options = [])
    {
        return $this->send($this->client->createRequest('DELETE', $url, $options));
    }

    public function put($url = null, array $options = [])
    {
        return $this->send($this->client->createRequest('PUT', $url, $options));
    }

    public function patch($url = null, array $options = [])
    {
        return $this->send($this->client->createRequest('PATCH', $url, $options));
    }

    public function post($url = null, array $options = [])
    {
        return $this->send($this->client->createRequest('POST', $url, $options));
    }

    public function options($url = null, array $options = [])
    {
        return $this->send($this->client->createRequest('OPTIONS', $url, $options));
    }
    
    public function send(RequestInterface $request)
    {
        try {
            return $this->client->send($request)->json();
        } catch (RequestException $e) {
            $this->handleException($e);
        }
    }

    protected function handleException(RequestException $exception)
    {
        $response = $exception->getResponse();
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 500) {
            throw new KaskusServerException();
        }

        try {
            $error = $response->json();
        } catch (\RuntimeException $e) {
            throw new KaskusServerException();
        }

        if (isset($error['error_description'])) {
            $errorMessage = $error['error_description'];

            if ($statusCode === 401) {
                throw new UnauthorizedException($errorMessage);
            } elseif ($statusCode === 404) {
                throw new ResourceNotFoundException();
            }
            throw new KaskusClientException($errorMessage);
        }

        throw new KaskusServerException();
    }
    
    protected function parseApiUrl($url)
    {
        $baseUrl = parse_url($url ?: self::BASE_URL);
        
        $parsedUrl['apiUrl'] = $baseUrl['scheme'] . '://' . $baseUrl['host'] . '/api/oauth/';
        $parsedUrl['accessTokenUrl'] = $baseUrl['scheme'] . '://' . $baseUrl['host'] . '/oauth/access-token/';

        return $parsedUrl;
    }
}
