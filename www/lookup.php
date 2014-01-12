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

</head>

<body>

<div id="wrap">

<? include('templates/top.php') ?>

<div id="content">

<div id="spacer"></div>

<div id="form">

<form action="lookup.php" method="get">
  <input id="addrinput" name="addr" placeholder="Paste your PTS or BTC Address Here">
  <button id="addrsubmit">Submit</button>
</form>

</div>

<div id="result" style="display:none">
<p class="intro">Address:</p>
<p id="address"></p>
<p class="intro">Total <span id="coinfull"></span> Sent:</p>
<p id="total-coin"></p>
<p class="intro">Total AngelShares:</p>
<p id="total-ags"></p>
<p class="intro" id = "rel-addr-title">Associated Addresses:</p>
<span id="rel-addr"></span>

<div id="ags-chart"></div>
<div id = "ags-txs"><h2>Full Transaction List For <? echo $_GET['addr']; ?></h2></div>
</div>



</div>

<div id="footer">
</div>

</div>

<script>

function roundTo(num,dec_places) {
    var power = Math.pow(10, dec_places)
    return Math.round(num * power)/power
};

function nC(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

function roundTo3(num) {
    return roundTo(num,3);
};

function getQueryVariable(variable)
{
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}

var addr = getQueryVariable("addr");

var result = (function() {
    var json = null;
    $.ajax({
        'async': false,
        'global': false,
        'url': "json/?address=" + addr,
        'dataType': "json",
        'success': function (data) {
            json = data;
        }
    });
    return json;
})();

function getAngelSharesTotal(transactions,coinshort){
    var days = (function() {
        var coinsjson = null;
        $.ajax({
            'async': false,
            'global': false,
            'url': "json/?coinsAmount",
            'dataType': "json",
            'success': function (data) {
                coinsjson = data;
            }
        });
        return coinsjson[coinshort];
    })();

    var today = days[days.length-1][0]

    var agsTotal = 0;
    var dayStats = {};

    for (var i=0; i<transactions.length; i++) {
        for (var j=0; j<days.length-1; j++) {

            console.log(transactions[i][2])
            console.log(days[j+1][0])

            if (transactions[i][2]<days[j+1][0]){
                console.log("hello?")
                agsTotal += (parseFloat(transactions[i][1])/days[j][1])*5000;
                if (dayStats[j] == undefined){
                    dayStats[j] = [days[j][0],parseFloat(transactions[i][1]),(parseFloat(transactions[i][1])/days[j][1])*5000];
                } else {
                    dayStats[j][1] += parseFloat(transactions[i][1]);
                    dayStats[j][2] += (parseFloat(transactions[i][1])/days[j][1])*5000;
                }
                break
            }       
        
        }
        dayStats[j+1] = [today,parseFloat(transactions[i][1])]
    };
    return [agsTotal,dayStats,today];
}

function prepareGraphData(dayStats){
    var coinPerDay = [];
    var agsPerDay = [];
    for(var k in dayStats){
        coinPerDay.push([dayStats[k][0],dayStats[k][1]])
        agsPerDay.push([dayStats[k][0],dayStats[k][2]])
    };
    console.log(dayStats)
    return [coinPerDay,agsPerDay]
}

if (addr!=false) {
    $('#spacer').hide();
    $('#result').show();
    var total_coin = result[0];
    var rel_addresses = result[1];
    var txs = result[2].tx;

    $('#address').text(addr);

    var color = "255, 255, 255";
    var coinfull;
    var coinshort;
    if (addr[0]=="P") {
        color = "rgb(173, 255, 255)";
        coinfull = "ProtoShares";
        coinshort = "PTS";
    } else if (addr[0]=="1") {
        color = "rgb(255, 206, 118)";
        coinfull = "Bitcoin";
        coinshort = "BTC";
    }

    var total_ags = getAngelSharesTotal(txs,coinshort);

    var data = prepareGraphData(total_ags[1]);

    console.log(data[0][data[0].length-1][0])
    console.log(total_ags[2])

    var coin_pending;
    if (data[0][data[0].length-1][0] == total_ags[2]){
        coin_pending = data[0][data[0].length-1][1]
    }

    

    $('#address').css('background-color',color);
    $('#coinfull').text(coinfull);
    $('#total-coin').text(roundTo(total_coin,7) + ' ' + coinshort);
    //if (coin_pending != undefined){
    //    $('#total-coin').append(" <span class='pending'>(" + coin_pending + " Pending)</span>");
    //}
    $('#total-ags').text(total_ags[0] + ' AGS');

    // check relative addresses
    var diff_address = false;
    for (var i = 0; i < rel_addresses.length; i++) {
        if (rel_addresses[i] != addr) {
            diff_address = true;
            break;
        }
    }
    if (diff_address) {
        for (var i=0;i<rel_addresses.length;i++)
        { 
            $('#rel-addr').append("<a href = 'lookup.php?addr=" + rel_addresses[i] + "'>" + rel_addresses[i]+"</a><br/>");
        };
    } else {
        $("#rel-addr-title").hide();
    }
    if (data[0][0] !== undefined) renderGraphs(data);
	
	// create tx table
	console.log(result[2].tx);
	var tablething = "<table style = 'width: 100%'><tr><th>txid</th><th>Amount (" + coinshort + ")</th><th>Time</th></tr>";
	$.each(result[2].tx, function(index, element) {
		if (element[1] > 0) {
			tablething += "<tr>";
			$.each(element, function(index, element) {
				if (index == 2) tablething += "<td>" + getdate(element) + "</td>";
				else tablething += "<td>" + element + "</td>";
			});
			tablething += "</tr>";
		}
	});
	tablething += "</table>";
	$("#ags-txs").append(tablething);
}

function getdate(timestamp){
    var date = new Date(parseInt(timestamp));
    return date;
}

function renderGraphs(graphdata){
    
    function timezoneFix(data){
        var UTCoffset = new Date().getTimezoneOffset();
            for (var i=0;i<data.length;i++)
                { 
                    data[i] = [data[i][0]+(UTCoffset*60000), data[i][1]];
                };
        return data;
    };

    function addPadding(data){
        var first = data[0][0]
        var last = data[data.length-1][0]
        var tomorrow = last-(last%86400000)+86400000;
        var yesterday = first-(first%86400000);
        data.push([tomorrow,-1]);
        ret = [[yesterday,0]].concat(data);
        return ret;
    };

    ags_raw = graphdata[1];

    ags_data = addPadding(timezoneFix(ags_raw));

    var barwidth = 640/((ags_data[ags_data.length-1][0]-ags_data[0][0])/86400000)-1;

    var pts_plot = $.jqplot('ags-chart', [ags_data], {
        title:'AngelShares Per Day',
        seriesColors:['rgba(226, 186, 0, 1)'],
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {  
                highlightMouseOver: true,
                barWidth :barwidth
            }
        },
        highlighter: {
            show: true,
            fadeTooltip: true,
            showMarker: false,
            //tooltipFormatString: '%.5P',
            sizeAdjust: 7.5
        },
        axes:{
            xaxis:{
                renderer: $.jqplot.DateAxisRenderer,
                tickOptions:{formatString:'%#m/%#d'},
                tickInterval:'1 day'
            },
            yaxis:{
                min: 0,
                pad: 1.05
            }
        }
    });
}

</script>
</body>
</html>
