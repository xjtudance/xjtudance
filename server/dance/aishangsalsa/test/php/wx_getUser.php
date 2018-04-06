<?php
/*******************************************************************************
接受用户从小程序端提交的用户id或微信code，从数据库中读取用户信息。
Version: 0.1 ($Rev: 2 $)
Website: https://github.com/aishangsalsa/aishangsalsa
Author: Linlin <jajupmochi@gmail.com>
Updated: 2017-11-07
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include_once('config.php');

// 从小程序端获取数据
$data = file_get_contents("php://input");
$data = json_decode($data, true);
$_id = $data['_id'];

// 获取需要返回的values
$getValues = $data['getValues'];
$values = explode('/', $getValues);
$which = array();
foreach ($values as $value) {
	$which = array_merge($which, array($value => 1));	// 生成findOne的参数
}

$db = get_db();

if ($_id == '') {
	// 从数据库读取小程序的appid和密匙，下面调用微信api时需要使用
	$contents = $db->read('globaldata', 
		['name' => 'wxmini'], 
		['projection' => ['appid' => 1, 'secret' => 1],
		'limit' => 1,
	]);
	$appid = $contents[0]->appid;
	$secret = $contents[0]->secret;

	// 调用微信接口获取用户的openid及本次登录的session_key
	$api = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$data['code']}&grant_type=authorization_code";
	$str = json_decode(httpGet($api), true); // 第二个参数为true时返回array而非object

	// 从数据库拉取用户信息
	$user_info = $db->read('users', 
		['wechat.openid_mini' => $str["openid"]], 
		['projection' => $which,
		'limit' => 1,
	]);
	echo json_encode($user_info);
} else {
	// 从数据库拉取用户信息
	$user_info = $db->read('users', 
		['_id' => new MongoDB\BSON\ObjectId($_id)], 
		['projection' => $which,
		'limit' => 1,
	]);
	echo json_encode($user_info[0]);
}

?>