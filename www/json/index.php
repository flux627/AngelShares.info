<?
// metacoin 2014

if ($_GET || $_SERVER['QUERY_STRING']) {
	$pts_dbh = create_pts_dbh();
	$btc_dbh = create_btc_dbh();

	// acquire data from database about 2013 and 2014 protoshares / BTC sent to ANGEL addresses	
	if ($_SERVER['QUERY_STRING'] == 'coinsAmount') {
		$ptsags = get_ags_amounts($pts_dbh);
		$btcags = get_ags_amounts($btc_dbh);

		exit(JSON_ENCODE(array("BTC" => combine_dates($btcags[0], $btcags[1]), "PTS" => combine_dates($ptsags[0], $ptsags[1]))));
	}

	// function to simply get how many pages there are in the fullTransaction
	if (isset($_GET['getFullTransactionPages'])) {
		if ($_GET['getFullTransactionPages'] == 'BTC') $dbh = $btc_dbh;
		if ($_GET['getFullTransactionPages'] == 'PTS') $dbh = $pts_dbh;
		exit(getFullTransactionPages($dbh, 50));
	}

	// take in page number and get result data
	if (isset($_GET['fullTransactionPaginated']) && isset($_GET['page'])) {
		$page = $_GET['page'];
		if ($_GET['fullTransactionPaginated'] == 'BTC') $dbh = $btc_dbh;
		if ($_GET['fullTransactionPaginated'] == 'PTS') $dbh = $pts_dbh;
		exit(JSON_ENCODE(getFullTransactionPaginated($dbh, $page, 50)));
	}

	// retrieve transactions by DAY (given a microtime)
	if (isset($_GET['dayTransactions'])) {
		if (count($_GET['dayTransactions']) > 0) {
			$microtime = (int)($_GET['dayTransactions']/1000)+10000;
			$pts_dt = get_day_transactions($pts_dbh, $microtime);
			$btc_dt = get_day_transactions($btc_dbh, $microtime);

			exit(JSON_ENCODE(array("BTC" => $btc_dt,  "PTS" => $pts_dt)));
		}
	}

	// acquire allAddresses in paginated format
	if ($_SERVER['QUERY_STRING'] == 'allAddressesPaginated') {
		$ptsags = get_ags_amounts($pts_dbh);
		$btcags = get_ags_amounts($btc_dbh);

		$ptsaddr = get_ags_addresses($pts_dbh);
		$btcaddr = get_ags_addresses($btc_dbh);
		
		//echo "<pre>";
		//var_dump ($ptsags);
		//echo "<hr/>";
		//var_dump ($ptsaddr);
		//echo "<hr/>";

		// convert to simple assoc array
		$ptsags_assoc[1388534400000] = (double)$ptsags[0];
		foreach ($ptsags[1] as $pts) { $ptsags_assoc[(int)$pts[0]] = (double)$pts[1]; }
		$btcags_assoc[1388534400000] = (double)$btcags[0];
		foreach ($btcags[1] as $btc) { $btcags_assoc[(int)$btc[0]] = (double)$btc[1]; }
		//var_dump($ptsags_assoc);
		//echo "<hr/>";
		$pag = null;
		$bag = null;

		// calculate all PTS addresses and how many AGS they have
		foreach ($ptsaddr as $ptsad) {
			if ($ptsad[2] < 1388534400) $ptsad[2] = 1388534400;
			$ags_per_day = $ptsags_assoc[(int)$ptsad[2]*1000];
			$pts_sent = (double)($ptsad[1]);
			$ags_total = (5000/$ags_per_day)*$pts_sent;
		//	echo "The address " . $ptsad[0]. " got 5000/$ags_per_day * $pts_sent = $ags_total AGS on " . $ptsad[2] . "\r\n";
			$pag[$ptsad[0]] += $ags_total;
		}

		// calculate all BTC addresses and how many AGS they have
		foreach ($btcaddr as $ptsad) {
			if ($ptsad[2] < 1388534400) $ptsad[2] = 1388534400;
			$ags_per_day = $btcags_assoc[(int)$ptsad[2]*1000];
			$pts_sent = (double)($ptsad[1]);
			$ags_total = (5000/$ags_per_day)*$pts_sent;
			//echo "The address " . $ptsad[0]. " got 5000/$ags_per_day * $pts_sent = $ags_total AGS on " . $ptsad[2] . "\r\n";
			$bag[$ptsad[0]] += $ags_total;
		}

		$bagpag = null;
		$pagpag = null;
		$rpp = 50;
		
		$page = 0;
		$i = 0;
		foreach ($bag as $key=>$b) {
			if ($i % $rpp == 0) {
				$page++;
			}
			$bagpag[$page][] = array($key=>$b);
			$i++;
		}

		$page = 0;
		$i = 0;
		foreach ($pag as $key=>$p) {
			if ($i % $rpp == 0) {
				$page++;
			}
			$pagpag[$page][] = array($key=>$p);
			$i++;
		}
			
		
	
		for ($i = 0; $i < count($ptsaddr); $i++) {
			
		}
		exit(JSON_ENCODE(array("BTC" => $bagpag, "PTS" => $pagpag)));
	}
	
	// acquire data on all addresses AGS balance
	if ($_SERVER['QUERY_STRING'] == 'allAddresses') {
		$ptsags = get_ags_amounts($pts_dbh);
		$btcags = get_ags_amounts($btc_dbh);

		$ptsaddr = get_ags_addresses($pts_dbh);
		$btcaddr = get_ags_addresses($btc_dbh);
		
		//echo "<pre>";
		//var_dump ($ptsags);
		//echo "<hr/>";
		//var_dump ($ptsaddr);
		//echo "<hr/>";

		// convert to simple assoc array
		$ptsags_assoc[1388534400000] = (double)$ptsags[0];
		foreach ($ptsags[1] as $pts) { $ptsags_assoc[(int)$pts[0]] = (double)$pts[1]; }
		$btcags_assoc[1388534400000] = (double)$btcags[0];
		foreach ($btcags[1] as $btc) { $btcags_assoc[(int)$btc[0]] = (double)$btc[1]; }
		//var_dump($ptsags_assoc);
		//echo "<hr/>";
		$pag = null;
		$bag = null;

		// calculate all PTS addresses and how many AGS they have
		foreach ($ptsaddr as $ptsad) {
			if ($ptsad[2] < 1388534400) $ptsad[2] = 1388534400;
			$ags_per_day = $ptsags_assoc[(int)$ptsad[2]*1000];
			$pts_sent = (double)($ptsad[1]);
			$ags_total = (5000/$ags_per_day)*$pts_sent;
		//	echo "The address " . $ptsad[0]. " got 5000/$ags_per_day * $pts_sent = $ags_total AGS on " . $ptsad[2] . "\r\n";
			$pag[$ptsad[0]] += $ags_total;
		}

		// calculate all BTC addresses and how many AGS they have
		foreach ($btcaddr as $ptsad) {
			if ($ptsad[2] < 1388534400) $ptsad[2] = 1388534400;
			$ags_per_day = $btcags_assoc[(int)$ptsad[2]*1000];
			$pts_sent = (double)($ptsad[1]);
			$ags_total = (5000/$ags_per_day)*$pts_sent;
			//echo "The address " . $ptsad[0]. " got 5000/$ags_per_day * $pts_sent = $ags_total AGS on " . $ptsad[2] . "\r\n";
			$bag[$ptsad[0]] += $ags_total;
		}
		//var_dump($pag);
		/*	
		foreach ($bag as $b) { $count+= $b; }
		foreach ($pag as $p) { $pcount+= $p; }
		echo "AGS from btcCount = $count<br/>";
		echo "AGS from ptsCount = $pcount<br/>";
			
		echo "</pre>";
		
	
		for ($i = 0; $i < count($ptsaddr); $i++) {
			
		}
		*/
		exit(JSON_ENCODE(array("BTC" => $bag, "PTS" => $pag)));
	}

	// transactions today and yesterday
	if ($_SERVER['QUERY_STRING'] == 'ticker') {
		$ptsags = get_ags_amounts_ticker($pts_dbh, 1);
		$btcags = get_ags_amounts_ticker($btc_dbh, 1);

		exit(JSON_ENCODE(array("BTC" => combine_dates($btcags[0], $btcags[1]), "PTS" => combine_dates($ptsags[0], $ptsags[1]))));
	}

	// all transactions 
	if ($_SERVER['QUERY_STRING'] == 'coinsTransaction') {
		$ptstx = get_ags_tx($pts_dbh);
		$btcags = get_ags_tx($btc_dbh);
		exit(JSON_ENCODE(array("BTC" => $btcags, "PTS" => $ptstx)));
	}

	// address lookup
	if ($_GET['address'] && ctype_alnum($_GET['address'])) {
		if ($_GET['address'][0] == '1') $dbh = $btc_dbh;
		else if ($_GET['address'][0] == 'P') $dbh = $pts_dbh;
		else exit();
		exit(get_address_info($dbh, $_GET['address']));
	}
	
	// get last update timestamp for BTC/PTS blockchain
	if ($_GET['update'] == "BTC" || $_GET['update'] == "PTS") {
		if ($_GET['update'] == "BTC") { $dbh = $btc_dbh; }
		if ($_GET['update'] == "PTS") { $dbh = $pts_dbh; }
		$q = $dbh->query("select B.id as block, A.entry*1000 as time from control as A join (select max(id) as id from block) as B where A.name = 'ags_lastblock'");
		$b = $q->fetchAll(PDO::FETCH_NUM);
		$block = (int)$b[0][0];
		$timestamp = (int)$b[0][1];
		exit(JSON_ENCODE(array($block, $timestamp)));
	}
}
else echo "hello world";


/* FUNCTIONS */

// get pages in fullTransaction (database, results_per_page)
function getFullTransactionPages($dbh, $rpp) {
	$q = $dbh->query("select count(*) from agsinfo where amount > 0");
	$r = $q->fetchAll(PDO::FETCH_COLUMN, 0);
	$results = $r[0];
	return ceil($results/$rpp);
}

// get results in fullTransaction in paginated format (only $results_per_page results).
function getFullTransactionPaginated($dbh, $page, $rpp) {
	$q = $dbh->query($b = "select address, txid, amount, block, 1000*blocktime from agsinfo where amount > 0 limit " . ($page*$rpp) .", " . (($page*$rpp) + $rpp));
	$r = $q->fetchAll(PDO::FETCH_NUM);
	return $r;
}
	
// get transactions with more than 0 amount sent to AGS address
function get_ags_tx($dbh) {
		$q = $dbh->query("select address, txid, amount, 1000*unix_timestamp(date(from_unixtime(blocktime))) from agsinfo where amount > 0");
		$arr = $q->fetchAll(PDO::FETCH_NUM);
		//$arr = $arr[0];

		// fix array so all dates before 2014-01-02 are set to 2014-01-01
		for ($i = 0; $i < count($arr); $i++) {
			if ($arr[$i][2] < 1388620800) $arr[$i][2] = 1388534400;
		}
		return $arr;
}

// get amounts
function get_ags_amounts($dbh) {
		// get 2013 data (day1 = every day prior to 2013)
		$q = $dbh->query("select sum(amount) from agsinfo where blocktime < 1388620800");
		$ags2013 = $q->fetchAll(PDO::FETCH_NUM);
		$ags2013 = $ags2013[0][0];

		$q = $dbh->query("select 1000*unix_timestamp(date(from_unixtime(blocktime))), sum(amount) from agsinfo where blocktime > 1388620800 group by date(from_unixtime(blocktime))");
		$ags2014 = $q->fetchAll(PDO::FETCH_NUM);

		return array($ags2013, $ags2014);
}

// get addresses
function get_ags_addresses($dbh) {
		$q = $dbh->query("select A.address, sum(A.amount), A.dateSimple from (select address, amount, unix_timestamp(date(from_unixtime(blocktime))) as dateSimple from agsinfo where amount > 0) as A group by A.address, A.dateSimple");
		$addresses = $q->fetchAll(PDO::FETCH_NUM);

		return $addresses;
}

// get amounts for ticker purposes
function get_ags_amounts_ticker($dbh) {
		$q = $dbh->query("select unix_timestamp(date(now()))");
		$r = $q->fetchAll(PDO::FETCH_COLUMN, 0);
		$today = $r[0];
		$yesterday = $today - (60 * 60 * 24);

		// if we haven't reached jan03 yet, just return regular get_ags_amounts
		if ($today == 1388620800) return get_ags_amounts($dbh);

		$q = $dbh->query("select 1000*unix_timestamp(date(from_unixtime(blocktime))), sum(amount) from agsinfo where blocktime >= $yesterday and blocktime < $today group by date(from_unixtime(blocktime))");
		$ags2013 = $q->fetchAll(PDO::FETCH_NUM);

		$q = $dbh->query("select 1000*unix_timestamp(date(from_unixtime(blocktime))), sum(amount) from agsinfo where blocktime > $today group by date(from_unixtime(blocktime))");
		$ags2014 = $q->fetchAll(PDO::FETCH_NUM);

		return array($ags2013, $ags2014);
}

function get_day_transactions($dbh, $time) {
	if (($time-10000) < 1388620800) {
		$time -= 10000;
		$today = 1387843200;
		$tomorrow = 1388620800;
		// get date of first day, date of second day, all coins, min block, and max block from first day to second day
		$q = $dbh->query("select 1000*unix_timestamp(date(from_unixtime($today))), 1000*unix_timestamp(date(from_unixtime($tomorrow))), sum(amount), min(block), max(block) from agsinfo where blocktime < $tomorrow");
	} else {
		$q = $dbh->query($qh = "select unix_timestamp(date(from_unixtime($time)))");
		$r = $q->fetchAll(PDO::FETCH_COLUMN, 0);
		$today = $r[0];
		$tomorrow = $today + (60 * 60 * 24);
		// get date of first day, date of second day, all coins, min block, and max block from first day to second day
		$q = $dbh->query("select 1000*unix_timestamp(date(from_unixtime($today))), 1000*unix_timestamp(date(from_unixtime($tomorrow))), sum(amount), min(block), max(block) from agsinfo where blocktime >= $today and blocktime < $tomorrow group by date(from_unixtime(blocktime))");
	}
	$ags2013 = $q->fetchAll(PDO::FETCH_NUM);

	// get all transaction IDs between these dates
	$q = $dbh->query("select address, amount, txid, block, blocktime*1000 from agsinfo where blocktime >= $today and blocktime < $tomorrow and amount > 0");
	$agstx= $q->fetchAll(PDO::FETCH_NUM);
	return array($ags2013, $agstx);

}

function get_address_info($dbh, $address) {
	// get the association with this address (simple assoc, not deep search)
	$q = $dbh->query("select distinct assoc from agsinfo where address = '$address'");
	$r = $q->fetchAll(PDO::FETCH_NUM);
	$assoc = $r[0][0];

	// get all addresses associated with it (make this recursive search later)
	$q = $dbh->query("select distinct B.address from agsinfo as A join (select * from agsinfo where assoc = $assoc) as B where A.assoc = B.assoc");
	$r = $q->fetchAll(PDO::FETCH_COLUMN, 0);
	$addresses = $r;
	$address_clause = " where address = '" . $addresses[0] . "' ";
	for ($i = 1; $i < count($addresses); $i++) {$address_clause .= " or address = '$address' ";}
	//[balance], [related addresses], tx: [hash, amount, date]
	
	// get balance
	$q = $dbh->query($b = "select sum(amount) from agsinfo $address_clause");
	
	$r = $q->fetchAll(PDO::FETCH_NUM);
	$balance = $r[0][0];
	
	// get this user's transactions
	$q = $dbh->query("select txid, amount, 1000*blocktime from agsinfo $address_clause and amount > 0");
	$r = $q->fetchAll(PDO::FETCH_NUM);
	$txinfo = $r;
	
	return JSON_ENCODE(array($balance, $addresses, array("tx" => $txinfo)));
}

// combine dates into one array for json formatting
function combine_dates($ags2013, $ags2014) {
	$json = array();

	// combine 2013 and 2014 (multiply by 1000 for javascript compatibility)
	$jan01 = 1388534400 * 1000;
	$json[] = array((int)$jan01, (double)$ags2013);
	
	foreach ($ags2014 as $data) {
		$json[] = array((int)$data[0], (double)$data[1]);
	}
	return $json;
}

// database handle creation
function create_btc_dbh() {
	  $setup['DB_HOST']='127.0.0.1';
	  $setup['DB_PORT']=3306;
	  $setup['DB_NAME']='bitcoin';
	  $setup['DB_USER']='root';
	  $setup['DB_PASS']='omitted';
	  try {
				 $dbh = new PDO("mysql:host=" . $setup['DB_HOST'] . ";dbport=" . $setup['DB_PORT'] . ";dbname=" . $setup['DB_NAME'], $setup['DB_USER'], $setup['DB_PASS'], array(
												 PDO::ATTR_PERSISTENT => true
												 ));
	  }
	  catch (PDOException $e) {
				 die("Error connecting to database.");
	  }
	  return $dbh;
}
function create_pts_dbh() {
	  $setup['DB_HOST']='127.0.0.1';
	  $setup['DB_PORT']=3306;
	  $setup['DB_NAME']='protoshares02';
	  $setup['DB_USER']='root';
	  $setup['DB_PASS']='omitted';
	  try {
				 $dbh = new PDO("mysql:host=" . $setup['DB_HOST'] . ";dbport=" . $setup['DB_PORT'] . ";dbname=" . $setup['DB_NAME'], $setup['DB_USER'], $setup['DB_PASS'], array(
												 PDO::ATTR_PERSISTENT => true
												 ));
	  }
	  catch (PDOException $e) {
				 die("Error connecting to database.");
	  }
	  return $dbh;
}
?>
