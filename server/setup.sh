#!/bin/bash
# centos 7 �°�װ dance ������

# �ű�δ���ƣ�����ʱ����ȫ���սű�ִ��
# �����������ű������бꣿ�����������������ĵط�����

# # �����Զ����� (DHCP)����yum��rpm��������ʱ�����и����������
# dhclient

# # �ر���
# yum -y install bzip2 # ��װbzip2��ѹ���
# yum -y install expat-devel
# yum install -y gcc gcc-c++
# yum -y install openssl openssl-devel # ��װopenssl
# yum -y install libxml2 libxml2-devel curl-devel libpng-devel freetype-devel libmcrypt-devel libjpeg-devel # ����php��Ҫ�İ�
# yum -y install autoconf # phpizeʱ��Ҫ
# yum -y install wget # ������������
# yum -y install sed # �������ļ��༭����
# yum -y install libtool # ���ڰ�װphp

# # ----------------------------------------------------------------------------------------
# # apache������
# ## ��װapache
# # 1. ��ȡ���°氲װ����
# mkdir -p /data/release/xjtudance-data/softwares
# wget -P /data/release/xjtudance-data/softwares http://mirrors.tuna.tsinghua.edu.cn/apache//httpd/httpd-2.4.28.tar.bz2
# wget -P /data/release/xjtudance-data/softwares http://mirror.bit.edu.cn/apache//apr/apr-1.6.2.tar.bz2
# wget -P /data/release/xjtudance-data/softwares http://mirror.bit.edu.cn/apache//apr/apr-util-1.6.0.tar.bz2
# #wget -P /data/release/xjtudance-data/softwares ftp://ftp.csx.cam.ac.uk/pub/software/programming/pcre/pcre2-10.30.tar.bz2
# wget -P /data/release/xjtudance-data/softwares ftp://ftp.csx.cam.ac.uk/pub/software/programming/pcre/pcre-8.41.tar.bz2

# # 2. ��ѹ����װ
  cd /data/release/xjtudance-data/softwares
# # tar -jxvf httpd-2.4.28.tar.bz2 # ��ѹ

# # # ��װAPR��APR-Util
# mkdir -p httpd-2.4.28/srclib/
# # ��ѹ
# tar -jxvf apr-1.6.2.tar.bz2 -C httpd-2.4.28/srclib/
# rename apr-1.6.2 apr httpd-2.4.28/srclib/apr-1.6.2
# tar -jxvf apr-util-1.6.0.tar.bz2 -C httpd-2.4.28/srclib/
# rename apr-util-1.6.0 apr-util httpd-2.4.28/srclib/apr-util-1.6.0
# #tar -jxvf pcre2-10.30.tar.bz2
# tar -jxvf pcre-8.41.tar.bz2
# # ��װapr
# cd httpd-2.4.28/srclib/apr
# ./configure --prefix=/usr/local/apr
# make
# make test
# make install
# # ��װAPR-Util
# cd ../apr-util
# ./configure --prefix=/usr/local/apr-util --with-apr=/usr/local/apr/
# make
# make test
# make install
# # ��װpcre#pcre2
# #cd ../../../pcre2-10.30
# #./configure --prefix=/usr/local/pcre2
# cd ../../../pcre-8.41
# ./configure --prefix=/usr/local/pcre
# make
# make check
# make install

# # ��װapache
# cd ../httpd-2.4.28
# #./configure --prefix=/usr/local/apache --enable-mods-shared=all --with-apr=/usr/local/apr/ --with-apr-util=/usr/local/apr-util/ --with-pcre=/usr/local/pcre2/bin/pcre2-config # --prefixָ����װ·����--enable-mods-shared��������֧�ֵĶ�̬����ģ�飬--with-aprָ��APR·����--with-apr-utilָ��APR-util·��
# ./configure --prefix=/usr/local/apache --enable-mods-shared=all --with-apr=/usr/local/apr/ --with-apr-util=/usr/local/apr-util/ --with-pcre=/usr/local/pcre --enable-ssl --enable-so # --prefixָ����װ·����--enable-mods-shared��������֧�ֵĶ�̬����ģ�飬--with-aprָ��APR·����--with-apr-utilָ��APR-util·��
# make
# make test
# make install

## ����apache

# # 1. ע��apacheΪϵͳ����
# cp /usr/local/apache/bin/apachectl /etc/init.d/httpd # ��apache�����ű����Ƶ�ϵͳ�ű�Ŀ¼��

# # 2. ����Apache��������
# # ��#!/bin/sh����������䣺
# mkdir -p /data/release/xjtudance-data/old-files/etc/rc.d/init.d/
# cp -p /etc/rc.d/init.d/httpd /data/release/xjtudance-data/old-files/etc/rc.d/init.d/httpd
# sed '1a #\n#chkconfig: 2345 10 90\n#description: Activates/Deactivates Apache Web Server' /etc/rc.d/init.d/httpd > /etc/rc.d/init.d/httpd.tmp
# mv /etc/rc.d/init.d/httpd.tmp /etc/rc.d/init.d/httpd

# # 3. ����apache�������������������������������������������������������������������������������������������������������������˴���yourip�ƺ��ø�
# mkdir -p /data/release/xjtudance-data/old-files/usr/local/apache/conf/
# cp -p /usr/local/apache/conf/httpd.conf /data/release/xjtudance-data/old-files/usr/local/apache/conf/httpd.conf
# sed 's/#ServerName www.example.com:80/ServerName yourip:80/1' /usr/local/apache/conf/httpd.conf > /usr/local/apache/conf/httpd.conf.tmp
# mv /usr/local/apache/conf/httpd.conf.tmp /usr/local/apache/conf/httpd.conf

# service httpd start
# ps -ef|grep httpd

# # 4. �鿴apache�汾��
# cp /usr/local/apache/bin/httpd /usr/sbin/httpd
# httpd -v

# ### apache����
# # ��������з��ʣ�http://����IP/���ܹ����֡�It works!��ҳ��ʹ���apache���óɹ���
# # ���δ�ɹ����������ǽ��

# ### �޸�apacheĬ����վ����Ŀ¼
# # 1. ����Ŀ¼��
# mkdir -p /data/release/dance
# # 2. �޸�apache�����ļ�/usr/local/apache/conf/httpd.conf��
# sed -e 's/DocumentRoot "\/usr\/local\/apache\/htdocs"/DocumentRoot "\/data\/release\/dance"/1' -e 's/<Directory "\/usr\/local\/apache\/htdocs">/<Directory "\/data\/release\/dance">/1' /usr/local/apache/conf/httpd.conf > /usr/local/apache/conf/httpd.conf.tmp
# mv /usr/local/apache/conf/httpd.conf.tmp /usr/local/apache/conf/httpd.conf
# service httpd restart -v

# # 4. ��дindex.html���²�����/data/release/dance�ļ����£����������������������������������������˴������ַ����������⣬����unicode
# echo '<!DOCTYPE html>
# <html>
# <head>
# <meta charset=\"UTF-8\">
# <title>��ӭ����dance��</title>
# </head>
# <body>
# <h1>Bienvenue �� dance!</h1>
# </body>
# </html>' > /data/release/dance/index.html

# ### Apache������������
# # 1. ȷ��ռ�ö˿ڵ���httpd
# netstat -anp |grep :80
# # 2. �޸�apache�����ļ��������������������������������������������������������������������������������������������������������������˴���yourip��yourdomain�ø�
# sed 's/ServerName yourip:80/ServerName yourdomain:80/1' /usr/local/apache/conf/httpd.conf > /usr/local/apache/conf/httpd.conf.tmp
# mv /usr/local/apache/conf/httpd.conf.tmp /usr/local/apache/conf/httpd.conf
# service httpd restart

# # ----------------------------------------------------------------------------------------
# PHP

## ��װPHP

# # 1. ��ȡ���°氲װ����
# wget -P /data/release/xjtudance-data/softwares http://php.net/distributions/php-7.1.10.tar.bz2
# # ��װ����ַ��http://php.net/downloads.php��Ѱ��

# # 2. ��ѹ����װ��
# cd /data/release/xjtudance-data/softwares
# tar -jxvf php-7.1.10.tar.bz2 # ��ѹ
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

# # 3. ����php�����ļ���
# cp php.ini-production /usr/local/php/etc/php.ini

## ����php

# # 1. ��php������뻷������
# mkdir -p /data/release/xjtudance-data/old-files/etc/
# cp -p /etc/profile /data/release/xjtudance-data/old-files/etc/profile
# sed '$a\\nPATH=$PATH:/usr/local/php/bin\nexport PATH' /etc/profile > /etc/profile.tmp
# mv /etc/profile.tmp /etc/profile
# source /etc/profile
# echo $PATH
# php -v

# # 2. ����apache֧��php��
# # * �޸�apache�����ļ�/usr/local/apache/conf/httpd.conf��
# sed -e 's/DirectoryIndex index.html/DirectoryIndex index.html index.php/1' -e 's/AddType application\/x-gzip .gz .tgz/    AddType application\/x-gzip .gz .tgz\nAddtype application\/x-httpd-php .php .phtml/1' -e 's/<IfModule unixd_module>/LoadModule php7_module modules\/libphp7.so\n\n<IfModule unixd_module>/1' /usr/local/apache/conf/httpd.conf > /usr/local/apache/conf/httpd.conf.tmp
# mv /usr/local/apache/conf/httpd.conf.tmp /usr/local/apache/conf/httpd.conf
# service httpd restart

## ����php�������������������������������������������������������������������������⣬�����޷��������������ֱ�Ӱ�php�ļ���������
# mkdir -p  /data/release/dance/test
# echo '<?php
# phpinfo();
# ?>' > /data/release/dance/test/testPhp.php
# curl https://xjtudance.top/test/testPhp.php # ��������������������������������������������������ز��ˣ���207.0.0.1�������

# ----------------------------------------------------------------------------------------
# mongo���ݿ�

## ��ȡMongoDB

# 1. ��ȡ���°氲װ����
wget -P /data/release/xjtudance-data/softwares https://fastdl.mongodb.org/linux/mongodb-linux-x86_64-rhel70-3.4.10.tgz
# ��װ����ַ��https://www.mongodb.com/download-center#community��Ѱ��

# 2. ��ѹ�������ļ���
cd /data/release/xjtudance-data/softwares
tar -zxf mongodb-linux-x86_64-rhel70-3.4.10.tgz # ��ѹ
mkdir -p /usr/local/mongodb # mongodb�ļ�Ŀ¼
mkdir -p /usr/local/mongodb/conf # ����Ŀ¼
cd mongodb-linux-x86_64-rhel70-3.4.10
cp -r * /usr/local/mongodb # ����mongodb�ļ�

## ����MongoDB

# 1. ����MongoDB����Ŀ¼��logĿ¼��
mkdir -p /data/release/xjtudance-data/mongodb
mkdir -p /data/release/xjtudance-data/logs/mongodb
mkdir -p /var/run/mongodb

# 2. ����mongodb�����ļ�mongodb.conf�����������������������������������������˴������ַ����������⣬����unicode
echo 'dbpath=/data/release/xjtudance-data/mongodb #����Ŀ¼����λ��
logpath=/data/release/xjtudance-data/logs/mongodb/mongodb.log #��־�ļ����Ŀ¼
logappend=true #д��־��ģʽ:����ΪtrueΪ׷��
fork=true  #���ػ�����ķ�ʽ���ã����ں�̨����
verbose=true
vvvv=true #����verbose�߳���Ϣ�����ļ����� vv~vvvvv��vԽ�༶��Խ�ߣ�����־�ļ��м�¼����ϢԽ��ϸ
maxConns=20000 #Ĭ��ֵ��ȡ����ϵͳ������ulimit���ļ������������ơ�MongoDB�в������������������
pidfilepath=/var/run/mongodb/mongodb.pid
directoryperdb=true #����Ŀ¼�洢ģʽ,���ֱ���޸�ԭ�������ݻ᲻����
profile=0 #���ݿ�����ȼ�����,0 �� 2 �����������в����� 1 ����������������
slowms=200 #��¼profile����������ѯ��ʱ�䣬Ĭ����100����
quiet=true
syncdelay=60 #ˢд���ݵ���־��Ƶ�ʣ�ͨ��fsync�������ݡ�Ĭ��60��
#port=27017  #�˿�
#bind_ip = 10.1.146.163 #IP
#auth=true  #��ʼ��֤
#nohttpinterface=false #28017 �˿ڿ����ķ���Ĭ��false��֧��
#notablescan=false#����ֹ��ɨ�����
#cpu=true #����Ϊtrue��ǿ��mongodbÿ4s����cpu�����ʺ�io�ȴ�������־��Ϣд����׼�������־�ļ�' > /usr/local/mongodb/conf/mongodb.conf

# 3. ��mongodb������뻷������
# * �޸�profile�ļ���
sed 's/PATH=$PATH:\/usr\/local\/php\/bin/PATH=$PATH:\/usr\/local\/php\/bin:\/usr\/local\/mongodb\/bin/1' /etc/profile > /etc/profile.tmp
mv /etc/profile.tmp /etc/profile
source /etc/profile
echo $PATH
mongo -version

# 4. ��mongodb����ű����뵽init.d/Ŀ¼�����������������������������������������˴������ַ����������⣬����unicode
echo '#!/bin/sh  
# chkconfig: 2345 93 18
# description:MongoDB  

#Ĭ�ϲ�������
#mongodb ��Ŀ¼
MONGODB_HOME=/usr/local/mongodb

#mongodb ��������
MONGODB_BIN=$MONGODB_HOME/bin/mongod

#mongodb �����ļ�
MONGODB_CONF=$MONGODB_HOME/conf/mongodb.conf

MONGODB_PID=/var/run/mongodb/mongodb.pid

#����ļ�����������
SYSTEM_MAXFD=65535

#mongodb ����  
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

# 5. ���mongodb����
chmod +x /etc/init.d/mongod # Ϊmongod��ӿ�ִ��Ȩ��
chkconfig --add mongod # ��mongodb����ϵͳ����
chkconfig mongod on # �޸ķ����Ĭ�������ȼ�
service mongod start # ����mongodb
chkconfig mongod on # ��������mongodb

# 6. �鿴mongodb�����Ƿ�ɹ�������
mongod -version # �鿴��װ�ķ���汾
netstat -ltp | grep 27017 # �鿴27017�˿��Ƿ�mongodbռ��
# �Ժ��ʹ�� service mongod start ��������

# ## ����warnings

# # ��ʹ��mongo����������ݿ�ʱ�����ܻ����һЩ���棬��������Ե��������ǡ�

# # 1. ���־������ʱ����������������������ʧЧ����������������������������������������������������û������
# cat /sys/kernel/mm/transparent_hugepage/enabled
# echo never > /sys/kernel/mm/transparent_hugepage/enabled # ��� WARNING: /sys/kernel/mm/transparent_hugepage/enabled is 'always'.
# cat /sys/kernel/mm/transparent_hugepage/defrag
# echo never > /sys/kernel/mm/transparent_hugepage/defrag # ��� WARNING: /sys/kernel/mm/transparent_hugepage/defrag is 'always'.

# # 2. WARNING: soft rlimits too low. rlimits set to 3889 processes, 65535 files. Number of processes should be at least 32767.5 : 0.5 times number of files.
# # ��δ����

# ## PHP7��װMongoDB��չ

# # 1. ��ȡ���°氲װ����
# wget -P /data/release/xjtudance-data/softwares http://pecl.php.net/get/mongodb-1.3.1.tgz
# # ��װ����ַ��http://pecl.php.net/package/mongodb��Ѱ��

# # 2. ��ѹ����װ�ļ���
# cd /data/release/xjtudance-data/softwares
# tar -zxf mongodb-1.3.1.tgz # ��ѹ
# cd mongodb-1.3.1
# phpize
# ./configure --with-php-config=/usr/local/php/bin/php-config
# make
# make -n test
# make install

# # 3. �޸�php.ini�ļ���
# mkdir -p /data/release/xjtudance-data/old-files//usr/local/php/etc/
# cp -p /usr/local/php/etc/php.ini /data/release/xjtudance-data/old-files/usr/local/php/etc/php.ini
# sed 's/;extension=php_xsl.dll/;extension=php_xsl.dll\n\nextension=mongodb.so/1' /usr/local/php/etc/php.ini > /usr/local/php/etc/php.ini.tmp
# mv /usr/local/php/etc/php.ini.tmp /usr/local/php/etc/php.ini

# # 4. ����Apache��php-fpm��
# service httpd restart
# # service php-fpm restart

# ## ����MongoDB�Ƿ�ɹ����ӵ�PHP
# php -m
# # Ҳ����������з���https://xjtudance.top/test/testPhp.php����������mongodb���˵��php mongo��չ���óɹ���

# ### �������Է���
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
# curl https://xjtudance.top/test/testMongo.php #��������������������������������������������������ز���

# # ----------------------------------------------------------------------------------------
# # swoole

# # Swoole��һ��PHP������ṩWebSocket�ȹ��ܣ���ַΪhttp://www.swoole.com/��

# ## ��װswoole

# # 1. ��ȡ���°氲װ����
# wget -P /data/release/xjtudance-data/softwares http://pecl.php.net/get/swoole-1.9.21.tgz
# # ��װ����ַ��http://pecl.php.net/package/swoole��Ѱ��

# # 2. ��ѹ����װ�ļ���
# cd /data/release/xjtudance-data/softwares
# tar -zxf swoole-1.9.21.tgz # ��ѹ
# cd swoole-1.9.21
# phpize
# ./configure \
# --with-php-config=/usr/local/php/bin/php-config \
# --enable-openssl \
# --enable-sockets
# make
# #make test
# make install

# # 3. �޸�php.ini�ļ���
# sed 's/extension=mongodb.so/extension=mongodb.so\nextension=swoole.so/1' /usr/local/php/etc/php.ini > /usr/local/php/etc/php.ini.tmp
# mv /usr/local/php/etc/php.ini.tmp /usr/local/php/etc/php.ini
# # ͨ��php -m��phpinfo()���鿴�Ƿ�ɹ�������swoole�����û�п�����php.ini��·�����ԣ�����ʹ��php -i |grep php.ini����λ��php.ini�ľ���·����
# php -m

# # 4. ����apache����
# service httpd restart

# # 5. �򿪷�����5902�˿ڡ�

# ## ����swoole ��������������������������������������������������������������������������������������������û����

# ### ����WebSocket������

# # ----------------------------------------------------------------------------------------
# # ��������

# ## php�ű�����
# # ������Ҫ�޸����¸��
# sed -e 's/max_execution_time = 30/max_execution_time = 90/1' -e 's/upload_max_filesize = 2M/upload_max_filesize = 20M/1' -e 's/post_max_size = 8M/post_max_size = 21M/1' -e 's/max_input_time = 60/max_input_time = 90/1' -e 's/memory_limit = 128M/memory_limit = 128M/1' /usr/local/php/etc/php.ini > /usr/local/php/etc/php.ini.tmp
# mv /usr/local/php/etc/php.ini.tmp /usr/local/php/etc/php.ini
# service httpd restart

# # ----------------------------------------------------------------------------------------
# # �����������ļ�Ŀ¼
# mkdir -p /data/release/dance/xjtudance # ��������danceĿ¼
# mkdir -p /data/release/dance/xjtudance/php # phpĿ¼
# mkdir -p /data/release/dance/xjtudance/data/audios/dance # ��ƵĿ¼
# mkdir -p /data/release/dance/xjtudance/data/images/dance # ͼƬĿ¼
# mkdir -p /data/release/xjtudance-data/mongodb-backup # mongo���ݿⱸ���ļ���
# mkdir -p /data/release/xjtudance-data/mongodb-backup/BSON # mongo���ݿ�BSON�����ļ���
# mkdir -p /data/release/xjtudance-data/mongodb-backup/JSON-PHP # mongo���ݿ�JSON�����ļ���

# mkdir -p /data/release/dance/xjtudance/test # testĿ¼
# mkdir -p /data/release/dance/xjtudance/test/php # phpĿ¼
# mkdir -p /data/release/dance/xjtudance/test/data/audios/dance # ��ƵĿ¼
# mkdir -p /data/release/dance/xjtudance/test/data/images/dance # ͼƬĿ¼

# ----------------------------------------------------------------------------------------
# ----------------------------------------------------------------------------------------
# �������ǰ�ȫ����

# �������ļ�ϵͳ��ȫ

## ��ֹ�ⲿ����ͨ��url����ApacheĿ¼

sed -e 's/Options Indexes FollowSymLinks/Options FollowSymLinks/1' /usr/local/apache/conf/httpd.conf > /usr/local/apache/conf/httpd.conf.tmp
mv /usr/local/apache/conf/httpd.conf.tmp /usr/local/apache/conf/httpd.conf

## ���ļ�ϵͳ���÷���Ȩ��

### ����apache�û����û���Ȩ��

# 1. ��CentOS�н����û�dance
groupadd dance # �����û���
useradd --shell /sbin/nologin -g dance dance # �����û�
groups dance # �鿴�û����û���

# 2. ��Apache�����ļ�/usr/local/apache/conf/httpd.conf������User��Group��
sed -e 's/User daemon/User dance/1' -e 's/Group daemon/Group dance/1' /usr/local/apache/conf/httpd.conf > /usr/local/apache/conf/httpd.conf.tmp
mv /usr/local/apache/conf/httpd.conf.tmp /usr/local/apache/conf/httpd.conf
service httpd restart

### ������վ�ļ����û����û���Ϊdance
chown -R dance:dance /data/release/dance
chown -R dance:dance /data/release/xjtudance-data

### �趨��Ŀ¼���ļ�Ȩ��

# Ȩ���趨����ϼ��ļ������¼��ߣ�����Ḳ���¼�Ŀ¼Ȩ�ޡ�

find /data/release/dance/ -exec chmod 550 {} \; # ��������Ŀ¼dance:dance��ִ��Ȩ��

find /data/release/dance/xjtudance -exec chmod 550 {} \; # xjtudanceĿ¼dance:dance��ִ��Ȩ��
find /data/release/dance/xjtudance/php -exec chmod 550 {} \; # php�ű�Ŀ¼dance:dance��ִ��Ȩ��
find /data/release/dance/xjtudance/data -exec chmod 770 {} \; # dataĿ¼dance:dance��дִ��Ȩ��

#find /data/release/xjtudance-data -exec chmod 770 {} \; # xjtudance-dataĿ¼dance:dance��дִ��Ȩ��

find /data/release/dance/test -exec chmod 770 {} \; # ������testĿ¼dance:dance��дִ��Ȩ��
find /data/release/dance/xjtudance/test -exec chmod 550 {} \; # xjtudance testĿ¼dance:dance��ִ��Ȩ��
find /data/release/dance/xjtudance/test/php -exec chmod 550 {} \; # test php�ű�Ŀ¼dance:dance��ִ��Ȩ��
find /data/release/dance/xjtudance/test/data -exec chmod 770 {} \; # test dataĿ¼dance:dance��дִ��Ȩ��

# �����ϴ�����õ�/data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh�У�һ����Զ�ʱ�Զ����ã��μ����ģ����������������������������������������������˴�û������

## һЩ�ļ����ļ��е�λ��

# * mongodb�����ݺ�logĿ¼�ֱ�����/data/release/xjtudance-data/mongodb��/data/release/xjtudance-data/logs/mongodb�£���������apache������Ŀ¼�ڡ�
# * mongodb�����ļ�����/data/release/xjtudance-data/mongodb-backup�£���������apache������Ŀ¼�ڡ�
# * dance.conf�ļ�����/data/release/xjtudance-data/�£���������apache������Ŀ¼�ڡ�
# * mongodb-bak.sh��mongobak2json.php����/data/release/xjtudance-data/mongodb-backup�£���������apache������Ŀ¼�ڡ����������ļ������ݼ����ġ���

# ----------------------------------------------------------------------------------------
# ���ݿⰲȫ

# �������˲���mongodb��
	
## ���ݿ��Ȩ���� ������������������������������������������������������������������������������������������ʱû����

## �Զ���ʱ�������ݿ�

# �����ݿⱸ�ݵ��������ļ���ʹ�����ַ�ʽ��1��mongodb�Դ���mongodump�����Ϊbson��ʽ��2���Զ����php�ű�����Ϊjson��ʽ�����������£�
# * JSON�ɶ���ǿ������ϴ�BSON���Ƕ������ļ������С�������༸��û�пɶ��ԡ�
# * ��һЩmongodb�汾֮�䣬BSON��ʽ���ܻ���汾��ͬ��������ͬ�����Բ�ͬ�汾֮����mongodump/mongorestore���ܲ���ɹ�������Ҫ���汾֮��ļ����ԡ����޷�ʹ��BSON���п�汾������Ǩ�Ƶ�ʱ��ʹ��JSON��ʽ��mongoexport/mongoimport��һ����ѡ���汾��mongodump/mongorestore���˲����Ƽ���ʵ��Ҫ�����ȼ���ĵ��������汾�Ƿ���ݣ��󲿷�ʱ���ǵģ���
# * JSON��Ȼ���нϺõĿ�汾ͨ���ԣ�����ֻ���������ݲ��֣��������������˻�������������Ϣ��ʹ��ʱӦ��ע�⡣

# 1. ��/data/release/xjtudance-data/mongodb-backup�´���BSON��JSON-PHP�����ļ��С�
mkdir -p /data/release/xjtudance-data/mongodb-backup/BSON
mkdir -p /data/release/xjtudance-data/mongodb-backup/JSON-PHP

# 2. ��/data/release/xjtudance-data/mongodb-backup�´����ļ�mongobak2json.php������Ϊ�����������������������������������������˴������ַ����������⣬����unicode
echo '<?php
/*******************************************************************************
��mongo���ݿ�������JSON��ʽ���浽�ļ���������ֻ���������ݲ��֣��������������˻�
������������Ϣ��
Version: 0.1 ($Rev: 2 $)
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-10-12
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include('/data/release/dance/xjtudance/php/db_fun.php');

db::saveDB2File($argv[1], $argv[2]);

?>' > /data/release/xjtudance-data/mongodb-backup/mongobak2json.php
# **���ļ�ִ���ٶ���Խ�����ע������php�ű�������ʱʱ��Ϊ���ޣ���������޷�����ȫ�����ݡ�**

# �鿴/data/release/dance/xjtudance/php/db_fun.php�ļ������鿴�����Ƿ�����������ݣ�����������������������������������������������������������������û�������������⣬��Ҫ��

# 3. ��/data/release/xjtudance-data/mongodb-backup�´����ļ�mongodb-bak.sh������Ϊ��
echo '#!/bin/bash
#backup MongoDB

#mongodump����·��
DUMP=/usr/local/mongodb/bin/mongodump
#EXPORT=/usr/local/mongodb/bin/mongoexport
#��ʱ����Ŀ¼
OUT_DIR=/data/release/xjtudance-data/mongodb-backup
#���ݴ��·��
#TAR_DIR=/data/release/xjtudance-data/mongodb-backup/mongodb_bak_list
#��ȡ��ǰϵͳʱ��
DATE=`date +%Y-%m-%d-%H:%M:%S`
#���ݿ��˺�
DB_USER=��������Ա�˻�
#���ݿ�����
DB_PASS=��������Ա����
#DAYS=10����ɾ��10��ǰ�ı��ݣ���ֻ������10��ı���
DAYS=10
#���ձ�������ݿⱸ���ļ�
#TAR_BAK_BSON="mongodb-bak-bson_$DATE.tar.gz"
#TAR_BAK_JSON="mongodb-bak-json_$DATE.tar.gz"

cd $OUT_DIR
rm -rf $OUT_DIR/BSON
#rm -rf $OUT_DIR/JSON
mkdir -p $OUT_DIR/BSON
#mkdir -p $OUT_DIR/JSON
#����ȫ�����ݿ�
$DUMP -h 127.0.0.1:27017 -u $DB_USER -p $DB_PASS --authenticationDatabase "admin" -o $OUT_DIR/BSON
#$EXPORT -h 127.0.0.1:27017 -u $DB_USER -p $DB_PASS --authenticationDatabase "admin" -o $OUT_DIR/JSON
#ѹ��Ϊ.tar.gz��ʽ
#tar -zcvf $TAR_DIR/$TAR_BAK_BSON $OUT_DIR/$BSON
#tar -zcvf $TAR_DIR/$TAR_BAK_JSON $OUT_DIR/$JSON
#ɾ��10��ǰ�ı����ļ�
#find $TAR_DIR/ -mtime +$DAYS -delete
	
#���Զ����php��������json��ʽ����
rm -rf $OUT_DIR/JSON-PHP
mkdir -p $OUT_DIR/JSON-PHP
php /data/release/xjtudance-data/mongodb-backup/mongobak2json.php $DB_USER $DB_PASS

exit' > /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh
# �������ڱ������ݿ���������ļ���

# 4. �޸�mongodb-bak.sh�ļ����ԣ�ʹ���ִ�С�
chmod +x /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh

# 5. ��mongodb-bak.sh�ļ������ʽ�޸�Ϊunix���粻���ô��.sh�ļ����޷�ִ�С�
file /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh # �鿴�ļ������ʽ
iconv -f ISO-8859 -t UNIX /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh -o /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh # �����������������������������������������������������������������������˾�δ���ԣ���֪�Ƿ���ȷ�����������⣩

# 6. ��ӵ��ƻ������޸�/etc/crontab��
cp -p /etc/crontab /data/release/xjtudance-data/old-files/etc/crontab
sed '$a\\n30 4 * * * /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh\n30 16 * * * /data/release/xjtudance-data/mongodb-backup/mongodb-bak.sh' /etc/crontab > /etc/crontab.tmp
mv /etc/crontab.tmp /etc/crontab
# ��ʾÿ��4:30��16:30ִ�б��ݡ�ÿ��������庬���������£�
# Example of job definition:
# .---------------- minute (0 - 59)
# |  .------------- hour (0 - 23)
# |  |  .---------- day of month (1 - 31)
# |  |  |  .------- month (1 - 12) OR jan,feb,mar,apr ...
# |  |  |  |  .---- day of week (0 - 6) (Sunday=0 or 7) OR sun,mon,tue,wed,thu,fri,sat
# |  |  |  |  |
# *  *  *  *  * user-name  command to be executed
crontab -l # �鿴��ǰ�û���crontab���������������������������������������������������������������������û������

# 7. ����cron����
/sbin/service crond start

# ���ÿ�����������
chkconfig crond on

exit
