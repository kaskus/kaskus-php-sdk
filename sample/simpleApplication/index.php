<?php
	require __DIR__ . '/vendor/autoload.php';
	session_start();

	// configuration
	$consumerKey = 'YOUR CONSUMER KEY';
	$consumerSecret = 'YOUR CONSUMER SECRET';
	$callbackUrl = 'http://localhost:8000'; // e.g. http://yourapplication.com

	// creating client
	$client = new \Kaskus\KaskusClient($consumerKey, $consumerSecret);

	if (isset($_POST['login'])) {
		// attempt to get request token
		$requestToken = $client->getRequestToken($callbackUrl);
		$authorizeUrl = $client->getAuthorizeUrl($requestToken['oauth_token']);
		$_SESSION['tokenSecret'] = $requestToken['oauth_token_secret'];
		header('Location: ' . $authorizeUrl);
	} elseif ($_GET['oauth_token'] && $_GET['token'] && $_GET['oauth_verifier']) {
		$requestTokenSecret = $_SESSION['tokenSecret'];
		$client->setCredentials($_GET['oauth_token'], $requestTokenSecret);
		$accessToken = $client->getAccessToken();
		if ($accessToken['access'] === 'GRANTED') {
			$client->setCredentials($accessToken['oauth_token'], $accessToken['oauth_token_secret']);
			try {
				$response = $client->get('user');
				$userInfo = json_decode($response->getBody(), true);
				$profilePicture = $userInfo['profilepicture'];
			} catch (\Kaskus\Exceptions\KaskusRequestException $exception) {
				// Kaskus Api returned an error

			} catch (\Exception $exception) {
				// some other error occured
			}

?>
			Welcome, <a href="http://www.kaskus.co.id/profile/<?php echo $accessToken['userid']; ?>"><?php echo $accessToken['username'];?></a><br>
			Your profile picture: <br><img src="<?php echo $profilePicture; ?>">
<?php
		}
	} else {
?>
		<!DOCTYPE html>
		<html>
		<head>
			<title></title>
		</head>
		<body>
			<form method="POST">
				<input type="submit" name="login" value="login with kaskus">
			</form>
		</body>
		</html>
<?php
	}
?>