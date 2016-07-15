<?php

require __DIR__.'/../vendor/autoload.php';

$clientId = '1';
$clientSecret = 'secret';

//optional
$baseUrl = 'http://webstaging.kaskus.co.id/';
$token['accessToken'] = '13PP02Cl2zFFpeJnz08vZtW6OK5gIIyveNWq3vm4k';
$token['expires'] = 1468577986;

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
// $client->getAccessToken();