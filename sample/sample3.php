<?php

require __DIR__.'/../vendor/autoload.php';

$config['clientId'] = 'YOUR_CLIENT_ID';
$config['clientSecret'] = 'YOUR_CLIENT_SECRET';

//optional
$config['baseUrl'] = 'https://www.kaskus.co.id/';
$config['accessTokenUrl'] = 'https://www.kaskus.co.id/oauth/access-token/';
$config['accessToken'] = [
    'accessToken' => 'YOUR_ACCESS_TOKEN',
    'expires' => 1468577986 //unix timestamp
];

$client = new \Kaskus\KaskusClientOauth2($config);

try {
    $response = $client->get('v1/hot_threads');
    print_r($response);
} catch (\Kaskus\Exceptions\KaskusRequestException $exception) {
    // Kaskus Api returned an error
    echo $exception->getMessage();
} catch (\Exception $exception) {
    // some other error occured
    echo $exception->getMessage();
}

echo "\n";

// Get current access token or request new access token
// SDK will request new access token if current access token is invalid, expired, or not exists
// $client->getAccessToken();