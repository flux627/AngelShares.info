import urllib
import json
import csv
import re
import time
import datetime

download = urllib.urlopen('https://www.cryptsy.com/json/ajaxtradechart_119.json?callback=chart')
raw_json = json.load(download)
historical_prices = []

today = 1388534400000 # New Years day
today_trades = 0
today_volume = 0
today_average = 0

for index, i in enumerate(raw_json):

    if (i[0] - (i[0] % 86400000)) > today:
        if today_volume != 0:
            today_average = today_trades / today_volume
            historical_prices.append([today, today_average])
#        print "day complete: " + str(datetime.datetime.fromtimestamp(today/1000))
#        print "average price: " + str(today_average)
#        print
        today = i[0] - (i[0] % 86400000)
        today_trades = 0
        today_volume = 0

    if index == len(raw_json)-1:
        if today_volume == 0:
            historical_prices.append([today,historical_prices[len(historical_prices)-1][1]])
        today_average = today_trades / today_volume
        historical_prices.append([today, today_average])
#        print "current day cut short: " + str(datetime.datetime.fromtimestamp(today/1000))
#        print "average price: " + str(today_average)
#        print
        break

    if i[0] >= today:
        today_trades += i[1]*i[5]
        today_volume += i[5]

currency_json = json.dumps(historical_prices)

with open('/var/www/curjson/pts_history.json', 'w') as f:
    f.write(currency_json)