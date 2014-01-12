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

function getAngelSharesTotal(unixtime){
    var days = (function() {
        var coinsjson = null;
        $.ajax({
            'url': "json/?dayTransactions="+unixtime,
            'dataType': "json",
	    'async': false,
            'success': function (data) {
                coinsjson = data;
		console.log(coinsjson);
            }
        });
	console.log(coinsjson);
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
<input type = "text" id = "time" name = "time">
<div onclick = "getAngelSharesTotal($('#time').val())">SUBMIT</div>

</div>

<div id="footer">
</div>

</div>
</body>
</html>
