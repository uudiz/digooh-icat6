#!/bin/sh
ps -eaf |grep "icat6testserver.php" | grep -v "grep"| awk '{print $2}'|xargs kill -9
sleep 2
ulimit -c unlimited 
/usr/bin/php -f /home/icat/testserver/public_html/swoole/icat6testserver.php
echo "restart";
echo $(date +%Y-%m-%d_%H:%M:%S) >/home/icat/testserver/public_html/logs/restart.log;