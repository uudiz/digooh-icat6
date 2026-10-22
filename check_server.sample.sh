#!/bin/sh
count=`ps -ef |grep "icat6serverphp85.php" | grep -v "grep" | wc -l`
echo $count
if [ $count -lt 1 ]; then
ps -eaf |grep "icat6serverphp85.php" | grep -v "grep"| awk '{print $2}'|xargs kill -9
sleep 2
ulimit -c unlimited 
/usr/bin/php -f /home/icat/public_html/swoole/icat6serverphp85.php
echo "restart";
fi