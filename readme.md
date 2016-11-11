Kaskus SDK For PHP
====================


This repository contains the open source PHP SDK that allows you to access Kaskus API from your PHP app.

This version of the Kaskus SDK for PHP requires 
* PHP 5.4 or greater.
* Composer

Installation
------------

1) Require this library in your composer.json
```
{
  "require": {
    "kaskus/kaskus-php-sdk": "v0.2.0"
  }
}
```

2) [Composer](https://getcomposer.org/) is a prerequisite for using Kaskus Sdk for PHP.

Install composer globally, then run `composer install` to install required files.

3) Get Consumer Key and Consumer Secret for your application.

4) Require *vendor/autoload.php* in your application.

5) Follow sample script for further usage



Usage
-----

Minimal example:

```php
<?php

// skip these two lines if you use composer 
define('KASKUS_SDK_SRC_DIR', '/path/to/kaskus-sdk-for-php/src/Kaskus/');
require __DIR__ . '/path/to/kaskus-sdk-for-php/autoload.php';

// skip this line if you do not use composer
require 'vendor/autoload.php';

$consumerKey = 'YOUR_API_KEY';
$consumerSecret = 'YOUR_API_SECRET';

$client = new \Kaskus\KaskusClient($consumerKey, $consumerSecret);

try {
    $response = $client->get('v1/hot_threads');
    $forumList = $response->json();
    print_r($forumList);
} catch (\Kaskus\Exceptions\KaskusRequestException $exception) {
    // Kaskus Api returned an error
    
} catch (\Exception $exception) {
    // some other error occured
    
}

```

Login With Kaskus
-----------------

This is the login flow diagram. For more information see this [article](http://oauthbible.com/)
![alt tag](https://raw.githubusercontent.com/kaskus/kaskus-php-sdk/master/docs/Kaskus%20Oauth1%20Login%20Flow.png)

Use this [Oauth sample](sample/simpleApplication/readme.md)

Advance Usage
-------------

We use guzzle as HTTP Client, for further usage, read [Guzzle](http://guzzle.readthedocs.org/en/latest/)

Api Documentation
-----------------

### Get all current hot threads
```
GET /v1/hot_threads
```

### Get thread detail for certain thread id

```
GET /v1/thread/<thread_id>?field=thread,thread_id,total_post,current_page,per_page,open,total_page,posts,profilepicture,post_username,post_userid,title,decoded,dateline,profilepicture,usertitle,post_id,reputation_box,pagetext,enable_reputation&page=1&limit=20

Parameter:
thread_id : thread id to read
field : fields name separated by comma that we want to filter from response
page : page that we want to read
limit : post per page that we want to read
```

### Get top thread for certain forum.

```
GET /content/top_thread/{forum_id}

Parameter:
forum_id : forum id that we want to see the top thread, e.g. 36 (Handphone and tablet)

```


### Get list of user post

```
GET /mypost/{user_id}

Parameter:
user_id : user id that we want to see the post list, e.g. 1234
```



