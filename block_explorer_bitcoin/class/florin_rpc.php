<?
// TODO: fix the fucking call args .. should be an array x_x

include (__DIR__ . '/../lib/jsonrpcphp/includes/jsonRPCClient.php');

interface florin_blockchain {
	function spawnDaemon($rpc_setup);
   function call($command, $arg, $arg2, $txcomment);
}

/* florin_RPC_client allows for RPC calls from PHP...
   florin_exe = the florincoind executable
   florind_reconnect_tries = amount of times to retry to connect to daemon
*/

class florin_RPC_client implements florin_blockchain {
	function __construct($florin_exe, $tries, $rpc_setup) { 
		$this->florin_exe = $florin_exe;
		$this->florind_reconnect_tries = $tries;
		$this->spawnDaemon($rpc_setup);
	}

   // pass in rpc setup from setup.php / setup.conf - spawn jasonRPCClient
	function spawnDaemon($rpc_setup) {	
		extract($rpc_setup);
		try {
			$this->florind = new jsonRPCClient("http://$RPC_USER:$RPC_PASS@$RPC_HOST:$RPC_PORT/");
		} catch (Exception $e) {
			echo "Could not connect to florincoind, attempting again...\r\n";
		}
		if ($this->florind) {} 
		else exit("FATAL ERROR: cannot connect to daemon");
	}

	// warning: $arg is type sensitive
   // more info on calls can be found here: https://en.bitcoin.it/wiki/API_reference_(JSON-RPC)
   // also find info on what each call does: https://en.bitcoin.it/wiki/Original_Bitcoin_client/API_Calls_list
	function call($command, $arg, $arg2, $txcomment) {
		try {
       switch ($command) {
            case "listaddressesbyaccount":
               $rv = $this->florind->listaddressesbyaccount($arg);
               break;
            case "getnetworkhashps":
               $rv = $this->florind->getnetworkhashps();
               break;
            case "getrawmempool":
               $rv = $this->florind->getrawmempool();
               break;
            case "getblockcount":
               $rv = $this->florind->getblockcount();
               break;
            case "getblockhash":
               $rv = $this->florind->getblockhash($arg);
               break;
            case "getblock":
               $rv = $this->florind->getblock($arg);
               break;
            case "getrawtransaction":
               $rv = $this->florind->getrawtransaction($arg);
               break;
            case "decoderawtransaction":
               $rv = $this->florind->decoderawtransaction($arg);
               break;
            case "signrawtransaction":
               $rv = $this->florind->signrawtransaction($arg);
               break;
            case "sendrawtransaction":
               $rv = $this->florind->sendrawtransaction($arg);
               break;
            case "createrawtransaction":
//            echo "<pre>$arg<br/>$arg2<br/>$txcomment</pre>";
//            $args=json_encode(array(($arg), ($arg2), ($txcomment)));
//            var_dump($args);
               $rv = $this->florind->createrawtransaction($arg, $arg2, $txcomment);
//               $rv = $this->florind->createrawtransaction($args);
//               $rv = $this->florind->createrawtransaction(escapeshellarg($arg), escapeshellarg($arg2), escapeshellarg($txcomment));
               break;
            default:
               echo "Incorrect input parameters\r\n";
               return false;
               break;
         }
      }
		catch (Exception $e) {
			echo "Could not successfully call " . $command . " with given arguments.\r\n";
		}
		return $rv;
	}
}
/*
$test = new florin_RPC_client($florin_d, 5);

$test->call("getnetworkhashps");
$block = $test->call("getblockhash", 5);
var_dump($block);
$block_decode = $test->call("getblock", $block);
var_dump($block_decode);
*/
?>
