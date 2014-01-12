#!/usr/bin/php
<?
include ('class/florin_rpc.php');
include ('class/block_parser.php');
include ('setup/setup.php');

if ($argv[1] == "help" || $argv[1] == "-h" || $argv[1] == "-help") { include ('doc/help.php'); }

$options = getopt("a:s:b:", array("debug", "delete"));

$r = new florin_RPC_client($setup["FLORIND"], 5, $rpc_setup);
$f = new block_parser($r, $dbh, isset($options['debug']), $options['a']); 
// test rpc calls via jsonRPCClient.php
$startTime = microtime(true); 

/* BEGIN */

switch ($options['a']) {
	case "fill":	
      $f->set_lockdb(1);
		$f->fillInBlanks(isset($options['delete']), isset($options['deleteall']), (int)($options['s']));
      $f->set_lockdb(0);
		break;
	case "redo": 
      $f->set_lockdb(1);
		$f->fillInBlanks(1, 1);
      $f->set_lockdb(0);
		break;
	case "rescan":
	        $f->set_lockdb(1);
		$f->rescanTest((int)($options['s']), (int)($options['b']));
      		$f->set_lockdb(0);
		break;
	case "rescanTest":
	        $f->set_lockdb(1);
		$f->rescanTest((int)($options['s']), (int)($options['b']));
      		$f->set_lockdb(0);
		break;
	case "test":
		$f->testSimple();
		break;
	case "msgs":
		$f->testScanComments((int)($options['s']));
		break;
   case "unlock":
      $f->set_lockdb(0);
      break;
   case "lock":
      $f->set_lockdb(1);
      break;
	case "height":
		$h = $f->getHeight();
		echo "Current block: " . $h[1] . "\r\n";
		echo "Max block in DB: " .$h[2] . "\r\n";
		break;
	case "setup":
      $f->initial_setup();
      break;
	default:
		echo "No command selected. Try passing a command as the first argument (argv[1]).\r\n";
		break;
}

/*  END  */

$endTime = microtime(true);  
$elapsed = $endTime - $startTime;
echo "Execution time : $elapsed seconds\r\n";

?>
