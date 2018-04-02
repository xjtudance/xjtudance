<?php
/*******************************************************************************
全局变量（参考bmy_wap的配置方法）
Version: 0.1 ($Rev: 2 $)
Website: https://github.com/xjtudance/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-09-28
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

class globalVariables {
	static $dance_release = true; // 是否为发布版本

	static $dance_db = "xjtudance"; // 数据库名称（在用户体验版和正式发布版中，$dance_db = "xjtudance"）
	static $dance_db_backup = "xjtudance_backup"; // 备份数据库名称（在用户体验版和正式发布版中，$dance_db_backup = "xjtudance_backup"）
	static $data_path = "/data/release/xjtudance-data/"; // 数据储存位置
	static $config_file_path = "/data/release/xjtudance-data/dance.conf"; // 设置文件路径
}

?> 