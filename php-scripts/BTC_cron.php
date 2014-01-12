#!/usr/bin/php
<?
// metacoin 2014
include '/home/ubuntu/florincoin_block_explorer_bitcoin/api/block_explorer.php';
include '/home/ubuntu/florincoin_block_explorer_bitcoin/class/block_parser.php';

$block_parser  = new block_parser($r, $dbh, 0, 0);
$block_explorer = $f;
//if ($block_parser->get_lockdb() == 1) exit("DB LOCKED - TRY AGAIN LATER\r\n");
//else {$block_parser->set_lockdb(1); echo "Begin function - locking DB...\r\n";}

$our_block = new_blocks($f->dbh, $block_parser);
echo "#### Returned: $our_block\r\n";
var_dump($our_block);
if ($our_block === FALSE) {
	$block_parser->set_lockdb(0);
	exit("\r\nNo new blocks found, exiting...\r\n");
}

// delete past 5 blocks of data
$our_block_delete = $our_block-5;
if ($our_block_delete < 0) $our_block_delete = 0;
echo "Starting our search at block $our_block_delete...\r\n";

$f->dbh->beginTransaction();
if ($start_delete = check_txid_with_rpc($our_block_delete, $block_explorer)) {
	$f->dbh->query($q = "delete from agsinfo where block >= $start_delete");
	echo "QUERY: $q\r\n";
} 

// given a blockheight, check txids from here 
function check_txid_with_rpc($height, $block_explorer) {
	$maxRPCblock = $block_explorer->rpc->call("getblockcount", 0, 0, 0);
	for ($i = $height; $i <= $maxRPCblock; $i++) {
		echo "block $i  checking transactions in database...\r\n";
		$q = $block_explorer->dbh->query("select distinct txid from agsinfo where block = $i");
		$r = $q->fetchAll(PDO::FETCH_COLUMN, 0);
		if (count($r)) {
			// check against RPC
			$rpcBLOCK = ($block_explorer->getBlockFromRPC(null, $i));
			$rpcTXList = $rpcBLOCK['tx'];
			// foreach ($rpcTXList as $tx) echo "$tx\r\n";
			foreach ($r as $txid) {
				echo "block $i  $txid checking if this txid is in the RPC... ";
				if (in_array($txid, $rpcTXList)) echo "FOUND - no problem!\r\n";
				else {
					echo "NOT FOUND - WE MUST REORG HANDLE FROM HERE (block: $i)!!\r\n";
					return $i;
				}
			}
	
		}
	}
	echo "\r\nNo problems found with reorg or txids...\r\n";
}




$ags_addr = '1ANGELwQwWxMmbdaSWhWLqBEtPTkWb8uDc';
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
	//echo "txid $txid ";
	$resp = $f->dbh->query("select id from tx where hash = '$txid' and block > $our_block_delete and inactive is null");
	$tx_db_id = $resp->fetch();
	$tx_db_id = $tx_db_id[0];

	$resp = $f->dbh->query("select id from agsinfo where txid = '$txid'");
	$in_agsinfo = $resp->fetch();
	if (!$tx_db_id || $in_agsinfo) {
		//echo "... exists already!\r\n"; 
		continue;
	} else echo "\r\n";

	$resp2 = $f->dbh->query($b = "select * from vin where tx_db_id = $tx_db_id and inactive is null");
	$vin_from_db = $resp2->fetchAll();
	$assoc = -1;
	for ($i = 0; $i < count($vin_from_db); $i++) {
		$vin = $vin_from_db[$i];
		$address = $f->getAddressFromTxidAndVout($vin['txid'], $vin['vout']);
			
		if (!$address) {
			echo "\r\n" . $vin['txid'] . " -- $txid\r\n";
			continue;
		}
		// check if this address exists in the database with an association ID already
		$q = $f->dbh->query("select assoc from agsinfo where address = '$address'");
		if ($q) $resp = $q->fetchAll();
		if (isset($resp[0])) { $assoc = $resp[0][0]; }
		else {
			// if we don't have an association with this address yet, create it.
			if ($i == 0) {
				$q = $f->dbh->query("select MAX(assoc) from agsinfo");
				if ($q) $resp = $q->fetchAll();
				else $resp[0][0] = 0;
				$assoc = $resp[0][0]+1;
			}
			// else: we're still in another tx, let's keep the assoc with the "primary" address (listed in vin[0] but not give it any value
		}

		// find the block this tx is in, get block height and time from the same query!
		$q = $f->dbh->query("select id, time from block as A join (select block from tx where id = $tx_db_id and inactive is null) as B where A.inactive is null and A.id = B.block");
		$resp = $q->fetchAll();

		$block = $resp[0][0];
		$blocktime= $resp[0][1];
		$amount = 0;
		// we found a "primary" address
		if ($i == 0) {
			// get the amount donated to the AGS address
			$amount = $f->getTotalOutputsFromTxByAddress($txid, $ags_addr);
		}

		$q = $f->dbh->query("select max(id) from block where inactive is null");
		$r = $q->fetchAll(PDO::FETCH_COLUMN, 0);
		$maxblock = (int)$r[0];

		echo "  block $block, $address, $assoc, $amount, $tx_db_id, $blocktime\r\n";
		$f->dbh->query($b = "insert into agsinfo (address, assoc, amount, txid, tx_db_id, block, blocktime) values ('$address', $assoc, $amount, '$txid', $tx_db_id, $block, $blocktime)");
		$f->dbh->query("update control set value = '$maxblock', entry = unix_timestamp(now()) where name = 'ags_lastblock'");
	}
}
$f->dbh->commit();
$block_parser->set_lockdb(0);


function new_blocks($dbh, $block_parser) {
	$q = $dbh->query("select max(id) from block where inactive is null");
	$r = $q->fetchAll(PDO::FETCH_COLUMN, 0);
	$maxblock = (int)$r[0];
	echo "#### Our maxblock: $maxblock\r\n";
	
	
	$q = $dbh->query("select value from control where name = 'ags_lastblock'");
	$r = $q->fetchAll(PDO::FETCH_COLUMN, 0);
	$our_block = (int)$r[0];
	echo "#### Our lastblock: $our_block\r\n";
	if (($maxblock - $our_block) > 10) {
		echo ("\r\n***** WARNING: DIFFERENCE > 10 ($maxblock - $our_block) **** \r\n");
	}
	if ($maxblock > $our_block) return $our_block;
	return false;
}

function get_ags_txs($txs_from_addr) {
	foreach ($txs_from_addr as $tx) {$rv[] = $tx['hash'];}
	return $rv;
}

?>
