<?php

define('KASKUS_SDK_SRC_DIR', __DIR__.'/../src/Kaskus/');
require __DIR__ . '/../autoload.php';

// $consumerKey = 'YOUR_API_KEY';
// $consumerSecret = 'YOUR_API_SECRET';

$consumerKey = '03f0968bdbd3462e77ff719b717f40';
$consumerSecret = 'b0b1338bcda983fe77342bab138951';

$client = new \Kaskus\Client\KaskusClient($consumerKey, $consumerSecret);

try {
    $response = $client->get('v1/hot_threads');
    $forumList = $response->json();
    print_r($forumList);
} catch (\Kaskus\Exceptions\KaskusRequestException $exception) {
    // Kaskus Api returned an error
    echo $exception->getMessage();
} catch (\Exception $exception) {
    // some other error occured
    echo $exception->getMessage();
}

echo "\n";
