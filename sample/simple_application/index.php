<?php
require __DIR__ . '/vendor/autoload.php';
session_start();

// configuration
//todo: revert this
//$consumerKey = 'YOUR_API_KEY';
//$consumerSecret = 'YOUR_API_SECRET';
//$callbackUrl = 'http://localhost:8000'; // e.g. http://yourapplication.com
$consumerKey = '03f0968bdbd3462e77ff719b717f40';
$consumerSecret = 'b0b1338bcda983fe77342bab138951';
$callbackUrl = 'http://valdie.phpsdk-kaskus.dev/index.php'; // e.g. http://yourapplication.com

// creating client
$client = new \Kaskus\Client\KaskusClient($consumerKey, $consumerSecret);
$authorized = FALSE;

if (isset($_POST['login']))
{
	$request_token = $client->getRequestToken($callbackUrl);
	$authorize_url = $client->getAuthorizeUrl($request_token['oauth_token']);
	$_SESSION['token_secret'] = $request_token['oauth_token_secret'];

	header('Location: ' . $authorize_url);
}
else if (isset($_POST['logout']))
{
	unset($_SESSION['authorized']);
	unset($_SESSION['token_key']);
	unset($_SESSION['token_secret']);

	header('Location: index.php');
}

if (isset($_SESSION['authorized']) && $_SESSION['authorized'] === TRUE)
{
	$token_key = $_SESSION['token_key'];
	$token_secret = $_SESSION['token_secret'];

	$authorized = TRUE;
}
else if (isset($_GET['oauth_token']) && isset($_GET['token']) && isset($_GET['oauth_verifier']))
{
	$token_key = $_GET['oauth_token'];
	$token_secret = $_SESSION['token_secret'];

	$client->setCredentials($token_key, $token_secret);

	$access_token = $client->getAccessToken();

	if ($access_token['access'] === 'GRANTED')
	{
		$_SESSION['authorized'] = TRUE;
		$_SESSION['token_key'] = $access_token['oauth_token'];
		$_SESSION['token_secret'] = $access_token['oauth_token_secret'];;

		$token_key = $access_token['oauth_token'];
		$token_secret = $access_token['oauth_token_secret'];

		$authorized = TRUE;
	}
	else
	{
		$authorized = FALSE;
		unset($_SESSION['token_secret']);
	}
}
else
{
	$authorized = FALSE;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>KASKUS PHP SDK Example</title>
</head>
<body>
	<?php if ($authorized): ?>
		<div>
			<p>Successfully login with KASKUS, here's your token: </p>
			<p>Token : <?= $token_key; ?></p>
			<p>Token Secret : <?= $token_secret; ?></p>
			<p><a href="kaskus_profile.php">View your KASKUS profile</a></p>
			<form method="POST">
				<input type="submit" name="logout" value="logout">
			</form>
		</div>
	<?php else: ?>
		<form method="POST">
			<input type="submit" name="login" value="login with kaskus">
		</form>
	<?php endif; ?>
</body>
</html>