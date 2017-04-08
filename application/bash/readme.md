1. 通过supervisor来守护队列进程，来保持消费任务
2. 通过crontab 定时触发队列任务，用于产生任务
*/30 * * * * ntpdate 10.143.33.50

*/1 * * * * /usr/bin/curl  http://devsunsun.8raw.com/index.php/filter_vat_task/index?from=crontab

0 0 23 * *  /usr/bin/curl  http://devsunsun.8raw.com/index.php/filter_vat_task/clear_db?from=crontab

3. 启动队列服务端
  调用start_queue.sh