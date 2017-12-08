<?php
require __DIR__ . '/vendor/autoload.php';
session_start();

$consumer_key = '03f0968bdbd3462e77ff719b717f40';
$consumer_secret = 'b0b1338bcda983fe77342bab138951';
$client = new \Kaskus\Client\KaskusClient($consumer_key, $consumer_secret);
$authenticated = FALSE;

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === TRUE)
{
	$token_key = $_SESSION['token_key'];
	$token_secret = $_SESSION['token_secret'];

	$client->setCredentials($token_key, $token_secret);

	try
	{
		$user_request = $client->get('user');
		$user_str = $user_request->getBody()->getContents();
		$user_arr = json_decode($user_str, TRUE);

		$user_id = $user_arr['userid'];
		$username = $user_arr['username'];
		$profile_picture = $user_arr['profilepicture'];

		$authenticated = TRUE;
	}
	catch (\Kaskus\Exceptions\KaskusRequestException $exception)
	{
		// Kaskus Api returned an error
		$authenticated = FALSE;
		echo $exception->getMessage();
	}
	catch (\Exception $exception)
	{
		// some other error occured
		$authenticated = FALSE;
		echo $exception->getMessage();
	}
}
else
{
	header('Location: index.php');
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>KASKUS PHP SDK Example</title>
</head>
<body>
	<?php if ($authenticated): ?>
		Welcome, <a href="http://www.kaskus.co.id/profile/<?php echo $user_id; ?>"><?php echo $username;?></a><br>
		Your profile picture: <br><img src="<?php echo $profile_picture; ?>">
	<?php endif; ?>
</body>
</html>