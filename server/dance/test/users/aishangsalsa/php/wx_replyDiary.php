<?php
/*******************************************************************************
接受用户从小程序端提交的回复，储存到mongo数据库和备份数据库，需要时同步发表到兵
马俑BBS。
Version: 0.1 ($Rev: 2 $)
Website: https://github.com/aishangsalsa/aishangsalsa
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-09-29
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include_once('config.php');

// 从小程序端获取数据
/* $data = file_get_contents('php://input');
$data = json_decode($data, true);
$author = $data['author'];

$db = db::getMongoDB();
//setUserOnline(new MongoId($author), $db);
$collection_users = $db->users;
$user_info = $collection_users->findOne(array('_id' => new MongoId($author)));

$sec = explode(' ', microtime()); // get t value
$micro = explode('.', $sec[0]);
$time = date("YmdHis").".".substr($micro[1], 0, 3);

// 同步到兵马俑BBS
// -----------------------------------------------------------------------------
$bmybbs = get_bmybbs();
$bmy_id = $user_info['bmy']['id'] == '' ? 'jiaodadance' : $user_info['bmy']['id'];
$bmy_password = $user_info['bmy']['password'] == '' ? 'lovedance123' : $user_info['bmy']['password'];
$sessionurl = $bmybbs->login($db, $user_info['_id'], $bmy_id, $bmy_password); // 登录兵马俑
	
$collection_diaries = $db->diaries;
$mamaDiaryInfo = $collection_diaries->findOne(array('_id' => new MongoId($data['mamaId'])), 
	array('reply' => true, 'author' => true, 'title' => true, 'bmyurl' => true));


if ($bmybbs->postArticle($mamaDiaryInfo['bmyurl'], $bmyurl['title'], $sessionurl, $data['content']) == '错误! 两次发文间隔过密, 请休息几秒后再试!') { // 发表文章
	header('errMsg: 错误! 两次发文间隔过密, 请休息几秒后再试!');
	return;
}
$bmyurl = $bmybbs->getLatestArticleUrl($sessionurl, $bmy_id); // 获取该文的bmyurl


// 将diary及相关数据储存到数据库
// -----------------------------------------------------------------------------
// 拼接日记数据
$doc_diary = array(
	'title' => $data['title'], // 标题
	'author' => new MongoId($author), // 作者
	'content' => $data['content'], // 正文
	'father' => new MongoId($data['fatherId']), // ObjectId of 父帖，如值为''则说明没有父帖
	'mama' => new MongoId($data['mamaId']), // ObjectId of 对应主题帖，如为主帖则表示帖和回复的最近修改时间
	'from' => 'wxmini', // 发表位置
	'bmyurl' => $bmyurl, // 兵马俑bbs链接 ??????????????????
);
$doc_diary = db::createDiary($db, $doc_diary, $time, false);
$diary_id = $doc_diary['_id'];

// 更新母帖（主题帖）
$mamaDiaryInfo = $collection_diaries->findOne(array('_id' => new MongoId($data['mamaId'])), array('reply' => true, 'author' => true));
$reply = array_merge($mamaDiaryInfo['reply'], array($doc_diary['_id']));
$author_reply = array();
foreach ($reply as $replyId) {
	$cur_author = $collection_diaries->findOne(array('_id' => $replyId), array('author' => true)); // 这可以改用where???????????????????????????
	$author_reply = array_merge($author_reply, array($cur_author['author']));
}
$author_reply = array_unique($author_reply); // 删除重复元素
$discuss = count($author_reply);
$discuss = in_array($mamaDiaryInfo['author'], $author_reply) ? $discuss : ($discuss + 1); // 参与讨论人数
$collection_diaries->update(array('_id' => new MongoId($data['mamaId'])), 
	array('$set' => array('mama' => $time, 'discuss' => $discuss, 'reply' => $reply)));

if ($data['fatherId'] != $data['mamaId']) {
	// 更新父帖
	$fatherDiaryInfo = $collection_diaries->findOne(array('_id' => new MongoId($data['fatherId'])), array('reply' => true, 'author' => true));
	$reply = array_merge($fatherDiaryInfo['reply'], array($doc_diary['_id']));
	$author_reply = array();
	foreach ($reply as $replyId) {
		$cur_author = $collection_diaries->findOne(array('_id' => $replyId), array('author' => true)); // 这可以改用where???????????????????????????
		$author_reply = array_merge($author_reply, array($cur_author['author']));
	}
	$author_reply = array_unique($author_reply); // 删除重复元素
	$discuss = count($author_reply);
	$discuss = in_array($fatherDiaryInfo['author'], $author_reply) ? $discuss : ($discuss + 1); // 参与讨论人数
	$collection_diaries->update(array('_id' => new MongoId($data['fatherId'])), 
		array('$set' => array('discuss' => $discuss, 'reply' => $reply)));
	
	$father_nickname = $collection_users->findOne(array('_id' => $fatherDiaryInfo['author']), array('nickname' => true));
	$doc_diary['title'] = "@".$father_nickname['nickname']." ".$doc_diary['title']; // 添加@父帖作者昵称字段
}

// 更新users数据库
$userInfo = $collection_users->findOne(array('_id' => new MongoId($author)), array('degree.credit' => true, 'diaries.posts' => true));
$credit = $userInfo['degree']['credit'] + 5 * ($post2bmy ? 2 : 1); // 发文5分，如同步到兵马俑BBS则乘以2
$level = credit2level($credit); // 根据积分修改等级
$diary_posts = array_merge($userInfo['diaries']['posts'], array($doc_diary['_id'])); // 发表日记列表
$collection_users->update(array('_id' => new MongoId($author)),
	array('$set' => array('degree.level' => $level, 'degree.credit' => $credit, 
	'individualized.post2bmy' => $post2bmy, 'diaries.posts' => $diary_posts)));
	
// 更新数据库globaldata
$diary_num = $collection_diaries->count(); // 文章总数
$collection_global = $db->globaldata;
$collection_global->update(array("name" => "dance"), array('$set' => 
	array("diary_num" => $diary_num)));

$author = $collection_users->findOne(array('_id' => new MongoId($author)), 
	array('_id' => true, 'id_dance' => true, 'nickname' => true, 'avatar_url' => true, 'degree' => true));
$doc_diary['author'] = $author;
header('errMsg: 0');
echo json_encode(array($doc_diary)); // 将数据放到一个数组对象里便于小程序端拼接
 */
?>