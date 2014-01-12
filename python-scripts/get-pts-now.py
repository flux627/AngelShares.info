
import urllib
import json

crytpsy_json = json.load(urllib.urlopen('http://pubapi.cryptsy.com/api.php?method=singlemarketdata&marketid=119'))

pts_last = crytpsy_json['return']['markets']['PTS']['recenttrades'][0]['price']

pts = json.dumps({'PTS': pts_last})

with open('/var/www/curjson/pts_now.json', 'w') as f:
    f.write(pts)