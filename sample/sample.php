<?php

require __DIR__.'/../vendor/autoload.php';

$consumerKey = 'YOUR_API_KEY';
$consumerSecret = 'YOUR_API_SECRET';

$client = new \Kaskus\KaskusClient($consumerKey, $consumerSecret);

try {
    $response = $client->get('v3/hot_threads?channel_id=0');
    $hotThreads = json_decode($response->getBody(), true);
    print_r($hotThreads);
} catch (\Kaskus\Exceptions\KaskusRequestException $exception) {
    // Kaskus Api returned an error
    echo $exception->getMessage();
} catch (\Exception $exception) {
    // some other error occured
    echo $exception->getMessage();
}

echo "\n";






