# 服务器安装

# 服务器迁移

## 数据库迁移

在原服务器命令行执行以下命令：
```
mongorestore -h <hostname><:port> -d dbname <path>
```
* --host <:port>, -h <:port>：
MongoDB所在服务器地址，默认为： localhost:27017
* --db , -d ：
需要恢复的数据库实例，例如：test，当然这个名称也可以和备份时候的不一样，比如test2
* --drop：
恢复的时候，先删除当前数据，然后恢复备份的数据。就是说，恢复后，备份后添加修改的数据都会被删除，慎用哦！
* <path>：
mongorestore 最后的一个参数，设置备份数据所在位置，例如：c:\data\dump\test。
你不能同时指定 <path> 和 --dir 选项，--dir也可以设置备份目录。
* --dir：
指定备份的目录
你不能同时指定 <path> 和 --dir 选项。
例如：
```
mongorestore --drop /data/release/xjtudance-data/mongodb-backup/BSON
```

## 文件迁移

使用sftp命令、scp命令