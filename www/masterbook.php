<? 
function curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $resp = json_decode(curl_exec($ch), 1);
        curl_close($ch);
        return $resp;
}
?>

<html>
 
<head>
    <title>AngelShares.info</title>
    <meta http-equiv="content-type" 
        content="text/html;charset=utf-8" />

    <!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->
    <script language="javascript" type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="js/jqp/jquery.jqplot.css" />

    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="stylesheet" type="text/css" href="table_style.css" />

</head>

<body>
<style>
table {width: 100%;}
.masterbook_page { display: none; }
#pagenav #back, #pagenav #next {color: skyBlue; cursor: pointer}
#pagenav span {margin-right: 5px}
h2, #btcpts {margin: 15px 0}
#btcpts a {margin: 0 0 0 1px; text-decoration: none}
#pts table td a, #btc table td a {font-weight: normal;}
</style>
<div id="wrap">
<? include('templates/top.php') ?>

	<div id="content">
		<h2>AngelShares Masterbook - Bitcoin</h2>
		<div id = "btcpts">
			<span id = "pts"><a href = "masterbook_pts.php">Switch to PTS</a></span>
		</div>
		<div id = "btc">
			<div id = "pagenav">
				<span id = "btcpage"></span>
				<span id = "back">Previous</span>
				<span id = "next">Next</span>
			</div>
			<?
			$btc = curl('http://angelshares.info/json/?allAddressesPaginated');
			$btc = $btc['BTC'];
			$pages = count($btc);
			for ($i = 1; $i <= $pages; $i++) {
				echo "<div class = 'masterbook_page' id = 'page_$i'><table><tr><th>Address (click for more info)</th><th>AGS</th></tr>";
				foreach ($btc[$i] as $key=>$value) {
					echo "<tr><td><a href = 'lookup.php?addr=" . key($value) . "'>" . key($value) . "</a></td><td>" . $value[key($value)] . "</td></tr>";
				}
				echo "</table></div>";
			}
				
			


			?>
		</div>
		<div id = "pts">

		</div>
	</div>
	<div id="footer">
	</div>

</div>

<script>
var current_page = 1;
var max_pages = <? echo $pages; ?>;
$(document).ready(function() {
	$("#back").click(function() {
		if (current_page > 1) {
			current_page--;
			update();
		}
	});
	$("#next").click(function() {
		if (current_page < max_pages) {
			current_page++;
			update();
		}
	});
	console.log("max_pages: " + max_pages);
	function update() {
		// hide all other pages except current page
		for (var i = 1; i <= max_pages; i++) $("#page_" + i).css("display", "none");
		$("#page_" + current_page).css("display", "block");

		// update page nav
		$("#btcpage").html('Page ' + current_page + '/' + max_pages);
	}
	update();

});
</script>
</body>
</html>
