<?php

require __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\Client;
use Sainsburys\Guzzle\Oauth2\GrantType\RefreshToken;
use Sainsburys\Guzzle\Oauth2\GrantType\ClientCredentials;
use Sainsburys\Guzzle\Oauth2\GrantType\PasswordCredentials;
use Sainsburys\Guzzle\Oauth2\Oauth2Subscriber;

try {
    
    $base_url = 'http://webstaging.kaskus.co.id';

    $oauth2Client = new Client(['base_url' => $base_url]);

    $config = [
        'username' => 'recca',
        'password' => 'kaskus',
        'client_id' => '1',
        'client_secret' => 'secret',
        'scope' => 'thread.write,thread.read',
        'token_url' => 'oauth/access-token',
    ];

    // $token = new PasswordCredentials($oauth2Client, $config);
    // $refreshToken = new RefreshToken($oauth2Client, $config);
    // $oauth2 = new Oauth2Subscriber($token, $refreshToken);
     
    $token = new ClientCredentials($oauth2Client, $config);
    $oauth2 = new Oauth2Subscriber($token);
    
    // $oauth2->setAccessToken('fdBH4DCoTDxQAH9F9sUZntXCEXNOGpHP9tSPtl9H', 'Bearer', 468308379);
    // $oauth2->setRefreshToken('Cj2eNF9rFNysxFNMj4hACLtEtkBNfnBAWhWeeeZK');

    $client = new Client([
        'defaults' => [
            'debug' => true, //to do delete
            'auth' => 'oauth2',
            'subscribers' => [$oauth2],
            'headers' => array(
                'Return-Type' => 'text/json'
            )
        ],
    ]);


    $response = $client->get('http://webstaging.kaskus.co.id/api/oauth/v1/forum_thread/530d5df41e0bc3a8068b47a5');
    var_dump($response->json());
    var_dump($oauth2->getAccessToken());
    var_dump($oauth2->getRefreshToken());
    
    $accessToken = $oauth2->getAccessToken();
    echo "\r\n access token \r\n";
    var_dump($accessToken->getToken());
    var_dump($accessToken->getExpires()->getTimestamp());
    echo "\r\n refresh token \r\n";
    var_dump($accessToken->getRefreshToken()->getToken());

} catch (\Exception $e) {
    var_dump($e->getResponse()->json());
}

 // var_dump($oauth2->getAccessToken());
  // var_dump($oauth2->getRefreshToken());
//   var_dump($oauth2->getAccessToken()->getExpires()->getTimestamp());
//   var_dump($oauth2->getRefreshToken()->token);