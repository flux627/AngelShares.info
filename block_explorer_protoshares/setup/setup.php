<?
// this file should be included in every class which takes advantage of the database or RPC

// for now, only MYSQL support...
$DB_TYPE = 'MYSQL';
$control_keys = array(
	'FLORIND',    // florincoin command
	'RPC_USER',   // the rpc user
	'RPC_PASS',   // rpc password
	'RPC_HOST',   // rpc hostname
	'RPC_PORT',   // rpc port number
	'DB_HOST',    // database host (localhost or remote)
	'DB_PORT',    // database port number
	'DB_USER',    // database username
	'DB_PASS'     // database password
); // these are the required fields, all others are optional

$optional_keys = array(
	'RECORD_BLOCKCHAIN',   // record blockchain or only messages
	'LOG_DIR_CRON',        // directory for cron log
	'CLI'                  // command line interface (specify for debug output in setup)
); // optional fields

// parse and validate setup.conf
$control = file(__DIR__ . '/setup.conf', FILE_IGNORE_NEW_LINES);
if (!$control) die("ERROR READING setup.conf\r\n");

foreach ($control as $key=>$val) {
	if (!$val) unset($control[$key]);
	else if ($val[0] == "#") unset($control[$key]);
	else {
		$vals = explode("=", $val);
		$setup[$vals[0]] = $vals[1];
	} 
}

// check if required modules are loaded.
$extensions = array('bcmath', 'pdo');
foreach ($extensions as $extension) {
   if (extension_loaded("$extension")) {
      if ($setup['CLI']) echo "$extension is loaded";
   } else {
      die("CRITICAL ERROR: $extension was not found - please install before continuing");
   };
}

// validate
$setup_check = $setup;
foreach($optional_keys as $opt) unset($setup_check[$opt]);

if ($control_keys == array_intersect($control_keys, array_keys($setup_check))) {}
else {
	echo "Fatal error: setup.conf has the following errors:\r\n";
	$probs = array_diff($control_keys, (array_keys($setup_check)));
	if ($probs == $control_keys) {
		echo "You haven't set any values in setup.conf or you don't have permission to read it from this user. Please check that the settings for setup.conf are valid and your user has permission to read that file.";
	} 
	else {
		foreach ($probs as $prob) echo "$prob not set \r\n";
	}
}

// setup database
if ($setup["LOG_DIR_CRON"]) $setup["LOG_DIR_CRON"] .= "blockchain_cron_log.txt";
if ($DB_TYPE == 'MYSQL') { 
   if ($setup['CLI']) echo "Attempting to connect to database..";
	try {
		$dbh = new PDO("mysql:host=" . $setup['DB_HOST'] . ";dbport=" . $setup['DB_PORT'] . ";dbname=" . $setup['DB_NAME'], $setup['DB_USER'], $setup['DB_PASS'], array(
          PDO::ATTR_PERSISTENT => true
      ));
	}
	catch (PDOException $e) {
		die("Error connecting to database.");
	}
}
$rpc_setup = array(
	"RPC_USER" => $setup["RPC_USER"],
	"RPC_PASS" => $setup["RPC_PASS"],
	"RPC_HOST" => $setup["RPC_HOST"],
	"RPC_PORT" => $setup["RPC_PORT"]
);
?>
