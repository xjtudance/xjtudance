<?php
/*******************************************************************************
接受用户从小程序端提交的报到信息，储存到mongo数据库，需要时同步发表到兵马俑BBS。
Version: 0.1 ($Rev: 3 $)
Website: https://github.com/xjtudance/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-09-11
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include_once('config.php');

// 获取用户微信openid
$db = db::getMongoDB();
$collection_global = $db->globaldata;
$contents = $collection_global->findOne(array('name' => 'wxmini'), array('appid' => true, 'secret' => true));
$appid = $contents["appid"];
$secret = $contents["secret"];
$api = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$_POST['code']}&grant_type=authorization_code";
$str = json_decode(httpGet($api), true);

// 上传图片
// -----------------------------------------------------------------------------
$photo_path = saveImage($_FILES['photo']['tmp_name']); // tmp_name没有后缀，这里会把所有文件全部存为jpg????????????????????????????????
if (is_array($photo_path)) {
	echo array('errMsg' => '图片上传失败！');
	return;
}

// 保存数据到数据库
// -----------------------------------------------------------------------------
$sec = explode(' ', microtime()); // get t value
$micro = explode('.', $sec[0]);
date_default_timezone_set("Asia/Shanghai");
$time = date('YmdHis').".".substr($micro[1], 0, 3);
$timeBmy = $sec[1].substr($micro[1], 0, 3);

$collection_users = $db->users;
$user_info = $collection_users->findOne(array('wechat.openid_mini' => $str['openid']));

if ($user_info == null || $user_info['bmy']['id'] == '') { // 未绑定账户使用jiaodadance账户发帖
	$bmy_id = "jiaodadance";
	$bmy_password = "lovedance123";
	$bmy_title = "新手(".$_POST['nickname'].")报到";
} else {
	$bmy_id = $user_info['bmy']['id'];
	$bmy_password = $user_info['bmy']['password'];
	$bmy_title = "新手".$bmy_id."(".$_POST['nickname'].")报到";
}

$bmybbs = get_bmybbs();
$collection_diaries = $db->diaries;
if ($user_info == null) {
	// user数据	
	$diary_posts = array();
 	$doc_user = array(
		"nickname" => $_POST['nickname'], // 昵称
		"gender" => $_POST['gender'], // 性别
		"person_info" => array(
			"eggday" => $_POST['eggday'], // 生日
			"grade" => $_POST['grade'], // 年级
			"major" => $_POST['major'], // 专业
			"hometown" => $_POST['hometown'], // 家乡
			"QQ" => $_POST['QQ'], // QQ号
			"contact" => $_POST['contact'], // 联系方式
			"height" => $_POST['height'] // 身高
		),
		"web" => array(
			"visit_from" => "wxmini" // 访问位置
		),
		"diaries" => array(
			"posts" => $diary_posts, // 发表文章
		),
		"wechat" => array(
			"openid_mini" => $str['openid'], // 与dance微信小程序对应的用户openid
			"id" => $_POST['wechat_id'] // 微信id
		),
		"dance" => array(
			"baodao" => $time, // 报到时间，为空时未报到
			"danceLevel" => $_POST['danceLevel'], // 初入dance时的舞蹈水平
			"knowdancefrom" => $_POST['knowdancefrom'], // 从哪里知道dance????????????????
			"selfIntro" => $_POST['selfIntro'], // 自我介绍
			"photos" => array($photo_path) // 照片地址
		)
	);
	$doc_user = db::createUser($db, $doc_user, $time, false);
	$user_id = $doc_user['_id']; // 用户_id
	$sessionurl = $bmybbs->login($db, $user_id, $bmy_id, $bmy_password); // 登录兵马俑
} else {
	$user_id = $user_info['_id']; // 用户_id
	// 删除上次报到的信息
	// 删除兵马俑BBS对应报到帖
	$sessionurl = $bmybbs->login($db, $user_id, $bmy_id, $bmy_password);
 	if (array_key_exists('baodao_bmyurl', $user_info['dance'])) {
		$bmyurl_old = $user_info['dance']['baodao_bmyurl'];
		$bmybbs->deleteArticle($sessionurl, $bmyurl_old);
	}
	// 删除对应diary
	if (array_key_exists('baodao_diaryId', $user_info['dance'])) {
		$diaryId_old = $user_info['dance']['baodao_diaryId'];
		$collection_diaries->remove(array('_id' => $diaryId_old), array('justOne' => true));
		$diary_posts = $user_info['diary']['posts']; // 删除user中对应的post信息
		$key_id = array_search($diaryId_old, $diary_posts);
		if ($key_id != false) {
			array_splice($diary_posts, $key_id, 1);
		}
	}
	// 删除照片
	if (array_key_exists('photos', $user_info['dance'])) {
		$photos = $user_info['dance']['photos'];
		foreach ($photos as $photo) {
			if (is_string($photo)) {
				unlink($_SERVER['DOCUMENT_ROOT']."/".$photo);
			}
		}
	} 
	
	$doc_user = array(
		"nickname" => $_POST['nickname'], // 昵称
		"gender" => $_POST['gender'], // 性别
		"degree.level" => credit2level($credit), // 等级
		"degree.credit" => $credit,
		"person_info.eggday" => $_POST['eggday'], // 生日
		"person_info.grade" => $_POST['grade'], // 年级
		"person_info.major" => $_POST['major'], // 专业
		"person_info.hometown" => $_POST['hometown'], // 家乡
		"person_info.QQ" => $_POST['QQ'], // QQ号
		"person_info.contact" => $_POST['contact'], // 联系方式
		"person_info.height" => $_POST['height'], // 身高
		"web.visit_from" => "wxmini", // 访问位置
		"web.lastvisit" => $time, // 上次访问时间
		"wechat.id" => $_POST['wechat_id'], // 微信id
		"dance.baodao" => $time, // 报到时间，为空时未报到
		"dance.danceLevel" => $_POST['danceLevel'], // 初入dance时的舞蹈水平
		"dance.knowdancefrom" => $_POST['knowdancefrom'], // 从哪里知道dance????????????????
		"dance.selfIntro" => $_POST['selfIntro'], // 自我介绍
		"dance.photos" => array($photo_path) // 照片地址?????????????????????????????????????????
	);
	$collection_users->update(array('wechat.openid_mini' => $str['openid']), array('$set' => $doc_user));
	$doc_user = $collection_users->findOne(array('wechat.openid_mini' => $str['openid']));
	$user_id = $doc_user['_id']; // 用户_id
}

// 同步到兵马俑BBS
// -----------------------------------------------------------------------------
$credit = ($user_info == null ? 0: $user_info['degree']['credit']) + 400;
$bmy_content = wxminiBaodao($bmy_id, $_POST['nickname'], $_POST['gender'], 
	$_POST['height'], $_POST['grade'], 
	$_POST['major'], $_POST['hometown'], 
	$_POST['selfIntro'], $_POST['danceLevel']).
	wxminiWatermark4bmy($time, $db, credit2level($credit));

if ($bmybbs->postArticle($sessionurl, $bmy_title, $bmy_content) == '错误! 两次发文间隔过密, 请休息几秒后再试!') { // 发表文章
	echo array('errMsg' => '错误! 两次发文间隔过密, 请休息几秒后再试!');
	return;
}
$bmyurl = $bmybbs->getLatestArticleUrl($sessionurl, $bmy_id); // 获取该文的bmyurl
$collection_users->update(array('_id' => $user_id), array('$set' => 
	array('dance.baodao_bmyurl' => $bmyurl)));

// diary数据
// -----------------------------------------------------------------------------
$id = ($bmy_id == 'jiaodadance' ? '小dance代发' : $bmy_id);
$content = "您的id是:\n".$id.
	"\n\n昵称呢?:\n".$_POST['nickname'].
	"\n\n性别:\n".$_POST['gender'].
	"\n\n身高可别忘了:\n".$_POST['height'].
	"\n\n学院/专业:\n".$_POST['major'].
	"\n\n年级:\n".$_POST['grade'].
	"\n\n家乡:\n".$_POST['hometown'].
	"\n\n再介绍一下自己啦:\n".$_POST['selfIntro'].
	"\n\n您的舞蹈水平(参加培训情况等)/擅长或喜欢的舞种?:\n".$_POST['danceLevel'].
	"\n\n打开微信小程序\"西交dance\"查看美照啦~";
$doc_diary = array(
	"title" => $bmy_title, // 标题
	"author" => $user_id, // 作者
	"content" => $content, // 正文
	"highlight" => "虫虫报到/".date('Y')."年新手报到集", // 精华区路径
	"tags" => array('新人报到'), // 标签 ?????????????????????
	"from" => "wxmini", // 发表位置
	"bmyurl" => $bmyurl // 兵马俑bbs链接 ??????????????????？？？？？？？？？？？？？？？？？
);
$doc_diary = db::createDiary($db, $doc_diary, $time, false);
$diary_id = $doc_diary["_id"];
$diary_posts[] = $diary_id; // 发表日记列表
$collection_users->update(array('_id' => $user_id), array('$set' => 
	array('diaries.posts' => $diary_posts, 'dance.baodao_diaryId' => $diary_id))); // 报到对应的diary id

// global数据
// -----------------------------------------------------------------------------
$collection_global = $db->globaldata;
$global_info = $collection_global->findOne(array('name' => 'dance'), array('baodao_num' => true, 'book' => true)); // ?????????????????????????报到人数这直接加一，没有考虑一个人多次报到的情况，且未更新visited和user_online数据
$diary_num = $collection_diaries->count(); // 文章总数
$user_num = $collection_users->count(); // 用户总数
$baodaoY = date('Y')."年新手报到集";
/*  			 $baodaos = $global_info['book']['虫虫报到'][$baodaoY]; ?????????????????????????????????????????????此处未考虑到没有如果已经有book的情况，
			if (!is_array($baodaos)) {
				$book = array(
					'虫虫报到' => array(
						$baodaoY => array(
							$bmy_title => $diary_id)));
			}  */
$collection_global->update(array('name' => 'dance'), array('$set' => 
	array('diary_num' => $diary_num, 'user_num' => $user_num, 
	'baodao_num' => $global_info['baodao_num'] + 1)));

// 报到成功模板消息
// -----------------------------------------------------------------------------
$formId = $_POST['formId'];
$templateId = 'xH1cax55MvsDxJQ-VvSf6iX0r-vbfpRq1zzijSDanWw';
$time = explode('.', $time);
$time = $time[0];
$time = substr_replace($time, '-', 4, 0);
$time = substr_replace($time, '-', 7, 0);
$time = substr_replace($time, ' ', 10, 0);
$time = substr_replace($time, ':', 13, 0);
$time = substr_replace($time, ':', 16, 0);
$templateData = <<<END
{
  "touser": "{$str['openid']}",  
  "template_id": "{$templateId}", 
  "page": "/pages/dancers/dancers",          
  "form_id": "{$formId}",         
  "data": {
      "keyword1": {
          "value": "报到成功啦", 
          "color": "#8A2BE2"
      }, 
      "keyword2": {
          "value": "{$time}", 
          "color": "#173177"
      }, 
      "keyword3": {
          "value": "请加入dance QQ群：40682446（请备注昵称）；稍后斑斑们会把你拉入微信群~", 
          "color": "#173177"
      }
  },
  "emphasis_keyword": "keyword1.DATA" 
}
END;
// access_token每天只能获取2000次，有效期目前为2个小时，需定时刷新
$getTokenApi = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
$resultStr = httpGet($getTokenApi);
$arr = json_decode($resultStr, true);
$token = $arr["access_token"];
// 发送模板消息的api
$templateApi = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token={$token}";
$res = httpPost($templateData, $templateApi);

// 返回报到用户提醒给版务
// -----------------------------------------------------------------------------
$banbans = $collection_users->find(array('rights.banban.is' => true), array('_id' => true, 'messages' => true));
$baodao_info = array(
	'nickname' => $_POST['nickname'], // 昵称
	'gender' => $_POST['gender'], // 性别
	'eggday' => $_POST['eggday'], // 生日
	'grade' => $_POST['grade'], // 年级
	'major' => $_POST['major'], // 专业
	'hometown' => $_POST['hometown'], // 家乡
	'QQ' => $_POST['QQ'], // QQ号
	'contact' => $_POST['contact'], // 联系方式
	'height' => $_POST['height'], // 身高
	'wxid' => $_POST['wechat_id'], // 微信id
	'danceLevel' => $_POST['danceLevel'], // 初入dance时的舞蹈水平
	'knowdancefrom' => $_POST['knowdancefrom'], // 从哪里知道dance????????????????
	'selfIntro' => $_POST['selfIntro'], // 自我介绍
	'photos' => array($photo_path) // 照片地址
);
foreach ($banbans as $banban) {
	$msgs = $banban['messages'];
	if (array_key_exists('baodaos', $msgs)) {
		$baodaos = $msgs['baodaos'];
		$baodaos[] = $baodao_info;
		$msgs['baodaos'] = $baodaos;
	} else {
		$msgs = array_merge($msgs, array('baodaos' => array($baodao_info)));
	}
	$collection_users->update(array('_id' => $banban['_id']), array('$set' => 
		array('messages' => $msgs)));
}

// 返回user数据
echo json_encode($doc_user);
?>