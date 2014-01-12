#!/usr/bin/php
<?
include '/home/ubuntu/florincoin_block_explorer/api/block_explorer.php';
include '/home/ubuntu/florincoin_block_explorer/class/block_parser.php';
$block_parser  = new block_parser($r, $dbh, 0, 0);
if ($block_parser->get_lockdb() == 1) exit("DB LOCKED - TRY AGAIN LATER\r\n");
else {$block_parser->set_lockdb(1); echo "Begin function - locking DB...\r\n";}

$our_block = new_blocks($f->dbh);
if ($our_block === FALSE) {
	$block_parser->set_lockdb(0);
	exit("\r\nNo new blocks found, exiting...\r\n");
}

// delete past 5 blocks of data
$our_block_delete = $our_block-5;
if ($our_block_delete < 0) $our_block_delete = 0;
$f->dbh->query("delete from agsinfo where block > $our_block_delete");
echo "Starting our search at block $our_block_delete...\r\n";

$block_explorer = $f;
$ags_addr = 'PaNGELmZgzRQCKeEKM6ifgTqNkC4ceiAWw';
$all_ags_txs = array_reverse($block_explorer->getTxsFromAddress($ags_addr));

$txids = get_ags_txs($all_ags_txs);

$day = -1;
$donated_today = 0;
$current_date = "2013-12-26";
$new_date = "";
$todays_date = date('Y-m-d');
$date = new DateTime();
$date->sub(new DateInterval('P1D'));
$yesterdays_date = $date->format('Y-m-d');

$days = array();

echo "\r\n=== Finding all protoshares addr associated with the addresses found ===\r\n";
foreach ($txids as $txid) {
	echo "txid $txid ";
	$resp = $f->dbh->query("select id from tx where hash = '$txid' and block > $our_block_delete");
	$tx_db_id = $resp->fetch();
	$tx_db_id = $tx_db_id[0];
	if (!$tx_db_id) {
		echo "... exists already!\r\n"; 
		continue;
	} else echo "\r\n";

	$resp2 = $f->dbh->query($b = "select * from vin where tx_db_id = $tx_db_id");
	$vin_from_db = $resp2->fetchAll();
	$assoc = -1;
	for ($i = 0; $i < count($vin_from_db); $i++) {
		$vin = $vin_from_db[$i];
		$vin_txid = $vin['txid'];
		// get the previous id of this input's txid
		$q = $f->dbh->query("select id from tx where hash = '$vin_txid'");
		$r = $q->fetchAll();
		$prev_tx_db_id = $r[0][0];

		// find the address each vin is "from"
		$q = $f->dbh->query($b = 'select address from vout_address as A join (select id from vout where n = ' . (int)$vin['vout'] . ' and tx_db_id = ' . $prev_tx_db_id . ') as B where A.vout_id = B.id');
		$r = $q->fetchAll();
		$address = $r[0][0];
			
		// check if this address exists in the database with an association ID already
		$q = $f->dbh->query("select assoc from agsinfo where address = '$address'");
		$r = $q->fetchAll();
		if (isset($r[0])) { $assoc = $r[0][0]; }
		else {
			// if we don't have an association with this address yet, create it.
			if ($i == 0) {
				$q = $f->dbh->query("select MAX(assoc) from agsinfo");
				$r = $q->fetchAll();
				$assoc = $r[0][0]+1;
			}
			// else: we're still in another tx, let's keep the assoc with the "primary" address (listed in vin[0] but not give it any value
		}

		// find the block this tx is in, get block height and time from the same query!
		$q = $f->dbh->query("select id, time from block as A join (select block from tx where id = $tx_db_id) as B where A.id = B.block");
		$r = $q->fetchAll();

		$block = $r[0][0];
		$blocktime= $r[0][1];
		$amount = 0;
		// we found a "primary" address
		if ($i == 0) {
			// get the amount donated to the AGS address
			$amount = $f->getTotalOutputsFromTxByAddress($txid, $ags_addr);
		}

		echo "  block $block, $address, $assoc, $amount, $tx_db_id, $blocktime\r\n";
		$f->dbh->query($b = "insert into agsinfo (address, assoc, amount, txid, tx_db_id, block, blocktime) values ('$address', $assoc, $amount, '$txid', $tx_db_id, $block, $blocktime)");
		$f->dbh->query("update control set value = '$block', entry = unix_timestamp(now()) where name = 'ags_lastblock'");
	}
}

$block_parser->set_lockdb(0);


function new_blocks($dbh) {
	$q = $dbh->query("select max(id) from block");
	$r = $q->fetchAll(PDO::FETCH_COLUMN, 0);
	$maxblock = (int)$r[0];
	
	
	$q = $dbh->query("select value from control where name = 'ags_lastblock'");
	$r = $q->fetchAll(PDO::FETCH_COLUMN, 0);
	$our_block = (int)$r[0];
	if ($maxblock > $our_block) return $our_block;
}
function get_ags_txs($txs_from_addr) {
	foreach ($txs_from_addr as $tx) {$rv[] = $tx['hash'];}
	return $rv;
}

?>
