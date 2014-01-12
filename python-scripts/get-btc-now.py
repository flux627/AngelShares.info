
import urllib
import json

btc_json = json.load(urllib.urlopen('https://api.bitcoinaverage.com/ticker/all'))

btc_dict = {}

for i in btc_json:
    if i != 'timestamp':
        btc_dict[i] = btc_json[i]['last']

btc_json = json.dumps(btc_dict)

with open('/var/www/curjson/btc_now.json', 'w') as f:
    f.write(btc_json)