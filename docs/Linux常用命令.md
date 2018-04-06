# Linux常用命令

```
rpm -qa # 列出所有安装的包（.so）
rpm -q -l package_name # 列出属于某包的所有文件

yum remove package_name # centos下删除yum安装的包

yum list installed | grep package_name # centos下列出指定名称的包

kill 进程号 # 杀死进程

netstat -ltp | grep 端口号 # 查看端口占用

(sudo) dhclient # 网络自动配置 (DHCP)，当yum、rpm出现问题时可运行该命令尝试修正

cat /etc/redhat-release # 查看操作系统版本号
uname
uname -r
uname -a

yum whatprovides ifconfig # 查看什么包提供ifconfig
yum search ifconfig

ifconfig -a # 查看IP地址

reboot
shutdown now

```

# Linux下常用的操作

## 允许任何ip访问TCP80端口

* 临时配置（重启系统会重置）：
```
iptables -I INPUT -p TCP --dport 80 -j ACCEPT
```

** 下面这种方法在centos下有问题，需要先安装和配置iptables。 **
* 永久生效配置：
1. 修改/etc/selinux/config文件，将 SELINUX=enforcing 这一行注释掉，并且加上一行，如下：
```
# SELINUX=enforcing
SELINUX=disabled
```
2.
```
chkconfig --level 3  iptables off
chkconfig --level 5  iptables off
```
3. 重启linux。

## 调整分辨率

```
ll /boot/grub2/ #查看grub2目录
vi /boot/grub2/grub.cfg #打开 grub.conf
```

## 使用SSH连接VirtualBox下的centos

1. VirtualBox中使用VirtualBox Host-Only Network模式。

2. 虚拟机中：

```
service iptables stop # 关闭防火墙
chkconfig iptables off
service sshd start # 启动ssh服务
yum install net-tools
ifconfig -a # 查看IP地址
```

3. 在xshell中，使用上面查到的ip地址（inet）连接。

## 使用sftp连接VirtualBox下的centos

1. VirtualBox中使用VirtualBox Host-Only Network模式。

2. 虚拟机中：

```
service iptables stop # 关闭防火墙
chkconfig iptables off
yum -y install vsftpd # 安装FTP服务包
chkconfig vsftpd on # 设置开机启动vsftpd ftp服务
service vsftpd restart # 开启FTP服务
setenforce 0 # 关闭selinux
yum install net-tools
ifconfig -a # 查看IP地址
```

3. 在xftp中，使用上面查到的ip地址（inet）连接。

## VirtualBox下挂载共享文件夹

```
mount -t vboxsf dancehome /data/share-dancehome # 挂载共享文件夹（注意前后两个文件夹名不能相同）
```

## Vi中不能正常显示中文的解决方法

```
vi ~/.vimrc
```

在新建的.vimrc文件中添加以下内容:
```
set fileencodings=utf-8,gb2312,gbk,gb18030  
set termencoding=utf-8  
set fileformats=unix  
set encoding=prc 
```