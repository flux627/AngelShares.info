#!/bin/sh
while [ true ] 
do	
	cd /home/ubuntu/florincoin_block_explorer_bitcoin ; php blockchain_cron.php -a rescan -b 3 --debug;
	cd /home/ubuntu/php-scripts/ ; php BTC_cron.php;
	sleep 40
done
