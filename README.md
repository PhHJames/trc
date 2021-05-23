# 项目依赖
# php 版本7.1
# 软件如下
```
1：yaf  3.0.7
2：redis 扩展 
3：mysql 5.7 

服务器需要安装 beanstalkd 队列服务 下载地址：

https://github.com/beanstalkd/beanstalkd/archive/v1.11.tar.gz

redis 下载地址：
http://download.redis.io/releases/redis-5.0.5.tar.gz

nodejs 下载地址：

https://npm.taobao.org/mirrors/node/v10.15.3/node-v10.15.3-linux-x64.tar.gz
  
```

# beanstalkd 安装
~~~

tar xvf v1.11.tar.gz
cd beanstalkd-1.11
make && make install
//创建beanstald的数据库目录 存放数据库文件的作用
mkdir -p /usr/local/beanstalkd/data

touch  /usr/local/beanstalkd/start.sh

注意下面的绑定IP地址， 请根据实际情况进行修改

vim /usr/local/beanstalkd/start.sh
chmod +x /usr/local/beanstalkd/start.sh

#!/bin/sh
/usr/local/bin/beanstalkd -b /usr/local/beanstalkd/data -l 127.0.0.1 -p 11300  &
~~~
# php相关的文件修改

```
 1：打开 conf/application.ini 文件 修改响应的配置
 2：打开 account.php 文件修改成自己的系统内置的币地址
 3：php的一些脚本是放在 crontab.sample 这个目录里面 不要忘记修改相关的参数 进行启动
```
# nodejs 安装
~~~

tar xvf node-v10.15.3-linux-x64.tar.gz
cd node-v10.15.3-linux-x64
mkdir /usr/local/nodejs
cp -r * /usr/local/nodejs
/*把nodejs导入到环境变量*/
echo "export PATH=$PATH:/usr/local/nodejs/bin" >>/etc/profile
/*立马生效*/
source /etc/profile
/*安装pm2*/
npm install pm2 -g

~~~
# yaf 安装
~~~
去官网下载yaf 3.0.7 然后编译安装 并且加入到php.ini里面 不会安装这个的请百度吧
~~~

# redis服务端安装

~~~

tar xvf redis-5.0.5.tar.gz
cd redis-5.0.5
make PREFIX=/usr/local/redis  install
mkdir -p /usr/local/redis/etc
mkdir -p /usr/local/redis/logs
mkdir -p /usr/local/redis/data/6379
cat redis.conf > /usr/local/redis/etc/6379.conf
修改配置文件里面的 daemon 为 yes 其他配置项根据需要修改
然后启动：
/usr/local/redis/bin/redis-server /usr/local/redis/etc/6379.conf
~~~
#nodejs服务

~~~
1:进入server目录
2：执行 ： npm install 
3:修改配置文件 config/config.js
4: pm2 start startServer.json 
5: pm2 start block_trx.js
6: pm2 start block_usdt.js
~~~


# 如何生成一个本地的不上链的地址
~~~
1:进入server目录
2：执行 ：node address.js 即可
~~~

# nginx配置参考
~~~

server {
		listen       80;
		server_name    usdt.pay.com ;
		charset utf-8;
		root  "D:\php_work\USDT\public";

		location / {
			if (!-e $request_filename) {
				rewrite ^/(.*)  /index.php/$1 last;
			}
			index  index.php;
			autoindex  off;
		}
		
		
		location  ~ .+\.php($|/)   {

			fastcgi_index index.php;
			fastcgi_pass 127.0.0.1:9000;

			include      fastcgi_params;
			set $path_info "";
			set $real_script_name $fastcgi_script_name;
			if ($fastcgi_script_name ~ "^(.+?\.php)(/.+)$") {
				set $real_script_name $1;
				set $path_info $2;
			}
			fastcgi_param SCRIPT_FILENAME $document_root$real_script_name;
			fastcgi_param SCRIPT_NAME $real_script_name;
			fastcgi_param PATH_INFO $path_info;
		}

		location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
		{
			expires 1h;
		}
		location ~ .*\.(js|css)?$
		{
			expires 1h;
		}
}


~~~


~~~

知识点普及
1：只要拥有币地址的私钥那么就可以把这个币地址里面的钱转出
2：币地址可以理解为收款码比如微信支付宝的收款码
3：系统创建的币地址，那么私钥在数据库已经保存了
4：一个订单对应一个币地址也就是一个收款码防止串单。
5：比如系统有付款成功订单1000笔 ， 那么1000个订单对应的币地址里面都有钱如何把这1000个币地址对应的钱取出来呢？
    a。要先把这1000个币地址对应的币先归集到一个账户里面。
    b。然后在从归集账户里面把钱转到火币或者币安那边进行卖
6：每次归集需要消耗3-5个TRX，所以要保证待归集账号里面有TRX
7：每个TRX大概几分钱。

~~~