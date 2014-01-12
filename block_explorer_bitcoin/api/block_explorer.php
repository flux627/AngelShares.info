<?
// this file is used to display the block explorer info to the web


// TODO: organize these functions into DB/RPC buckets
// make an include file for interface stuff ... prettyprint etc

include(__DIR__ . '/../setup/setup.php');
include(__DIR__ . '/../class/florin_rpc.php');
$r = new florin_RPC_client($setup["FLORIND"], 5, $rpc_setup);
$f = new block_explorer($r, $dbh);

class block_explorer {
	function __construct($r, $dbh) {
		$this->rpc = $r;
		$this->dbh = $dbh;	
	}

	public function d_print($msg) {
		if ($_SERVER['REMOTE_ADDR'] == 'myip') { var_dump($msg); }
	}

	public function isTXinRPC($hash) {
		return $this->rpc->call('getrawtransaction', $hash, 0, 0);
	}
	
	public function getTXFromRPC($hash) {
        $raw_tx= trim($this->rpc->call('getrawtransaction', $hash, 0, 0));
        return $this->rpc->call('decoderawtransaction', $raw_tx, 0, 0);
	}

	public function getBlockFromRPC($hash, $id) {
		if ($hash) {return $this->rpc->call('getblock', $hash, 0, 0);}
		return $this->getBlockFromRPC(trim($this->rpc->call('getblockhash', $id, 0, 0)), 0, 0, 0);
	}

   public function getVinFromRPC($txid) {
      $rtx = $this->rpc->call('getrawtransaction', $txid, 0, 0);
      $dtx = $this->rpc->call('decoderawtransaction', $rtx, 0, 0);
      return $dtx['vin'];
   }

   public function getAddressFromTxidAndVout($txid, $vout) {
      $rtx = $this->rpc->call('getrawtransaction', $txid, 0, 0);
      $dtx = $this->rpc->call('decoderawtransaction', $rtx, 0, 0);
      return $dtx['vout'][$vout]['scriptPubKey']['addresses'][0];
   }

   public function getTotalOutputsFromTxByAddress($txid, $address) {
      $rtx = $this->rpc->call('getrawtransaction', $txid, 0, 0);
      $dtx = $this->rpc->call('decoderawtransaction', $rtx, 0, 0);
      $total = 0;
      foreach ($dtx['vout'] as $vout) {
         if (in_array($address, $vout['scriptPubKey']['addresses'])) {$total += $vout['value'];}
      }
      return $total;
   }

	public function getBlockByID($block_id) {
		$r = $this->dbh->prepare("select * from block where id = :block_id");
		$r->bindValue(':block_id', $block_id);
		$r->execute();
		return $r->fetch();
	}

	public function getBlockByHash($block_hash) {
		$r = $this->dbh->prepare('select * from block where hash = ?');
		$r->bindParam(1, $block_hash, PDO::PARAM_STR, strlen($block_hash));
		$r->execute();
		return $r->fetch();
	}

	public function getTXById($tx_id) {
		$r = $this->dbh->prepare("select * from tx where id = :tx_id");
		$r->bindValue(':tx_id', $tx_id);
		$r->execute();
		return $r->fetch();
	}

	public function getTxByHash($tx_hash) {
		$r = $this->dbh->prepare('select * from tx where hash = ?');
		$r->bindValue(1, $tx_hash, PDO::PARAM_STR);
		$r->execute();
		return($r->fetch());
	}

	public function getRecentBlocks($num_blocks) {
		$r = $this->dbh->query("select value from control where name = 'lastblock'");
		$v = $r->fetch();
		for ($i = 0; $i < $num_blocks; $i++) {
			$blocks[] = $this->getBlockById($v[0] - $i);	
		}
		return $blocks;
	}

	public function getTxsInBlock($block_id) {
		$r = $this->dbh->prepare("select hash from tx where block = :block_id");
		$r->bindValue(':block_id', $block_id);
		$r->execute();
		return $r->fetchAll(PDO::FETCH_ASSOC);
	}

   public function getTotalCoinsMinted() {
      $r = $this->dbh->query("select sum(outputs) from tx where coinbase = 1");
      return $r->fetch(PDO::FETCH_NUM);
   }

   public function getOutputFromTx($tx_hash) {
      $r = $this->dbh->query("select outputs from tx where tx = '$tx_hash'");
      return $r->fetch(PDO::FETCH_NUM);
   }

   public function getOutputFromHashAndVout($tx_hash, $vout) {
      $r = $this->dbh->query("select value from vout as a join (select id from tx where hash = '$tx_hash') as b where n = $vout and tx_db_id = b.id limit 1;");
      return $r->fetch(PDO::FETCH_NUM);
   }   

   public function getTxHashFromDBID($tx_db_id) {
      $r = $this->dbh->query("select hash from tx where id = $tx_db_id LIMIT 1");
      $v = $r->fetch(PDO::FETCH_NUM);
      return $v[0];
   }


   public function getTxsFromAddress($addr) {
      /* $r = $this->dbh->prepare("select distinct value, tx_db_id from vout as A join (select vout_id from vout_address where address = ?) as B where A.id = B.vout_id"); */
      $r = $this->dbh->prepare("select distinct time,  F.value, F.hash from block as G join (select D.value, hash, block from tx as C join (select value, tx_db_id from vout as A join (select vout_id from vout_address where address = ?) as B where A.id = B.vout_id) as D where D.tx_db_id = C.id) as F where F.block = G.id order by time desc");
      $r->bindValue(1, $addr, PDO::PARAM_STR);
      $r->execute();
      return $r->fetchAll(PDO::FETCH_ASSOC);
   }

}

function satoshi($num) { return number_format((float)($num/100000000), 8, '.', ''); }

// thanks Kendall Hopkins http://stackoverflow.com/a/9776726/2576956
function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $prev_char = '';
    $in_quotes = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if( $char === '"' && $prev_char != '\\' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "  ": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "  ", $new_line_level );
        }
        $result .= $char.$post;
        $prev_char = $char;
    }

    return $result;
}
?>
