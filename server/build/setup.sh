#!/bin/bash
# centos 7 下安装 dance 服务器

# 脚本未完善，请暂时勿完全按照脚本执行
# 【请先修正脚本中所有标？？？？？？？？？的地方！】

# # 网络自动配置 (DHCP)，当yum、rpm出现问题时可运行该命令尝试修正
# dhclient

# # 必备包
# yum -y install bzip2 # 安装bzip2解压软件
# yum -y install expat-devel
# yum install -y gcc gcc-c++
# yum -y install openssl openssl-devel # 安装openssl
# yum -y install libxml2 libxml2-devel curl-devel libpng-devel freetype-devel libmcrypt-devel libjpeg-devel # 编译php需要的包
# yum -y install autoconf # phpize时需要
# yum -y install wget # 网络下载命令
# yum -y install sed # 批处理文件编辑命令
# yum -y install libtool # 用于安装php

# # ----------------------------------------------------------------------------------------
# # apache服务器
# ## 安装apache
# # 1. 获取最新版安装包：
# mkdir -p /data/release/xjtudance-data/softwares
# wget -P /data/release/xjtudance-data/softwares http://mirrors.tuna.tsinghua.edu.cn/apache//httpd/httpd-2.4.28.tar.bz2
# wget -P /data/release/xjtudance-data/softwares http://mirror.bit.edu.cn/apache//apr/apr-1.6.2.tar.bz2
# wget -P /data/release/xjtudance-data/softwares http://mirror.bit.edu.cn/apache//apr/apr-util-1.6.0.tar.bz2
# #wget -P /data/release/xjtudance-data/softwares ftp://ftp.csx.cam.ac.uk/pub/software/programming/pcre/pcre2-10.30.tar.bz2
# wget -P /data/release/xjtudance-data/softwares ftp://ftp.csx.cam.ac.uk/pub/software/programming/pcre/pcre-8.41.tar.bz2

# # 2. 解压并安装
  cd /data/release/xjtudance-data/softwares
# # tar -jxvf httpd-2.4.28.tar.bz2 # 解压

# # # 安装APR和APR-Util
# mkdir -p httpd-2.4.28/srclib/
# # 解压
# tar -jxvf apr-1.6.2.tar.bz2 -C httpd-2.4.28/srclib/
# rename apr-1.6.2 apr httpd-2.4.28/srclib/apr-1.6.2
# tar -jxvf apr-util-1.6.0.tar.bz2 -C httpd-2.4.28/srclib/
# rename apr-util-1.6.0 apr-util httpd-2.4.28/srclib/apr-util-1.6.0
# #tar -jxvf pcre2-10.30.tar.bz2
# tar -jxvf pcre-8.41.tar.bz2
# # 安装apr
# cd httpd-2.4.28/srclib/apr
# ./configure --prefix=/usr/local/apr
# make
# make test
# make install
# # 安装APR-Util
# cd ../apr-util
# ./configure --prefix=/usr/local/apr-util --with-apr=/usr/local/apr/
# make
# make test
# make install
# # 安装pcre#pcre2
# #cd ../../../pcre2-10.30
# #./configure --prefix=/usr/local/pcre2
# cd ../../../pcre-8.41
# ./configure --prefix=/usr/local/pcre
# make
# make check
# make install

# # 安装apache
# cd ../httpd-2.4.28
# #./configure --prefix=/usr/local/apache --enable-mods-shared=all --with-apr=/usr/local/apr/ --with-apr-util=/usr/local/apr-util/ --with-pcre=/usr/local/pcre2/bin/pcre2-config # --prefix指定安装路径，--enable-mods-shared启用所有支持的动态加载模块，--with-apr指定APR路径，--with-apr-util指定APR-util路径
# ./configure --prefix=/usr/local/apache --enable-mods-shared=all --with-apr=/usr/local/apr/ --with-apr-util=/usr/local/apr-util/ --with-pcre=/usr/local/pcre --enable-ssl --enable-so # --prefix指定安装路径，--enable-mods-shared启用所有支持的动态加载模块，--with-apr指定APR路径，--with-apr-util指定APR-util路径
# make
# make test
# make install

## 配置apache

# # 1. 注册apache为系统服务
# cp /usr/local/apache/bin/apachectl /etc/init.d/httpd # 把apache启动脚本复制到系统脚本目录下

# # 2. 配置Apache开机启动
# # 在#!/bin/sh下面添加两句：
# mkdir -p /data/release/xjtudance-data/old-files/etc/rc.d/init.d/
# cp -p /etc/rc.d/init.d/httpd /data/release/xjtudance-data/old-files/etc/rc.d/init.d/httpd
# sed '1a #\n#chkconfig: 2345 10 90\n#description: Activates/Deactivates Apache Web Server' /etc/rc.d/init.d/httpd > /etc/rc.d/init.d/httpd.tmp
# mv /etc/rc.d/init.d/httpd.tmp /etc/rc.d/init.d/httpd

# # 3. 启动apache：？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？此处的yourip似乎得改
# mkdir -p /data/release/xjtudance-data/old-files/usr/local/apache/conf/
# cp -p /usr/local/apache/conf/httpd.conf /data/release/xjtudance-data/old-files/usr/local/apache/conf/httpd.conf
# sed 's/#ServerName www.example.com:80/ServerName yourip:80/1' /usr/local/apache/conf/httpd.conf > /usr/local/apache/conf/httpd.conf.tmp
# mv /usr/local/apache/conf/httpd.conf.tmp /usr/local/apache/conf/httpd.conf

# service httpd start
# ps -ef|grep httpd

# # 4. 查看apache版本：
# cp /usr/local/apache/bin/httpd /usr/sbin/httpd
# httpd -v

# ### apache测试
# # 在浏览器中访问：http://公网IP/，能够出现“It works!”页面就代表apache配置成功。
# # 如果未成功，请检查防火墙。

# ### 修改apache默认网站解析目录
# # 1. 创建目录：
# mkdir -p /data/release/dance
# # 2. 修改apache配置文件/usr/local/apache/conf/httpd.conf：
# sed -e 's/DocumentRoot "\/usr\/local\/apache\/htdocs"/DocumentRoot "\/data\/release\/dance"/1' -e 's/<Directory "\/usr\/local\/apache\/htdocs">/<Directory "\/data\/release\/dance">/1' /usr/local/apache/conf/httpd.conf > /usr/local/apache/conf/httpd.conf.tmp
# mv /usr/local/apache/conf/httpd.conf.tmp /usr/local/apache/conf/httpd.conf
# service httpd restart -v

# # 4. 编写index.html如下并放在/data/release/dance文件夹下：？？？？？？？？？？？？？？？？？？？此处中文字符编码有问题，不是unicode
# echo '<!DOCTYPE html>
# <html>
# <head>
# <meta charset=\"UTF-8\">
# <title>欢迎来到dance！</title>
# </head>
# <body>
# <h1>Bienvenue à dance!</h1>
# </body>
# </html>' > /data/release/dance/index.html

# ### Apache服务器绑定域名
# # 1. 确认占用端口的是httpd
# netstat -anp |grep :80
# # 2. 修改apache配置文件：？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？此处的yourip和yourdomain得改
# sed 's/ServerName yourip:80/ServerName yourdomain:80/1' /usr/local/apache/conf/httpd.conf > /usr/local/apache/conf/httpd.conf.tmp
# mv /usr/local/apache/conf/httpd.conf.tmp /usr/local/apache/conf/httpd.conf
# service httpd restart

# # ----------------------------------------------------------------------------------------
# PHP

## 安装PHP

# # 1. 获取最新版安装包：
# wget -P /data/release/xjtudance-data/softwares http://php.net/distributions/php-7.1.10.tar.bz2
# # 安装包地址在http://php.net/downloads.php中寻找

# # 2. 解压并安装：
# cd /data/release/xjtudance-data/softwares
# tar -jxvf php-7.1.10.tar.bz2 # 解压
# cd php-7.1.10
# ./configure \
# --prefix=/usr/local/php \
# --with-mcrypt=/usr/include \
# --with-apxs2=/usr/local/apache/bin/apxs \
# --with-config-file-path=/usr/local/php/etc \
# --enable-fpm \
# --enable-ftp \
# --enable-xml \
# --enable-zip \
# --enable-soap \
# --enable-pcntl \
# --enable-shmop \
# --enable-bcmath \
# --enable-shared \
# --enable-mysqlnd \
# --enable-opcache \
# --enable-session \
# --enable-sockets \
# --enable-sysvsem \
# --enable-mbregex \
# --enable-mbstring \
# --enable-gd-native-ttf \
# --enable-inline-optimization \
# --with-gd \
# --with-curl \
# --with-zlib \
# --with-mhash \
# --with-iconv \
# --with-xmlrpc \
# --with-gettext \
# --with-jpeg-dir \
# --with-freetype-dir \
# --with-openssl \
# --without-gdbm \
# --without-pear \
# --disable-debug \
# --disable-rpath \
# --disable-fileinfo
# make
# make test -n
# make install
# libtool --finish /data/release/xjtudance-data/softwares/php-7.1.10/libs

# # 3. 复制php配置文件：
# cp php.ini-production /usr/local/php/etc/php.ini

## 配置php

# # 1. 将php命令加入环境变量
# mkdir -p /data/release/xjtudance-data/old-files/etc/
# cp -p /etc/profile /data/release/xjtudance-data/old-files/etc/profile
# sed '$a\\nPATH=$PATH:/usr/local/php/bin\nexport PATH' /etc/profile > /etc/profile.tmp
# mv /etc/profile.tmp /etc/profile
# source /etc/profile
# echo $PATH
# php -v

# # 2. 配置apache支持php：
# # * 修改apache配置文件/usr/local/apache/conf/httpd.conf：
# sed -e 's/DirectoryIndex index.html/DirectoryIndex index.html index.php/1' -e 's/AddType application\/x-gzip .gz .tgz/    AddType application\/x-gzip .gz .tgz\nAddtype application\/x-httpd-php .php .phtml/1' -e 's/<IfModule unixd_module>/LoadModule php7_module modules\/libphp7.so\n\n<IfModule unixd_module>/1' /usr/local/apache/conf/httpd.conf > /usr/local/apache/conf/httpd.conf.tmp
# mv /usr/local/apache/conf/httpd.conf.tmp /usr/local/apache/conf/httpd.conf
# service httpd restart

## 测试php？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？这有问题，外网无法访问虚拟机，会直接把php文件下载下来
# mkdir -p  /data/release/dance/test
# echo '<?php
# phpinfo();
# ?>' > /data/release/dance/test/testPhp.php
# curl https://xjtudance.top/test/testPhp.php # ？？？？？？？？？？？？？？？？？？？？？？好像加载不了，用207.0.0.1好像可以

# ----------------------------------------------------------------------------------------
# mongo数据库

## 获取MongoDB

# 1. 获取最新版安装包：
wget -P /data/release/xjtudance-data/softwares https://fastdl.mongodb.org/linux/mongodb-linux-x86_64-rhel70-3.4.10.tgz
# 安装包地址在https://www.mongodb.com/download-center#community中寻找

# 2. 解压并复制文件：
cd /data/release/xjtudance-data/softwares
tar -zxf mongodb-linux-x86_64-rhel70-3.4.10.tgz # 解压
mkdir -p /usr/local/mongodb # mongodb文件目录
mkdir -p /usr/local/mongodb/conf # 配置目录
cd mongodb-linux-x86_64-rhel70-3.4.10
cp -r * /usr/local/mongodb # 复制mongodb文件

## 配置MongoDB

# 1. 创建MongoDB数据目录和log目录：
mkdir -p /data/release/xjtudance-data/mongodb
mkdir -p /data/release/xjtudance-data/logs/mongodb
mkdir -p /var/run/mongodb

# 2. 创建mongodb配置文件mongodb.conf：？？？？？？？？？？？？？？？？？？？此处中文字符编码有问题，不是unicode
echo 'dbpath=/data/release/xjtudance-data/mongodb #数据目录存在位置
logpath=/data/release/xjtudance-data/logs/mongodb/mongodb.log #日志文件存放目录
logappend=true #写日志的模式:设置为true为追加
fork=true  #以守护程序的方式启用，即在后台运行
verbose=true
vvvv=true #启动verbose冗长信息，它的级别有 vv~vvvvv，v越多级别越高，在日志文件中记录的信息越详细
maxConns=20000 #默认值：取决于系统（即的ulimit和文件描述符）限制。MongoDB中不会限制其自身的连接
pidfilepath=/var/run/mongodb/mongodb.pid
directoryperdb=true #数据目录存储模式,如果直接修改原来的数据会不见了
profile=0 #数据库分析等级设置,0 关 2 开。包括所有操作。 1 开。仅包括慢操作
slowms=200 #记录profile分析的慢查询的时间，默认是100毫秒
quiet=true
syncdelay=60 #刷写数据到日志的频率，通过fsync操作数据。默认60秒
#port=27017  #端口
#bind_ip = 10.1.146.163 #IP
#auth=true  #开始认证
#nohttpinterface=false #28017 端口开启的服务。默认false，支持
#notablescan=false#不禁止表扫描操作
#cpu=true #设置为true会强制mongodb每4s报告cpu利用率和io等待，把日志信息写到标准输出或日志文件' > /usr/local/mongodb/conf/mongodb.conf

# 3. 将mongodb命令加入环境变量
# * 修改profile文件：
sed 's/PATH=$PATH:\/usr\/local\/php\/bin/PATH=$PATH:\/usr\/local\/php\/bin:\/usr\/local\/mongodb\/bin/1' /etc/profile > /etc/profile.tmp
mv /etc/profile.tmp /etc/profile
source /etc/profile
echo $PATH
mongo -version

# 4. 将mongodb服务脚本加入到init.d/目录：？？？？？？？？？？？？？？？？？？？此处中文字符编码有问题，不是unicode
echo '#!/bin/sh  
# chkconfig: 2345 93 18
# description:MongoDB  

#默认参数设置
#mongodb 家目录
MONGODB_HOME=/usr/local/mongodb

#mongodb 启动命令
MONGODB_BIN=$MONGODB_HOME/bin/mongod

#mongodb 配置文件
MONGODB_CONF=$MONGODB_HOME/conf/mongodb.conf

MONGODB_PID=/var/run/mongodb/mongodb.pid

#最大文件打开数量限制
SYSTEM_MAXFD=65535

#mongodb 名字  
MONGODB_NAME="mongodb"
. /etc/rc.d/init.d/functions

if [ ! -f $MONGODB_BIN ]
then
  # echo "$MONGODB_NAME startup: $MONGODB_BIN not exists! "  
  # exit
fi

start(){
  # ulimit -HSn $SYSTEM_MAXFD
  # $MONGODB_BIN --config="$MONGODB_CONF"  
  # ret=$?
  # if [ $ret -eq 0 ]; then
      # action $"Starting $MONGODB_NAME: " /bin/true
  # else
      # action $"Starting $MONGODB_NAME: " /bin/false
  # fi
}

stop(){
  # PID=$(ps aux |grep "$MONGODB_NAME" |grep "$MONGODB_CONF" |grep -v grep |wc -l) 
  # if [[ $PID -eq 0  ]];then
      # action $"Stopping $MONGODB_NAME: " /bin/false
      # exit
  # fi
  # kill -HUP `cat $MONGODB_PID`
  # ret=$?
  # if [ $ret -eq 0 ]; then
      # action $"Stopping $MONGODB_NAME: " /bin/true
      # rm -f $MONGODB_PID
  # else   
      # action $"Stopping $MONGODB_NAME: " /bin/false
  # fi
}

restart(){
  # stop
  # sleep 2
  # start
}

case "$1" in
  # start)
      # start
      # ;;
  # stop)
      # stop
      # ;;
  # status)
  # status $prog
      # ;;
  # restart)
      # restart
      # ;;
  # *)
      # echo $"Usage: $0 {start|stop|status|restart}"
esac' > /etc/init.d/mongod

# 5. 添加mongodb服务：
chmod +x /etc/init.d/mongod # 为mongod添加可执行权限
chkconfig --add mongod # 将mongodb加入系统服务
chkconfig mongod on # 修改服务的默认启动等级
service mongod start # 启动mongodb
chkconfig mongod on # 开机启动mongodb

# 6. 查看mongodb服务是否成功启动：
mongod -version # 查看安装的服务版本
netstat -ltp | grep 27017 # 查看27017端口是否被mongodb占用
# 以后可使用 service mongod start 启动服务。

# ## 消除warnings

# # 在使用mongo命令进入数据库时，可能会出现一些警告，可有针对性地消除它们。

# # 1. 部分警告的临时处理方法（如果重启计算机会失效）：？？？？？？？？？？？？？？？？？？？？？？好像没起作用
# cat /sys/kernel/mm/transparent_hugepage/enabled
# echo never > /sys/kernel/mm/transparent_hugepage/enabled # 解决 WARNING: /sys/kernel/mm/transparent_hugepage/enabled is 'always'.
# cat /sys/kernel/mm/transparent_hugepage/defrag
# echo never > /sys/kernel/mm/transparent_hugepage/defrag # 解决 WARNING: /sys/kernel/mm/transparent_hugepage/defrag is 'always'.

# # 2. WARNING: soft rlimits too low. rlimits set to 3889 processes, 65535 files. Number of processes should be at least 32767.5 : 0.5 times number of files.
# # 尚未消除

# ## PHP7安装MongoDB拓展

# # 1. 获取最新版安装包：
# wget -P /data/release/xjtudance-data/softwares http://pecl.php.net/get/mongodb-1.3.1.tgz
# # 安装包地址在http://pecl.php.net/package/mongodb中寻找

# # 2. 解压并安装文件：
# cd /data/release/xjtudance-data/softwares
# tar -zxf mongodb-1.3.1.tgz # 解压
# cd mongodb-1.3.1
# phpize
# ./configure --with-php-config=/usr/local/php/bin/php-config
# make
# make -n test
# make install

# # 3. 修改php.ini文件：
# mkdir -p /data/release/xjtudance-data/old-files//usr/local/php/etc/
# cp -p /usr/local/php/etc/php.ini /data/release/xjtudance-data/old-files/usr/local/php/etc/php.ini
# sed 's/;extension=php_xsl.dll/;extension=php_xsl.dll\n\nextension=mongodb.so/1' /usr/local/php/etc/php.ini > /usr/local/php/etc/php.ini.tmp
# mv /usr/local/php/etc/php.ini.tmp /usr/local/php/etc/php.ini

# # 4. 重启Apache或php-fpm：
# service httpd restart
# # service php-fpm restart

# ## 测试MongoDB是否成功连接到PHP
# php -m
# # 也可在浏览器中访问https://xjtudance.top/test/testPhp.php，如其中有mongodb项，则说明php mongo扩展配置成功。

# ### 其他测试方法
# echo '<?php
# $manager = new MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");
# $bulk = new MongoDB\Driver\BulkWrite;
# $bulk->insert(['x' => 1, 'class'=>'toefl', 'num' => '18']);
# $bulk->insert(['x' => 2, 'class'=>'ielts', 'num' => '26']);
# $bulk->insert(['x' => 3, 'class'=>'sat', 'num' => '35']);
# $manager->executeBulkWrite('test.log', $bulk);
# $filter = ['x' => ['$gt' => 1]];
# $options = [
    # 'projection' => ['_id' => 0],
    # 'sort' => ['x' => -1],
# ];
# $query = new MongoDB\Driver\Query($filter, $options);
# $cursor = $manager->executeQuery('test.log', $query);
# foreach ($cursor as $document) {
    # print_r($document);
# }
# ?>' > /data/release/dance/test/testMongo.php
# curl https://xjtudance.top/test/testMongo.php #？？？？？？？？？？？？？？？？？？？？？？好像加载不了

# # ----------------------------------------------------------------------------------------
# # swoole

# # Swoole是一个PHP插件，提供WebSocket等功能，网址为http://www.swoole.com/。

# ## 安装swoole

# # 1. 获取最新版安装包：
# wget -P /data/release/xjtudance-data/softwares http://pecl.php.net/get/swoole-1.9.21.tgz
# # 安装包地址在http://pecl.php.net/package/swoole中寻找

# # 2. 解压并安装文件：
# cd /data/release/xjtudance-data/softwares
# tar -zxf swoole-1.9.21.tgz # 解压
# cd swoole-1.9.21
# phpize
# ./configure \
# --with-php-config=/usr/local/php/bin/php-config \
# --enable-openssl \
# --enable-sockets
# make
# #make test
# make install

# # 3. 修改php.ini文件：
# sed 's/extension=mongodb.so/extension=mongodb.so\nextension=swoole.so/1' /usr/local/php/etc/php.ini > /usr/local/php/etc/php.ini.tmp
# mv /usr/local/php/etc/php.ini.tmp /usr/local/php/etc/php.ini
# # 通过php -m或phpinfo()来查看是否成功加载了swoole，如果没有可能是php.ini的路径不对，可以使用php -i |grep php.ini来定位到php.ini的绝对路径。
# php -m

# # 4. 重启apache服务：
# service httpd restart

# # 5. 打开服务器5902端口。

# ## 测试swoole ？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？这里没有做

# ### 创建WebSocket服务器

# # ----------------------------------------------------------------------------------------
# # 网络配置

# ## php脚本限制
# # 根据需要修改以下各项：
# sed -e 's/max_execution_time = 30/max_execution_time = 90/1' -e 's/upload_max_filesize = 2M/upload_max_filesize = 20M/1' -e 's/post_max_size = 8M/post_max_size = 21M/1' -e 's/max_input_time = 60/max_input_time = 90/1' -e 's/memory_limit = 128M/memory_limit = 128M/1' /usr/local/php/etc/php.ini > /usr/local/php/etc/php.ini.tmp
# mv /usr/local/php/etc/php.ini.tmp /usr/local/php/etc/php.ini
# service httpd restart

# # ----------------------------------------------------------------------------------------
# # 创建服务器文件目录
# mkdir -p /data/release/dance/xjtudance # 创建西交dance目录
# mkdir -p /data/release/dance/xjtudance/php # php目录
# mkdir -p /data/release/dance/xjtudance/data/audios/dance # 音频目录
# mkdir -p /data/release/dance/xjtudance/data/images/dance # 图片目录
# mkdir -p /data/release/xjtudance-data/mongodb-backup # mongo数据库备份文件夹
# mkdir -p /data/release/xjtudance-data/mongodb-backup/BSON # mongo数据库BSON备份文件夹
# mkdir -p /data/release/xjtudance-data/mongodb-backup/JSON-PHP # mongo数据库JSON备份文件夹

# mkdir -p /data/release/dance/xjtudance/test # test目录
# mkdir -p /data/release/dance/xjtudance/test/php # php目录
# mkdir -p /data/release/dance/xjtudance/test/data/audios/dance # 音频目录
# mkdir -p /data/release/dance/xjtudance/test/data/images/dance # 图片目录

# ----------------------------------------------------------------------------------------
# ----------------------------------------------------------------------------------------
# 接下来是安全配置

# 服务器文件系统安全

## 禁止外部网络通过url访问Apache目录

sed -e 's/Options Indexes FollowSymLinks/Options FollowSymLinks/1' /usr/local/apache/conf/httpd.conf > /usr/local/apache/conf/httpd.conf.tmp
mv /usr/local/apache/conf/httpd.conf.tmp /usr/local/apache/conf/httpd.conf

## 给文件系统设置访问权限

### 配置apache用户及用户组权限

# 1. 在CentOS中建立用户dance
groupadd dance # 创建用户组
useradd --shell /sbin/nologin -g dance dance # 创建用户
groups dance # 查看用户及用户组

# 2. 在Apache配置文件/usr/local/apache/conf/httpd.conf中设置User，Group：
sed -e 's/User daemon/User dance/1' -e 's/Group daemon/Group dance/1' /usr/local/apache/conf/httpd.conf > /usr/local/apache/conf/httpd.conf.tmp
mv /usr/local/apache/conf/httpd.conf.tmp /usr/local/apache/conf/httpd.conf
service httpd restart

### 设置网站文件的用户和用户组为dance
chown -R dance:dance /data/release/dance
chown -R dance:dance /data/release/xjtudance-data

### 设定各目录和文件权限

# 权限设定需从上级文件夹向下级走，否则会覆盖下级目录权限。

find /data/release/dance/ -exec chmod 550 {} \; # 服务器根目录dance:dance读执行权限

find /data/release/dance/xjtudance -exec chmod 550 {} \; # xjtudance目录dance:dance读执行权限
find /data/release/dance/xjtudance/php -exec chmod 550 {} \; # php脚本目录dance:dance读执行权限
find /data/release/dance/xjtudance/data -exec chmod 770 {} \; # data目录dance:dance读写执行权限

#find /data/release/xjtudance-data -exec chmod 770 {} \; # xjtudance-data目录dance:dance读写执行权限

find /data/release/dance/test -exec chmod 770 {} \; # 服务器test目录dance:dance读写执行权限
find /data/release/dance/xjtudance/test -exec chmod 550 {} \; # xjtudance test目录dance:dance读执行权限
find /data/release/dance/xjtudance/test/php -exec chmod 550 {} \; # test php脚本目录dance:dance读执行权限
find /data/release/dance/xjtudance/test/data -exec chmod 770 {} \; # test data目录dance:dance读写执行权限

# 将以上代码放置到/data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh中，一遍可以定时自动设置（参见下文）。？？？？？？？？？？？？？？？？？？？？？此处没有设置

## 一些文件及文件夹的位置

# * mongodb的数据和log目录分别置于/data/release/xjtudance-data/mongodb和/data/release/xjtudance-data/logs/mongodb下，请勿置于apache服务器目录内。
# * mongodb备份文件置于/data/release/xjtudance-data/mongodb-backup下，请勿置于apache服务器目录内。
# * dance.conf文件置于/data/release/xjtudance-data/下，请勿置于apache服务器目录内。
# * mongodb-bak.sh和mongobak2json.php置于/data/release/xjtudance-data/mongodb-backup下，请勿置于apache服务器目录内。（这两个文件的内容见下文。）

# ----------------------------------------------------------------------------------------
# 数据库安全

# 服务器端采用mongodb。
	
## 数据库鉴权配置 ？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？暂时没有做

## 自动定时备份数据库

# 将数据库备份到服务器文件，使用两种方式：1）mongodb自带的mongodump命令备份为bson格式，2）自定义的php脚本备份为json格式。其区别如下：
# * JSON可读性强但体积较大，BSON则是二进制文件，体积小但对人类几乎没有可读性。
# * 在一些mongodb版本之间，BSON格式可能会随版本不同而有所不同，所以不同版本之间用mongodump/mongorestore可能不会成功，具体要看版本之间的兼容性。当无法使用BSON进行跨版本的数据迁移的时候，使用JSON格式即mongoexport/mongoimport是一个可选项。跨版本的mongodump/mongorestore个人并不推荐，实在要做请先检查文档看两个版本是否兼容（大部分时候是的）。
# * JSON虽然具有较好的跨版本通用性，但其只保留了数据部分，不保留索引，账户等其他基础信息。使用时应该注意。

# 1. 在/data/release/xjtudance-data/mongodb-backup下创建BSON和JSON-PHP两个文件夹。
mkdir -p /data/release/xjtudance-data/mongodb-backup/BSON
mkdir -p /data/release/xjtudance-data/mongodb-backup/JSON-PHP

# 2. 在/data/release/xjtudance-data/mongodb-backup下创建文件mongobak2json.php，内容为：？？？？？？？？？？？？？？？？？？？此处中文字符编码有问题，不是unicode
echo '<?php
/*******************************************************************************
将mongo数据库内容以JSON格式保存到文件。本程序只保留了数据部分，不保留索引，账户
等其他基础信息。
Version: 0.1 ($Rev: 2 $)
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-10-12
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include('/data/release/dance/xjtudance/php/db_fun.php');

db::saveDB2File($argv[1], $argv[2]);

?>' > /data/release/xjtudance-data/mongodb-backup/mongobak2json.php
# **该文件执行速度相对较慢，注意设置php脚本的允许超时时间为无限，否则可能无法备份全部数据。**

# 查看/data/release/dance/xjtudance/php/db_fun.php文件，并查看其中是否存在以下内容：？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？这里没做，而且有问题，需要改

# 3. 在/data/release/xjtudance-data/mongodb-backup下创建文件mongodb-bak.sh，内容为：
echo '#!/bin/bash
#backup MongoDB

#mongodump命令路径
DUMP=/usr/local/mongodb/bin/mongodump
#EXPORT=/usr/local/mongodb/bin/mongoexport
#临时备份目录
OUT_DIR=/data/release/xjtudance-data/mongodb-backup
#备份存放路径
#TAR_DIR=/data/release/xjtudance-data/mongodb-backup/mongodb_bak_list
#获取当前系统时间
DATE=`date +%Y-%m-%d-%H:%M:%S`
#数据库账号
DB_USER=超级管理员账户
#数据库密码
DB_PASS=超级管理员密码
#DAYS=10代表删除10天前的备份，即只保留近10天的备份
DAYS=10
#最终保存的数据库备份文件
#TAR_BAK_BSON="mongodb-bak-bson_$DATE.tar.gz"
#TAR_BAK_JSON="mongodb-bak-json_$DATE.tar.gz"

cd $OUT_DIR
rm -rf $OUT_DIR/BSON
#rm -rf $OUT_DIR/JSON
mkdir -p $OUT_DIR/BSON
#mkdir -p $OUT_DIR/JSON
#备份全部数据库
$DUMP -h 127.0.0.1:27017 -u $DB_USER -p $DB_PASS --authenticationDatabase "admin" -o $OUT_DIR/BSON
#$EXPORT -h 127.0.0.1:27017 -u $DB_USER -p $DB_PASS --authenticationDatabase "admin" -o $OUT_DIR/JSON
#压缩为.tar.gz格式
#tar -zcvf $TAR_DIR/$TAR_BAK_BSON $OUT_DIR/$BSON
#tar -zcvf $TAR_DIR/$TAR_BAK_JSON $OUT_DIR/$JSON
#删除10天前的备份文件
#find $TAR_DIR/ -mtime +$DAYS -delete
	
#用自定义的php函数导出json格式数据
rm -rf $OUT_DIR/JSON-PHP
mkdir -p $OUT_DIR/JSON-PHP
php /data/release/xjtudance-data/mongodb-backup/mongobak2json.php $DB_USER $DB_PASS

exit' > /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh
# 这是用于备份数据库的批处理文件。

# 4. 修改mongodb-bak.sh文件属性，使其可执行。
chmod +x /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh

# 5. 将mongodb-bak.sh文件编码格式修改为unix。如不设置此项，.sh文件将无法执行。
file /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh # 查看文件编码格式
iconv -f ISO-8859 -t UNIX /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh -o /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh # ？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？此句未测试，不知是否正确（好像有问题）

# 6. 添加到计划任务，修改/etc/crontab。
cp -p /etc/crontab /data/release/xjtudance-data/old-files/etc/crontab
sed '$a\\n30 4 * * * /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh\n30 16 * * * /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh' /etc/crontab > /etc/crontab.tmp
mv /etc/crontab.tmp /etc/crontab
# 表示每天4:30和16:30执行备份。每行命令具体含义内容如下：
# Example of job definition:
# .---------------- minute (0 - 59)
# |  .------------- hour (0 - 23)
# |  |  .---------- day of month (1 - 31)
# |  |  |  .------- month (1 - 12) OR jan,feb,mar,apr ...
# |  |  |  |  .---- day of week (0 - 6) (Sunday=0 or 7) OR sun,mon,tue,wed,thu,fri,sat
# |  |  |  |  |
# *  *  *  *  * user-name  command to be executed
crontab -l # 查看当前用户的crontab？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？？这里好像没起作用

# 7. 启动cron服务。
/sbin/service crond start

# 设置开机自启动：
chkconfig crond on

exit
