<?php

define('KASKUS_SDK_SRC_DIR', __DIR__.'/../src/Kaskus/');
require __DIR__ . '/../autoload.php';

$consumer_key = 'YOUR_API_KEY';
$consumer_secret = 'YOUR_API_SECRET';

$client = new \Kaskus\Client\KaskusClient($consumer_key, $consumer_secret);

try {
    $response = $client->get('forumlist');
    $forum_list = $response->getBody()->getContents();
    print_r($forum_list);
} catch (\Kaskus\Exceptions\KaskusRequestException $exception) {
    // Kaskus Api returned an error
    echo $exception->getMessage();
} catch (\Exception $exception) {
    // some other error occured
    echo $exception->getMessage();
}

echo "\n";
