<?php
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

?>