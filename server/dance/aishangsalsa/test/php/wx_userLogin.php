<?php
/*******************************************************************************
微信小程序端用户登录。
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/aishangsalsa/aishangsalsa
Author: Linlin <jajupmochi@gmail.com>
Updated: 2017-11-11
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include_once('config.php');

// 从小程序端获取数据
$data = file_get_contents("php://input");
$data = json_decode($data, true);
$code = $data['code'];

// 获取需要返回的values
$getValues = $data['getValues'];
$values = explode('/', $getValues);
$which = array();
foreach ($values as $value) {
	$which = array_merge($which, array($value => 1));	// 生成findOne的参数
}

$db = get_db();

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
$openid = $str['openid'];
$session_key = $str['session_key'];
unset($str);

// 生成sessionid
// @todo 该方法由微信官方提供，但微信小程序API文档建议使用操作系统提供的真正随机数机制。
// @see https://github.com/tencentyun/wafer-php-server-sdk/blob/master/lib/Auth/AuthAPI.php
//      https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html#wxloginobject
$sessionid = sha1($session_key . mt_rand());

// 将openid和session_key写入session储存
session_id($sessionid);
wx_session_start($db);
$_SESSION['session_key'] = $session_key;
$_SESSION['openid'] = $openid;

// 从数据库拉取用户信息
$user_info = $db->read('users', 
	['wechat.openid_mini' => $str["openid"]], 
	['projection' => $which,
	'limit' => 1,
]);
if (!empty($user_info)) {
	$user_info = array_merge($user_info, ['sessionid' => $sessionid]);
}
echo json_encode($user_info);

?>