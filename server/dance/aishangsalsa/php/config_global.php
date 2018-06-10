<?php
/*******************************************************************************
全局变量（参考bmy_wap的配置方法）
Version: 0.1 ($Rev: 2 $)
Website: https://github.com/aishangsalsa/aishangsalsa
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-28
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

$dance_release = true; // 是否为发布版本

$dance_db = "aishangsalsa"; // 数据库名称
$dance_db_backup = "aishangsalsa_backup"; // 备份数据库名称
$data_path = dirname($_SERVER['DOCUMENT_ROOT'])."/aishangsalsa-data/"; // 数据储存位置
$config_file_path = $data_path."aishangsalsa.conf"; // 设置文件路径
?>  
