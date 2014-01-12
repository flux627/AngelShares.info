<? 
// include('/home/ubuntu/php-scripts/web_test.php');
$exp_date = time() - (time() % 86400) + 86400;
?>

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
// Count down milliseconds = server_end - server_now = client_end - client_now
var server_end = <?php echo $exp_date; ?> * 1000;
var server_now = <?php echo time(); ?> * 1000;
var client_now = new Date().getTime();
var end = server_end - server_now + client_now; // this is the real end time

var _second = 1000;
var _minute = _second * 60;
var _hour = _minute * 60;
var _day = _hour *24
var timer;
 
function showRemaining()
{
    var now = new Date();
    var distance = end - now;
    if (distance < 0 ) {
       clearInterval( timer );
       document.getElementById('countdown').innerHTML = '0 Hours, 0 Minutes, 0 Seconds';

       return;
    }
    var days = Math.floor(distance / _day);
    var hours = Math.floor( (distance % _day ) / _hour );
    var minutes = Math.floor( (distance % _hour) / _minute );
    var seconds = Math.floor( (distance % _minute) / _second );

    var countdown = document.getElementById('countdown');
    countdown.innerHTML = '';
    if (days) {
        countdown.innerHTML += 'Days: ' + days + '<br />';
    }
    countdown.innerHTML += hours+ ' Hours, ';
    countdown.innerHTML += minutes+ ' Minutes, ';
    countdown.innerHTML += seconds+ ' Seconds';
};

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


</script>

</head>

<body>

<div id="wrap">

<? include('templates/top.php') ?>

<div id="content">

<div>
<div id="curcont">
<form id="curform">
    <span style="font-size:22px">Currency:</span>
    <select id="curselect" >
        <option value="AUD">AUD</option>
        <option value="BRL">BRL</option>
        <option value="CAD">CAD</option>
        <option value="CHF">CHF</option>
        <option value="CNY">CNY</option>
        <option value="CZK">CZK</option>
        <option value="EUR">EUR</option>
        <option value="GBP">GBP</option>
        <option value="ILS">ILS</option>
        <option value="JPY">JPY</option>
        <option value="NOK">NOK</option>
        <option value="NZD">NZD</option>
        <option value="PLN">PLN</option>
        <option value="RUB">RUB</option>
        <option value="SEK">SEK</option>
        <option value="SGD">SGD</option>
        <option value="USD" selected="selected">USD</option>
        <option value="ZAR">ZAR</option>
    </select>
</form>
</div>
<span class="title">Today's Stats:</span>
</div>

<div id="countdown-box">
<span id="countdown-text">Time Until Next Day (estimated): </span>
<span id="countdown"></span>
</div>

<div id="proto-ticker">
<p class="ticker">Total Protoshares sent today:</p>
<p class="ticker-num"><span id="pts-today"></span><br/>(<span id="pts-today-cur"></span>)</p>
<p class="ticker-sub"><span id="pts-yesterday"></span><b> / </b><span id="pts-yesterday-cur"></span></p>
<p class="ticker">Current AngelShares Value: </p>
<p class="ticker-num"><span id="pts-ags-today"></span><br/>(<span id="pts-ags-today-cur"></span>)</p>
<p class="ticker-sub"><span id="pts-ags-yesterday"></span><b>, </b><span id="pts-ags-yesterday-cur"></span></p>
<p class="ticker">Current AngelShares Price: </p>
<p class="ticker-num"><span id="pts-ags-ptoday"></span><br/>(<span id="pts-ags-ptoday-cur"></span>)</p>
<p class="ticker-sub"><span id="pts-ags-pyesterday"></span><b>, </b><span id="pts-ags-pyesterday-cur"></span></p>
<p class="ticker">ProtoShares Angel Address:</p>
<p class="address">PaNGELmZgzRQCKeEKM6ifgTqNkC4ceiAWw</p>
</div>

<div id="bitcoin-ticker">

    <p class="ticker">Total Bitcoin sent today:</p>
    <p class="ticker-num">
        <span  id="btc-today"></span><br/>(<span id="btc-today-cur"></span>)
    </p>
    <p class="ticker-sub">
        <span id="btc-yesterday"></span><b> / </b><span id="btc-yesterday-cur"></span>
    </p>
    <p class="ticker">Current AngelShares Value: </p>
    <p class="ticker-num">
        <span id="btc-ags-today"></span><br/>(<span id="btc-ags-today-cur"></span>)
    </p>
    <p class="ticker-sub">
        <span id="btc-ags-yesterday"></span><b>, </b><span id="btc-ags-yesterday-cur"></span>
    </p>
    <p class="ticker">Current AngelShares Price: </p>
    <p class="ticker-num">
        <span id="btc-ags-ptoday"></span><br/>(<span id="btc-ags-ptoday-cur"></span>)
    </p>
    <p class="ticker-sub">
        <span id="btc-ags-pyesterday"></span><b>, </b><span id="btc-ags-pyesterday-cur"></span>
    </p>
    <p class="ticker">Bitcoin Angel Address:</p>
<p class="address">1ANGELwQwWxMmbdaSWhWLqBEtPTkWb8uDc</p>
</div>

<div id="pts-today-chart" class="left-chart"></div>
<div id="btc-today-chart" class="right-chart"></div>

<div class="stretch-box" id="today-stats" style="margin-top: 15px;">

    <div class="equal-spaced">
        <p class="ticker">Current BTC Price:</p>
        <p class="ticker-num">
            <span id="btc-price-cur"></span><br/><span id="btc-price-pts"></span>
        </p>
    </div>
    <div class="equal-spaced">
        <p class="ticker">Current PTS Price:</p>
        <p class="ticker-num">
            <span id="pts-price-btc"></span><br/><span id="pts-price-cur"></span>
        </p>
    </div>
    <div class="equal-spaced">
        <p class="ticker">Total BTC / Total PTS:</p>
        <p class="ticker-num">
            <span id="btc-pts-ratio"></span>
        </p>
    </div>
    <span class="stretch"></span>
    <p id="totalv">Today's Total Value: <span id="total-value" style="font-weight:bold;"></span></p>

</div>


<div>
<span class="title">Daily Stats:</span>
</div>

<div id="pts-chart" class="full-chart"></div>
<div id="btc-chart" class="full-chart"></div>
<div id="value-chart" class="full-chart"></div>

</div>

<div id="footer">
</div>

</div>

<script>
timer = setInterval(showRemaining, 1000);

if (getCookie('currency')) {
    var cur = getCookie('currency')
    $("#curselect").val(cur);
} else {
    var cur = 'USD'
}



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

function getPricesHistory(){
    var ptsHistory = (function() {
        var json = null;
            $.ajax({
                'async': false,
                'global': false,
                'url': "curjson/pts_history.json",
                'dataType': "json",
                'success': function (data) {
                    json = data;
                }
            });
            return json;
        })();


    var btcHistory = (function() {
        var json = null;
        $.ajax({
            'async': false,
            'global': false,
            'url': "curjson/btc_history.json",
            'dataType': "json",
            'success': function (data) {
                json = data;
            }
        });
        return json;
    })();
    return {pts_history:ptsHistory,btc_history:btcHistory};
};

function getPricesNow(){
    var ptsNow = (function() {
        var json = null;
        $.ajax({
            'async': false,
            'global': false,
            'url': "curjson/pts_now.json",
            'dataType': "json",
            'success': function (data) {
                json = data;
            }
        });
        return json;
    })();


    var btcNow = (function() {
        var json = null;
        $.ajax({
            'async': false,
            'global': false,
            'url': "curjson/btc_now.json",
            'dataType': "json",
            'success': function (data) {
                json = data;
            }
        });
        return json;
    })();
    return {pts_now:ptsNow,btc_now:btcNow};
}
/*
function prepareDayGraph(txs){
    var UTCoffset = new Date().getTimezoneOffset();
    var graph_list = [];
    var amount = 0
    var hour = 0
    var hour_total = 0

    console.log(txs[0][4] % 86400000)
    console.log(Math.floor(((txs[0][4] % 86400000)/86400000)*24))

    console.log()

    for (i=0; i<txs.length; i++){
        amount = txs[i][1];
        calc_hour = Math.floor(((txs[i][4] % 86400000)/86400000)*24);
        if (hour == calc_hour){
            hour_total += amount;
        } else {
            timestamp = (txs[i][4] % 86400000) + 86400000 + UTCoffset;
            graph_list.push([(timestamp),amount]);
            hour_total = amount;
            hour = calc_hour;
        };
        if (i == txs.length-1){
            hour_total += amount;
            timestamp = (txs[i][4] % 86400000) + 86400000 + UTCoffset;
            graph_list.push([(timestamp),amount]);
            break
        }
    };

    if (graph_list[0] != 0){
        [86400000+UTCoffset,0].concat(graph_list);
    };

    if (graph_list[graph_list.length-1] != 23){
        graph_list.push([169200000+UTCoffset,0]);
    };

    //console.log(graph_list)

    return graph_list
}
*/
function prepareDayGraphSum(txs){
    var UTCoffset = (new Date().getTimezoneOffset())*60000;
    var now = new Date().getTime() + UTCoffset;
    var graph_list = [];
    var hour = 0
    sum = 0
    for (i=0; i<txs.length; i++){
        var amount = parseFloat(txs[i][1]);
        var time = parseInt(txs[i][4]) + UTCoffset;
        sum += amount;
        graph_list.push([(time),sum])
    };

    //[[((txs[0][1]+UTCoffset)%86400000),0]].concat(graph_list);

    graph_list.push([now,sum]);

    return graph_list
}

function getHistory(){
    var ptsHistory = (function() {
        var json = null;
        $.ajax({
            'async': false,
            'global': false,
            'url': "curjson/pts_history.json",
            'dataType': "json",
            'success': function (data) {
                json = data;
            }
        });
        return json;
    })();

    var btcHistory = (function() {
        var json = null;
        $.ajax({
            'async': false,
            'global': false,
            'url': "beta/curjson/btc_history.json",
            'dataType': "json",
            'success': function (data) {
                json = data[cur];
            }
        });
        return json;
    })();
    return [ptsHistory,btcHistory]
}

function mergeValueBTC(history,amount,current){
    var value_data = []
    var value;
    var time;
    for (i=0; i<amount.length; i++){
        for (j=0; j<history.length; j++){
            if (history[j][0] == amount[i][0]){
                value = history[j][1]*amount[i][1];
                time = history[j][0];
                value_data.push([time,value]);
            }
        }
    };
    for (i=value_data.length ; i<amount.length-1; i++){
        value = current * amount[i][1];
        time = amount[i][0];
        value_data.push([time,value]);
    };
    return value_data
}

function mergeValuePTS(btcHistory,ptsHistory,amount,btc_now,pts_now){
    var value_data = [];
    var value;
    var time;
    var history = [];
    for (i=0; i<btcHistory.length; i++){
        for (j=0; j<ptsHistory.length; j++){
            if (btcHistory[i][0] == ptsHistory[j][0]){
                value = btcHistory[i][1]*ptsHistory[j][1];
                time = ptsHistory[j][0];
                history.push([time,value]);
            }
        }
    };
    for (i=0; i<amount.length; i++){
        for (j=0; j<history.length; j++){
            if (history[j][0] == amount[i][0]){
                value = history[j][1]*amount[i][1];
                time = history[j][0];
                value_data.push([time,value]);
            }
        }
    };
    var current = []
    for (i=history.length; i<ptsHistory.length-1; i++){
        value = ptsHistory[i][1]*btc_now
        time = ptsHistory[i][0]
        console.log(value)
        console.log(time)
        current.push([time,value])
    };

    for (i=current.length+history.length; i<amount.length-1; i++){
        value = pts_now*btc_now
        time = amount[i][0]
        console.log(value)
        console.log(time)
        current.push([time,value])
    };

    for (i=value_data.length ; i<amount.length-1; i++){
        for (j=0 ; j<current.length-1; j++){
            value = current[j][1] * amount[i][1];
            time = amount[i][0];
            value_data.push([time,value]);
        }
    };
    return value_data
}

function update(){
    var tickerJSON = (function() {
            var json = null;
            $.ajax({
                'async': false,
                'global': false,
                'url': "beta/json/index.php?coinsAmount",
                'dataType': "json",
                'success': function (data) {
                    json = data;
                }
            });
            return json;
        })();

    var btc_i = tickerJSON.BTC.length - 1;
    var pts_i = tickerJSON.PTS.length - 1;

    var pts_today = roundTo3(tickerJSON.PTS[pts_i][1]);
    var btc_today = roundTo3(tickerJSON.BTC[btc_i][1]);
    var pts_yesterday = roundTo3(tickerJSON.PTS[pts_i-1][1]);
    var btc_yesterday = roundTo3(tickerJSON.BTC[btc_i-1][1]);

    var pts_ags_today = roundTo3(5000/tickerJSON.PTS[pts_i][1]);
    var pts_ags_yesterday = roundTo3(5000/tickerJSON.PTS[pts_i-1][1]);
    var btc_ags_today = roundTo3(5000/tickerJSON.BTC[btc_i][1]);
    var btc_ags_yesterday = roundTo3(5000/tickerJSON.BTC[btc_i-1][1]);

    $('#pts-today').text(nC(pts_today) + " PTS");
    $('#pts-yesterday').text("Yesterday: " + nC(pts_yesterday) + " PTS");
    $('#pts-ags-today').text(nC(pts_ags_today) + " AGS/PTS");
    $('#pts-ags-yesterday').text("Yesterday: " + nC(pts_ags_yesterday) + " AGS/PTS");
    $('#btc-today').text(nC(btc_today) + " BTC");
    $('#btc-yesterday').text("Yesterday: " + nC(btc_yesterday) + " BTC");
    $('#btc-ags-today').text(nC(btc_ags_today) + " AGS/BTC");
    $('#btc-ags-yesterday').text("Yesterday: " + nC(btc_ags_yesterday) + " AGS/BTC");

    var pricesnow = getPricesNow()
    var pts_now = pricesnow.pts_now.PTS
    var btc_now = pricesnow.btc_now[cur]

    $('#btc-price-cur').text(nC(roundTo3(btc_now)) + " " + cur);
    $('#btc-price-pts').text(nC(roundTo3(1/pts_now)) + " PTS");
    $('#pts-price-cur').text(nC(roundTo3(pts_now*btc_now)) + " " + cur);
    $('#pts-price-btc').text(roundTo(pts_now,4) + " BTC");
    $('#btc-pts-ratio').text(roundTo(tickerJSON.BTC[btc_i][1]/tickerJSON.PTS[pts_i][1],4));

    var pts_today_cur = roundTo(pts_today*pts_now*btc_now,2);
    var pts_yesterday_cur = roundTo(pts_yesterday*pts_now*btc_now,2); // Need to change to historical price
    var pts_ags_today_cur = roundTo(pts_ags_today/(pts_now*btc_now),2);
    var pts_ags_yesterday_cur = roundTo(pts_ags_yesterday/(pts_now*btc_now),2);

    var btc_today_cur = roundTo(btc_today*btc_now,2);
    var btc_yesterday_cur = roundTo(btc_yesterday*btc_now,2);
    var btc_ags_today_cur = roundTo(btc_ags_today/btc_now,2);
    var btc_ags_yesterday_cur = roundTo(btc_ags_yesterday/btc_now,2);

    $('#total-value').text(nC(roundTo(btc_today*btc_now + pts_today*pts_now,2)) + " " + cur);

    $('#pts-today-cur').text(nC(pts_today_cur) + " " + cur);    
    $('#pts-yesterday-cur').text(nC(pts_yesterday_cur) + " " + cur);
    $('#pts-ags-today-cur').text(nC(pts_ags_today_cur) + " AGS/" + cur);
    $('#pts-ags-yesterday-cur').text(nC(pts_ags_yesterday_cur) + " AGS/" + cur);
    $('#btc-today-cur').text(nC(btc_today_cur) + " " + cur);
    $('#btc-yesterday-cur').text(nC(btc_yesterday_cur) + " " + cur);
    $('#btc-ags-today-cur').text(nC(btc_ags_today_cur) + " AGS/" + cur);
    $('#btc-ags-yesterday-cur').text(nC(btc_ags_yesterday_cur) + " AGS/" + cur);

    var pts_ags_ptoday = roundTo3(1/(5000/tickerJSON.PTS[pts_i][1]));
    var pts_ags_pyesterday = roundTo3(1/(5000/tickerJSON.PTS[pts_i-1][1]));
    var btc_ags_ptoday = roundTo3(1/(5000/tickerJSON.BTC[btc_i][1]));
    var btc_ags_pyesterday = roundTo3(1/(5000/tickerJSON.BTC[btc_i-1][1]));
    var pts_ags_ptoday_cur = roundTo(1/(pts_ags_today/(pts_now*btc_now)),2);
    var pts_ags_pyesterday_cur = roundTo(1/(pts_ags_yesterday/(pts_now*btc_now)),2);
    var btc_ags_ptoday_cur = roundTo(1/(btc_ags_today/btc_now),2);
    var btc_ags_pyesterday_cur = roundTo(1/(btc_ags_yesterday/btc_now),2);

    $('#pts-ags-ptoday').text(nC(pts_ags_ptoday) + " PTS/AGS");
    $('#pts-ags-pyesterday').text("Yesterday: " + nC(pts_ags_pyesterday) + " PTS/AGS");
    $('#pts-ags-ptoday-cur').text(nC(pts_ags_ptoday_cur) + " " + cur + "/AGS");
    $('#pts-ags-pyesterday-cur').text(nC(pts_ags_pyesterday_cur) + " " + cur + "/AGS");
    $('#btc-ags-ptoday').text(nC(btc_ags_ptoday) + " BTC/AGS");
    $('#btc-ags-pyesterday').text("Yesterday: " + nC(btc_ags_pyesterday) + " BTC/AGS");
    $('#btc-ags-ptoday-cur').text(nC(btc_ags_ptoday_cur) + " " + cur + "/AGS");
    $('#btc-ags-pyesterday-cur').text(nC(btc_ags_pyesterday_cur) + " " + cur + "/AGS");

    var today_pts = tickerJSON.BTC[tickerJSON.BTC.length-1][0]
    var today_btc = tickerJSON.PTS[tickerJSON.BTC.length-1][0]

    if (today_pts == today_btc) {

        var txsToday = (function() {
            var json = null;
            $.ajax({
                'async': false,
                'global': false,
                'url': "beta/json/index.php?dayTransactions=" + today_pts,
                'dataType': "json",
                'success': function (data) {
                    json = data;
                }
            });
            return json;
        })();
    
        var today_txs_pts = txsToday.PTS
        var today_txs_btc = txsToday.BTC

    } else {
        var today_txs_pts = (function() {
            var json = null;
            $.ajax({
                'async': false,
                'global': false,
                'url': "beta/json/index.php?dayTransactions=" + today_pts,
                'dataType': "json",
                'success': function (data) {
                    json = data.PTS;
                }
            });
            return json;
        })();

        var today_txs_btc = (function() {
            var json = null;
            $.ajax({
                'async': false,
                'global': false,
                'url': "beta/json/index.php?dayTransactions=" + today_btc,
                'dataType': "json",
                'success': function (data) {
                    json = data.BTC;
                }
            });
            return json;
        })();
    }

    var pts_todaysum_data = prepareDayGraphSum(today_txs_pts[1]);
    var btc_todaysum_data = prepareDayGraphSum(today_txs_btc[1]);



    todayGraphs(pts_todaysum_data,btc_todaysum_data);
}

function todayGraphs(pts_todaysum_data,btc_todaysum_data){
    $('#pts-today-chart').empty();
    var pts_today_plot = $.jqplot('pts-today-chart', [pts_todaysum_data], {
        title:'ProtoShares Today',
        seriesColors:['rgba(75, 178, 197, 1)'],
        seriesDefaults:{
            showMarker: false
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
                tickOptions:{formatString:'%H:%M'},
                //tickInterval:'4 hours',
            },
            yaxis:{
                min: 0,
                pad: 1.05
            }
        }
    });

    $('#btc-today-chart').empty();
    var pts_today_plot = $.jqplot('btc-today-chart', [btc_todaysum_data], {
        title:'Bitcoin Today',
        seriesColors:['rgba(234, 162, 40, 1)'],
        seriesDefaults:{
            showMarker: false
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
                tickOptions:{formatString:'%H:%M'},
                //tickInterval:'1 hour'
            },
            yaxis:{
                min: 0,
                pad: 1.05
            }
        }
    });   
}

update();

window.setInterval(function(){
  update()
}, 10000);

function updateGraphs(){
    $('#pts-chart').empty();
    $('#btc-chart').empty();

    var JSONdata = (function() {
        var json = null;
        $.ajax({
            'async': false,
            'global': false,
            'url': "beta/json/index.php?coinsAmount",
            'dataType': "json",
            'success': function (data) {
                json = data;
            }
        });
        return json;
    })();

    function timezoneFix(data){
        var UTCoffset = new Date().getTimezoneOffset();
            for (var i=0;i<data.length;i++)
                { 
                    data[i] = [data[i][0]+(UTCoffset*60000), data[i][1]];
                };
        return data;
    };

    function addPadding(data){
        var newyear = 1388448000000;
        var now = new Date().getTime();
        var tomorrow = now-(now%86400000)+86400000;
        data.push([tomorrow,-1]);
        ret = [[newyear,0]].concat(data);
        return ret;
    };

    var pts_raw = addPadding(JSONdata.PTS);
    var btc_raw = addPadding(JSONdata.BTC);

    var pts_data = timezoneFix(pts_raw);
    var btc_data = timezoneFix(btc_raw);

    var pts_barwidth = 640/((pts_data[pts_data.length-1][0]-pts_data[0][0])/86400000)-2;
    var btc_barwidth = 640/((btc_data[btc_data.length-1][0]-btc_data[0][0])/86400000)-1;



    var pts_plot = $.jqplot('pts-chart', [pts_data], {
        title:'ProtoShares Per Day',
        seriesColors:['rgba(75, 178, 197, 1)'],
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {  
                highlightMouseOver: true,
                barWidth :pts_barwidth
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

    var btc_plot = $.jqplot('btc-chart', [btc_data], {
        title:'Bitcoin Per Day',
        seriesColors:['rgba(234, 162, 40, 1)'],
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {  
                highlightMouseOver: true,
                barWidth :btc_barwidth
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


    var history = getHistory();
    var pricesnow = getPricesNow();

    var ptsHistory = history[0]
    var btcHistory = history[1]

    var btc_value = mergeValueBTC(btcHistory,JSONdata.BTC,pricesnow.btc_now[cur]);
    var pts_value = mergeValuePTS(btcHistory,ptsHistory,JSONdata.PTS,pricesnow.btc_now[cur],pricesnow.pts_now.PTS);
                        
    var btc_value_data = timezoneFix(addPadding(btc_value)); 
    var pts_value_data = timezoneFix(addPadding(pts_value));

    console.log(btc_value_data);
    console.log(pts_value_data);


    $('#value-chart').empty();
    var btc_plot = $.jqplot('value-chart', [pts_value_data,btc_value_data], {
        title:'Daily Total Value (in ' + cur + ')',
        seriesColors:['rgba(75, 178, 197, 1)','rgba(234, 162, 40, 1)',],
        stackSeries: true,
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {  
                highlightMouseOver: true,
                barWidth :btc_barwidth
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



    $('#pts-chart .jqplot-xaxis-tick:last-child,#btc-chart .jqplot-xaxis-tick:last-child').empty();
    $('#pts-chart .jqplot-xaxis-tick:first-child,#btc-chart .jqplot-xaxis-tick:first-child').empty();

};


updateGraphs();


window.setInterval(function(){
    updateGraphs();
}, 60000);

$('#curselect').change(function() {
      cur = $(this).val()
      update();
      updateGraphs();
      createCookie('currency',cur,7000);
});

</script>
</body>
</html>