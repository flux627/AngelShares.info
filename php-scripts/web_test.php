<?
include '/home/ubuntu/fluxexplorer/api/block_explorer.php';

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

foreach ($txids as $txid) {
	// get the amount from this transaction
	$amount = $f->getTotalOutputsFromTxByAddress($txid, $ags_addr);
	$donated_today += $amount;

	// get the address this transaction is from
	$vin = ($block_explorer->getVinFromRPC($txid));
	$in = $vin[0];
	$address = $block_explorer->getAddressFromTxidAndVout($in['txid'], $in['vout']);

	// display some relevant data
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
			$days[$current_date] = $donated_today;
			$donated[$address][3] = $ags_get;
		}
		$current_date = $new_date;
		$donated_today = 0;
	}
}
foreach ($donated as $address=>$vals) {
	if ($vals[2] != $current_date) continue;
	$ags_get = ($vals[1] / $donated_today) * 5000;
	$days[$current_date] = $donated_today;
	$donated[$address][3] = $ags_get;
}
$current_date = $new_date;
$donated_today = 0;

function get_ags_txs($txs_from_addr) {
	foreach ($txs_from_addr as $tx) {$rv[] = $tx['hash'];}
	return $rv;
}

function add_unique_address(&$arr, $addr) {
	if (!in_array($addr, $arr)) $arr[] = $addr;
}
?>
