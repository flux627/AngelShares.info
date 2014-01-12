#!/usr/bin/php
<?
// metacoin 2014
require_once('/home/ubuntu/php-scripts/ses.php');

// connect to DB
$setup['DB_HOST']='127.0.0.1';
$setup['DB_PORT']=3306;
$setup['DB_NAME']='angelsharesdotinfo';
$setup['DB_USER']='root';
$setup['DB_PASS']='omitted';
try { $dbh = new PDO("mysql:host=" . $setup['DB_HOST'] . ";dbport=" . $setup['DB_PORT'] . ";dbname=" . $setup['DB_NAME'], $setup['DB_USER'], $setup['DB_PASS'], array( PDO::ATTR_PERSISTENT => true)); } catch (PDOException $e) { $error = ("Error connecting to database."); } 

// get all email info
$q = $dbh->query("select * from email where disable = 0");
$user_info = $q->fetchAll(PDO::FETCH_ASSOC);

// get info from our API
$coinsAmount = curl('http://angelshares.info/beta/json/index.php?coinsAmount');
$pts_today = $coinsAmount['PTS'][count($coinsAmount['PTS'])-1][1];
$btc_today = $coinsAmount['BTC'][count($coinsAmount['BTC'])-1][1];

$pts_ags_today = 5000/$pts_today;
$btc_ags_today = 5000/$btc_today;

$pts_now = curl('http://angelshares.info/beta/curjson/pts_now.json');
$btc_now = curl('http://angelshares.info/beta/curjson/btc_now.json');


foreach ($user_info as $email) {
	$pts_donated_today_cur = number_format($pts_today * (double)$pts_now['PTS'] * $btc_now[$email['currency']], 4);
	$pts_ags_today_cur = number_format($pts_ags_today/((double)$pts_now['PTS'] * $btc_now[$email['currency']]), 4);
	
	$btc_today_cur = number_format($btc_today * $btc_now[$email['currency']], 4);
	$btc_ags_today_cur = number_format($btc_ags_today/$btc_now[$email['currency']], 4);

	$body = "There are approximately 3 hours remaining before a new AngelShares cycle begins.

Here are the stats for " . date('l jS \of F Y h:i:s A') . " UTC:
Today's Protoshares Info:
Donated today: " . number_format($pts_today, 4) . " PTS
Total value: $pts_donated_today_cur " . $email['currency'] . "

Current AngelShares Value: " . number_format($pts_ags_today, 4) . " AGS/PTS
Current AngelShares Value: $pts_ags_today_cur AGS/" . $email['currency'] . "


Today's Bitcoin Info:
Donated today: " . number_format($btc_today) . " BTC
Total value: $btc_today_cur " . $email['currency'] . "

Current AngelShares Value: " . number_format($btc_ags_today) . " AGS/BTC
Current AngelShares Value: $btc_ags_today_cur AGS/" . $email['currency'] . "

For more info, please check http://angelshares.info regularly.";

	// set up amazon SES
	$ses = new SimpleEmailService('AKIAIBU3OV5SJPTVNXWA', 'pN0z6xx6gNX+vOe9Yvyj3sSNAOiNZ4C08G7c0Ekp');
	$m = new SimpleEmailServiceMessage();
	$m->addTo($email['email']);
	$m->setFrom('noreply@angelshares.info');
	$m->setSubject("AngelShares.info 3 hour alert ");
	$m->setMessageFromString($body);

	$ses->sendEmail($m);
	unset($ses);
	unset($m);

	sleep(0.3);
}


function curl($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	$resp = json_decode(curl_exec($ch), 1);
	curl_close($ch);
	return $resp;
}
?>
