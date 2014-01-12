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
            'url': "json/?getFullTransactionPages=PTS",
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
		    'url': "json/?fullTransactionPaginated=PTS&page=" + current_page,
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
		$.each(current_page_data, function(index, element){
		   $.each(element, function(index, element) {
			if (index == 1) elementSub = element.substring(0,8) + "...";
			else elementSub = element;
			$("#testOutput").append(elementSub + " ");
		  }); 
		$("#testOutput").append("<br/>");
		
		});
	}
});
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



<div id="top">
    <div id="logo">
    </div>
    <div id="tagline">
    </div>
</div>

<div id="nav-contain">
<ul id="navbar">
    <a href=".#"><li>Stats and Charts</li></a>
    <a href="lookup.php"><li>Balance Lookup</li></a>
    <li>FAQ</li>
</ul>
</div>

<div id="content">
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
