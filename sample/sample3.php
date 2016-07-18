<?php

require __DIR__.'/../vendor/autoload.php';

$clientId = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';

//optional
$baseUrl = 'https://www.kaskus.co.id/';
$token['accessToken'] = 'YOUR_ACCESS_TOKEN';
$token['expires'] = 1468577986; //unix timestamp

$client = new \Kaskus\KaskusClientOauth2($clientId, $clientSecret, $baseUrl, $token);

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