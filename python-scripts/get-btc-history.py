import urllib
import json
import csv
import re
import time
import datetime

def get_csv(cur_code, url):
    download = urllib.urlopen(url)
    csvlist = list(csv.reader(download))
    return csvlist

def get_prices(csvlist):
    price_list = []
    for i in csvlist[1:]:
        datesplit = '/'.join(re.findall("[\d]+",i[0])[:-3])
        epoch = int(time.mktime(datetime.datetime.strptime(datesplit, "%Y/%m/%d").timetuple()))
        if epoch < 1388534400:
            continue
        price = float(i[3])
        price_list.append([epoch*1000, price])
    return price_list

def get_all():
    historical_prices = {}

    cur_codes = ["AUD", "BRL", "CAD", "CHF",
                 "CNY", "CZK", "EUR", "GBP",
                 "ILS", "JPY", "NOK", "NZD",
                 "PLN", "RUB", "SEK", "SGD",
                 "USD", "ZAR"]

    for cur_code in cur_codes:
        try:
            csvlist = get_csv(cur_code, "https://api.bitcoinaverage.com/history/{0}/per_day_all_time_history.csv".format(cur_code))
            price_list = get_prices(csvlist)
            historical_prices[cur_code] = price_list
        except:
            pass
    return historical_prices

data = get_all()
currency_json = json.dumps(data)

with open('/var/www/curjson/btc_history.json', 'w') as f:
    f.write(currency_json)