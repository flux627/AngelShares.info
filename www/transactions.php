<html>
 
<head>
    <title>AngelShares.info</title>
    <meta http-equiv="content-type" 
        content="text/html;charset=utf-8" />

    <!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->
    <script language="javascript" type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
    <script language="javascript" type="text/javascript" src="js/jqp/jquery.jqplot.min.js"></script>
    <script language="javascript" type="text/javascript" src="js/jqp/plugins/jqplot.barRenderer.min.js"></script>
    <script language="javascript" type="text/javascript" src="js/jqp/plugins/jqplot.dateAxisRenderer.min.js"></script>
    <script language="javascript" type="text/javascript" src="js/jqp/plugins/jqplot.highlighter.min.js"></script>
    <link rel="stylesheet" type="text/css" href="js/jqp/jquery.jqplot.css" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="stylesheet" type="text/css" href="table_style.css" />

    <script>
var api_pages = 0;
var current_page = 0;
$(document).ready(function() {
	
	$("#back").click(function() {
		if (current_page <= 0) { current_page = 0; return; }
		current_page--;
		draw_api_output();
	});
	$("#next").click(function() {
		if (current_page >= api_pages) { current_page = api_pages; return; }
		current_page++;
		draw_api_output();
	});
    var api_pages = (function() {
        var coinsjson = null;
        $.ajax({
            'url': "json/?getFullTransactionPages=BTC",
            'dataType': "text",
	    'async': false,
            'success': function (data) {
                coinsjson = data;
            }
        });
        return coinsjson;
    })();

	// draw select input box
	$("#selectPage").html('');
	$("#selectPage").append('<option value = "0">Jump to a page...</option>');
	for (var i = 0; i < api_pages; i++) {
		$("#selectPage").append('<option value = ' + i + '>' + (i+1) + '</option>');
	}

	// cause select input box to change page data
	$('#selectPage').change(function(){
		current_page = $('#selectPage').val();
		console.log("current_page is now : " + current_page);
		    draw_api_output();
	});

	// draw current page data
	draw_api_output();
	function draw_api_output() {
		$("#testOutput").html('');
	    var current_page_data = (function() {
		var coinsjson = null;
		$.ajax({
		    'url': "json/?fullTransactionPaginated=BTC&page=" + current_page,
		    'dataType': "json",
		    'async': false,
		    'success': function (data) {
			coinsjson = data;
		    }
		});
		return coinsjson;
	    })();
		var current_page_display = parseInt(current_page)+1;
		$("#pageNumber").html("Page: " + current_page_display + "/" + api_pages);
		/*$("#testOutput").append("<table><tr><th>Address (click for detailed info)</th><th>txid</th><th>Amount (BTC)</th><th>Block</th><th>Time</th></tr>");
		$.each(current_page_data, function(index, element){
		   $("#testOutput").append("<tr>");
		   $.each(element, function(index, element) {
			if (index == 1) elementSub = element.substring(0,8) + "...";
			else elementSub = element;
			$("#testOutput").append("<td>" + elementSub + "</td>");
		  }); 
		  $("#testOutput").append("</tr>");
		});
		$("#testOutput").append("</table>");
		*/

		var tablething = "<table style = 'width: 100%'><tr><th>Address (click for detailed info)</th><th>txid</th><th>Amount (BTC)</th><th>Block</th><th>Time</th></tr>";
		$.each(current_page_data, function(index, element){
		   tablething += "<tr>";
		   $.each(element, function(index, element) {
			if (index == 0) elementSub = "<a href = 'lookup.php?addr=" + element + "'>" + element + "</a>";
			else if (index == 1) elementSub = element.substring(0,8) + "...";
			else if (index == 4) elementSub = getdate(element);
			else elementSub = element;
			tablething += "<td>" + elementSub + "</td>";
		  }); 
		  tablething += "</tr>";
		});
		tablething += "</table>";
		$("#testOutput").append(tablething);
	}
});

function getdate(timestamp){
    var date = new Date(parseInt(timestamp));
    return date;
}


function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}
function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}

function getAngelSharesByPage(page){
    var days = (function() {
        var coinsjson = null;
        $.ajax({
            'url': "json/?dayTransactions="+unixtime,
            'dataType': "json",
	    'async': false,
            'success': function (data) {
                coinsjson = data;
            }
        });
        return coinsjson;
    })();
}
</script>

</head>

<body>

<div id="wrap">

<? include('templates/top.php') ?>


<div id="content">
<h2>Bitcoin Transactions</h2>
<span style = "margin: 0 0 10px 2px"><a style = "text-decoration: none" href = "transactions_pts.php">Switch to ProtoShares</a></span> <br/><br/>
<div id = "pageNavTop">
	<span id = "pageNumber"></span>
	<span class = "navButton" id = "back">Previous</span>
	<span class = "navButton" id = "next">Next</span>
	<div id = "selectPageDiv">
		<select id = "selectPage">
		</select>
	</div>
</div>
<div id = "testOutput"></div>
<div id = "pages"></div>
</div>

<div id="footer">
</div>

</div>
<style>
#pageNavTop span {margin-right: 5px;}
#pageNavTop span.navButton {cursor: pointer; color: skyBlue}
#pageNavTop #selectPageDiv {display: inline-block;}
</style>
</body>
</html>
