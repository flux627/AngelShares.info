#!/bin/sh
while [ true ]
do
	cd /home/ubuntu/florincoin_block_explorer ; php blockchain_cron.php -a rescan -b 12 --debug;
	cd /home/ubuntu/php-scripts/ ; php PTS_cron.php;
	sleep 30
done
