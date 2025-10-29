#!/bin/bash
#数据库IP
dbserver='db-lb'
#数据库用户名
dbuser='icat'
#数据密码
dbpasswd='deleted'
#数据库,如有多个库用空格分开
dbname='icat'
#备份时间
backtime=`date +%Y%m%d`
#备份输出日志路径
logpath='/home/icat/dbbackup/'


echo "################## ${backtime} #############################" 
echo "start to backup" 
#日志记录头部
echo "" >> ${logpath}/mysqlback.log
echo "-------------------------------------------------" >> ${logpath}/mysqlback.log
echo "备份时间为${backtime},备份数据库表 ${dbname} 开始" >> ${logpath}/mysqlback.log
#正式备份数据库
for table in $dbname; do
source=`mysqldump -h ${dbserver} -u ${dbuser} -p${dbpasswd} ${table} > ${logpath}/${backtime}.sql` 2>> ${logpath}/mysqlback.log;
#备份成功以下操作
if [ "$?" == 0 ];then
cd $datapath
#为节约硬盘空间，将数据库压缩
tar zcf ${table}${backtime}.tar.gz ${backtime}.sql > /dev/null
#删除原始文件，只留压缩后文件
rm -f ${datapath}/${backtime}.sql
#删除七天前备份，也就是只保存7天内的备份
find $datapath -name "*.tar.gz" -type f -mtime +7 -exec rm -rf {} \; > /dev/null 2>&1
echo "success in backuping table ${dbname} !!" >> ${logpath}/mysqlback.log
else
#备份失败则进行以下操作
echo "failed to backup table ${dbname}!!" >> ${logpath}/mysqlback.log
fi
done
echo "finish to backup"
echo "################## ${backtime} #############################"
