#!/bin/sh
awk '{if($7 ~/receive/) print substr($7,index($7,"sn=")+3,10),$11,$12}' /var/log/httpd/`date -d "-1 day" +%Y%m%d`_access_log > /home/miatek/bandwidth/cat_access_log.txt;
awk '{print $1,$2,$3}' /var/log/swoole/`date -d "-1 day" +%Y%m%d`_swoole_log > /home/miatek/bandwidth/swoole_log.txt;
cat /home/miatek/bandwidth/swoole_log.txt >> /home/miatek/bandwidth/cat_access_log.txt;
/usr/bin/mysqlimport -uroot -ptc44584 -d tochenca_np200 --fields-terminated-by=" " /home/miatek/bandwidth/cat_access_log.txt;
rm -f /home/miatek/bandwidth/cat_access_log.txt;
rm -f /home/miatek/bandwidth/swoole_log.txt;
/usr/bin/mysql -uroot -ptc44584 tochenca_np200 -e "DELETE FROM cat_player_bandwidth WHERE recode_date = `date -d "-1 day" +%Y%m%d`;INSERT INTO cat_player_bandwidth (player_id, recode_date, used_bandwidth)SELECT p.id,`date -d "-1 day" +%Y%m%d`, sum( a.input + a.output ) AS total FROM cat_player AS p, cat_access_log AS a WHERE p.sn = a.sn GROUP BY p.id";