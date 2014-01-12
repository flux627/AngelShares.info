#!/usr/bin/php
<?

include '../fluxexplorer/api/block_explorer.php';

$block_explorer = $f;
$ags_addr = 'PaNGELmZgzRQCKeEKM6ifgTqNkC4ceiAWw';
$all_ags_txs = array_reverse($block_explorer->getTxsFromAddress($ags_addr));

echo "Finding all ProtoShares addresses that have donated to AngelShares...\r\n";
echo "\r\n";
$txids = get_ags_txs($all_ags_txs);

$day = -1;
$donated_today = 0;
$current_date = "2013-12-26";
$new_date = "";

echo "Today is a new day! $current_date\r\n";
foreach ($txids as $txid) {
	// get the amount from this transaction
	$amount = $f->getTotalOutputsFromTxByAddress($txid, $ags_addr);
	$donated_today += $amount;

	// get the address this transaction is from
	$vin = ($block_explorer->getVinFromRPC($txid));
	$in = $vin[0];
	$address = $block_explorer->getAddressFromTxidAndVout($in['txid'], $in['vout']);

	// display some relevant data
	echo "$txid $address $amount\r\n";
	$donated[$address] = array($txid, $amount, $current_date, 0);

	// are we on a new day?
	$tx = ($block_explorer->getTxByHash($txid));
	$block = $block_explorer->getBlockById($tx['block']);
	$unix_time = $block['time'];
	$new_date = date('Y', $unix_time) . "-" . date('m', $unix_time) . "-" . date('d', $unix_time);

	// if so, do new day stuff
	if ($new_date != $current_date) {
		$day++;
		foreach ($donated as $address=>$vals) {
			if ($vals[2] != $current_date) continue;
			$ags_get = ($vals[1] / $donated_today) * 5000;
			echo "  $address submitted " . $vals[1] . " PTS to $ags_addr on this day, netting " . $ags_get . " AngelShares.\r\n";
			$donated[$address][3] = $ags_get;
		}
		$current_date = $new_date;
		$donated_today = 0;
		echo "\r\nToday is a new day! $current_date\r\n";
	}
	
}
echo "\r\n========================================\r\n\r\nFinding all ProtoShares addresses associated with the above addresses...\r\n";

$associated_addresses = array();

// look for addresses associated with the "first address"
foreach ($txids as $txid) {
	$vin = ($block_explorer->getVinFromRPC($txid));
	$address = null;
	$addresses = array();

	// find each "first address" and all addresses in the same transaction listed as subsequent vins
	foreach ($vin as $key=>$in) {
		$address = $block_explorer->getAddressFromTxidAndVout($in['txid'], $in['vout']);
		if ($key == 0) {
			$first_address = $address;
			$addresses[$address] = array();
			continue;
		}
		if ($address != $first_address) add_unique_address($addresses[$first_address], $address);
	}
	$associated_addresses[] = $addresses;
}

// print em
foreach ($associated_addresses as $addresses) {
	foreach ($addresses as $key=>$val) {
		if (count($val) < 1) break;
		echo "\r\n$key\r\n";
		foreach ($val as $addr) {
			echo "  $addr\r\n";
		}
	}
}



echo "\r\nDone.\r\n\r\n";

function get_ags_txs($txs_from_addr) {
	foreach ($txs_from_addr as $tx) {$rv[] = $tx['hash'];}
	return $rv;
}

function add_unique_address(&$arr, $addr) {
	if (!in_array($addr, $arr)) $arr[] = $addr;
}
?>
