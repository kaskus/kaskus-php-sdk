<?php
require __DIR__ . '/vendor/autoload.php';
session_start();

// configuration
$consumer_key = 'YOUR_API_KEY';
$consumer_secret = 'YOUR_API_SECRET';
$callback_url = 'http://localhost:8000'; // e.g. http://yourapplication.com

// creating client
$client = new \Kaskus\Client\KaskusClient($consumer_key, $consumer_secret);
$authenticated = FALSE;

if (isset($_POST['login']))
{
	$request_token = $client->getRequestToken($callback_url);
	$authorize_url = $client->getAuthorizeUrl($request_token['oauth_token']);
	$_SESSION['token_secret'] = $request_token['oauth_token_secret'];

	header('Location: ' . $authorize_url);
}
else if (isset($_POST['logout']))
{
	unset($_SESSION['authenticated']);
	unset($_SESSION['token_key']);
	unset($_SESSION['token_secret']);

	header('Location: index.php');
}
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === TRUE)
{
	$token_key = $_SESSION['token_key'];
	$token_secret = $_SESSION['token_secret'];

	$authenticated = TRUE;
}
else if (isset($_GET['oauth_token']) && isset($_GET['token']) && isset($_GET['oauth_verifier']))
{
	$token_key = $_GET['oauth_token'];
	$token_secret = $_SESSION['token_secret'];

	$client->setCredentials($token_key, $token_secret);

	$access_token = $client->getAccessToken();

	if ($access_token['access'] === 'GRANTED')
	{
		$_SESSION['authenticated'] = TRUE;
		$_SESSION['token_key'] = $access_token['oauth_token'];
		$_SESSION['token_secret'] = $access_token['oauth_token_secret'];;

		$token_key = $access_token['oauth_token'];
		$token_secret = $access_token['oauth_token_secret'];

		$authenticated = TRUE;
	}
	else
	{
		$authenticated = FALSE;
		unset($_SESSION['token_secret']);
	}
}
else
{
	$authenticated = FALSE;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>KASKUS PHP SDK Example</title>
</head>
<body>
	<?php if ($authenticated): ?>
		<div>
			<p>Successfully login with KASKUS, here's your token: </p>
			<p>Token : <?php echo $token_key; ?></p>
			<p>Token Secret : <?php echo $token_secret; ?></p>
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