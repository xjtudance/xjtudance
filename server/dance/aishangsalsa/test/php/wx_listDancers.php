<?php
/*******************************************************************************
读取数据库中的舞友列表，发送给小程序。
Version: 0.1 ($Rev: 2 $)
Website: https://github.com/aishangsalsa/aishangsalsa
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-11-07
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/
	
include_once('config.php');

/* if ($dance_release) {
	// 禁止直接从浏览器输入地址访问.PHP文件
	$fromurl="https://57247578.qcloud.la/"; // 跳转往这个地址。
	if( $_SERVER['HTTP_REFERER'] == "" )
	{
		header("Location:".$fromurl);	// 发送header给浏览器以进行跳转
		exit;
	}
} */

// 从小程序端获取数据
$data = file_get_contents('php://input');
$data = json_decode($data, true);
$skip = $data['skip'];
$limit = $data['limit'];
$list_order = $data['list_order'];

// 获取需要返回的values
$getValues = $data['getValues'];
$values = explode('/', $getValues);
$which = array();
foreach ($values as $value) {
	$which = array_merge($which, array($value => 1));
}

// 从数据库读取列表
$db = get_db();
$doc_users = $db->read('users', 
	['dance.baodao' => ['$ne' => '']],
	['projection' => $which,
	'sort' => [$list_order => -1],
	'skip' => $skip,
	'limit' => $limit,
]);

echo json_encode($doc_users);
?>