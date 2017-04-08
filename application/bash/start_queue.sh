#!/bin/sh
ps -ef | grep queue:work | grep -v grep | cut -c 9-15 | xargs kill -9
basePath=$(cd `dirname $0`; pwd);
phpfile=${basePath}"/../../think"
#echo ${phpfile}
php ${phpfile}  queue:work --queue itboye_timing_task --daemon |
php ${phpfile}  queue:work --queue itboye_email_task --daemon
