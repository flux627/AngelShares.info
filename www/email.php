<? 
// metacoin 2014
function send_mail($email) {
	require_once('/home/ubuntu/php-scripts/ses.php');
	$ses = new SimpleEmailService('public-key', 'private-key');

	//$ses->enableVerifyPeer(false);

	$m = new SimpleEmailServiceMessage();
	$m->addTo($email['address']);
	$m->setFrom('noreply@angelshares.info');
	$m->setSubject('AngelShares.info Notification Subscription');
	$m->setMessageFromString('Thank you for signing up for notifications from AngelShares.info. Every day at 21:00 UTC, approximately three hours before the cycle ends, you will receive an email with the current AngelShares rate for both ProtoShares and Bitcoin.

If you did not request this email, or if you would like to unsubscribe, click here: ' . $email['unsub']);
	$ses->sendEmail($m);
}

function db_connect() {
	$setup['DB_HOST']='127.0.0.1';
	$setup['DB_PORT']=3306;
	$setup['DB_NAME']='angelsharesdotinfo';
	$setup['DB_USER']='root';
	$setup['DB_PASS']='omitted';
	try {
		$dbh = new PDO("mysql:host=" . $setup['DB_HOST'] . ";dbport=" . $setup['DB_PORT'] . ";dbname=" . $setup['DB_NAME'], $setup['DB_USER'], $setup['DB_PASS'], array(
					PDO::ATTR_PERSISTENT => true
					));

	}
	catch (PDOException $e) {
		$error = ("Error connecting to database.");
	}
	return $dbh;
}

$error = '';
$message = '';
$test_msg = null;
if (isset($_GET['unsub']) && count($_GET['unsub']>30)) {
	$secret = $_GET['unsub'];
	$dbh = db_connect();
	$stmt = $dbh->prepare("update email set disable = 1 where secret = ?");
	$stmt->bindParam(1, $secret, PDO::PARAM_STR, strlen($secret));
	if ($stmt->execute()) { $success = 'You have sucessfully unsubscribed your email from the alert system.';
	} else { $error = 'There is no email on file associated with this link.'; }
}
	
if ($_POST) {
	$email = $_POST['email'];
	$remail = $_POST['remail'];
	if ($email != $remail) {
		$error = 'Emails do not match.';
	} else {
		// connect to DB
		$dbh = db_connect();
		// check if they are already subscribed
		$stmt = $dbh->prepare("select count(*) from email where email = ? and disable = 0");
		$stmt->bindParam(1, $email, PDO::PARAM_STR, strlen($email));
		$stmt->execute();
		$rowcount =  $stmt->fetch(PDO::FETCH_COLUMN, 0);
		if ($rowcount != 0) { $error = "This email ($email) is already subscribed to recieve updates."; }
		else {
			// find currency from cookie	
			$currency = 'USD';
			$ip = $_SERVER['REMOTE_ADDR'];

			// create a secret key for them to unsubscribe with
			$secret_uniq = $email . microtime(true) . 'ajs55353%%##KKKJJ$$(((RPP""jwrirkwNNnnLLLOppazxxsJIW8U8nnnneee*003';
			$test_msg[] = $secret_uniq;
			$secret= hash_hmac('ripemd160', $secret_uniq, 0, 0);
			$test_msg[] = $secret;

			// store row into db
			$stmt = $dbh->prepare("insert into email (email, ip, currency, secret) values (?, '$ip', ?, '$secret')");
			$stmt->bindParam(1, $email, PDO::PARAM_STR, strlen($email));
			$stmt->bindParam(2, $currency, PDO::PARAM_STR, strlen($currency));

			if ($stmt->execute()) { $success = "Your email has successfully been signed up for alerts. Please check your inbox for more info ($email)."; 
			} else { $error = "Error storing email in database"; } 

			$test_msg[] = "http://angelshares.info/email.php?unsub=$secret";
			send_mail(array('address' => $email, 'unsub' => ('http://angelshares.info/email.php?unsub=' . $secret)));
		}
	}
}
?>
<html>
<head>
    <title>AngelShares.info</title>
    <meta http-equiv="content-type" 
        content="text/html;charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>
<div id="wrap">

<? include('templates/top.php') ?>

<div id="content">
<h2>Set up email notifications</h2>
<div>Enter your email address here to recieve a notification when there are only 3 hours remaining to submit BTC or PTS for the current cycle.<br/>
The email notification also contains relevant info on AngelShares.<br/></div><br/>
<? if ($error) { echo "<div id = 'error'>$error</div>"; } ?>
<? if ($success) { echo "<div id = 'success'>$success</div>"; } ?>

<form action="email.php" method="POST">
  <input id="email" name="email" placeholder="Enter your email here">
  <input id="remail" name="remail" placeholder="Confirm your email">

  <input id = "submit" name = "submit" value = "submit" type = "submit">
</form>

</div>
</div> <!-- end wrap -->
</body>
</html>
