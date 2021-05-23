# 脚本文件目录


#1：checkPay.sh 是检测是否支付 使用 pm2 start checkPay.sh 守护服务

#2：notify.sh 异步通知商户 使用 pm2 start notify.sh 守护服务

#3 订单过期如下

~~~
订单过期 crontab

*/1 * * * * /www/server/php/71/bin/php /home/wwwroot/usdt.happy88.top/public/index.php  Shell_Orders/expire >>/home/wwwlogs/usdt_orders_expire.log

~~~


#4 更新码的账户余额
~~~
更新码的账户余额 crontab

*/15 * * * * /www/server/php/71/bin/php /home/wwwroot/usdt.happy88.top/public/index.php  Shell_Qr/money >>/home/wwwlogs/usdt_qr_money.log

~~~

