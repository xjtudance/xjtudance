<?php
include_once('config.php');

// --------------------------------------------------------------------------------------
// 测试db类 （MongoDB新驱动）

$db = get_db();
$contents = $db->read('globaldata', ['name' => 'wxmini'], [
    'projection' => ['appid' => 1, 'secret' => 1],
	'limit' => 1,
]);
$appid = $contents[0]->appid;
$secret = $contents[0]->secret;
var_dump($appid);
echo "ok\n";

$user_info = $db->read('users', 
	['wechat.openid_mini' => 'haha'], 
	['limit' => 1,
])
$user_info = $user_info[0] ?? 1;
var_dump(empty($user_info));

/* // --------------------------------------------------------------------------------------
// 测试MongoDB（新驱动）
include_once('config_global.php');
include_once($config_file_path);

//header('errMsg: 0');

$manager = new MongoDB\Driver\Manager("mongodb://".$MongoDBUserName4Wxmini.":".$MongoDBUserPwd4Wxmini."@localhost:27017");
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->insert(['x' => 1]);
$bulk->insert(['x' => 2]);
$bulk->insert(['x' => 3]);
$manager->executeBulkWrite('xjtudance_test.collection', $bulk);

$filter = ['x' => ['$gt' => 1]];
$options = [
    'projection' => ['_id' => 0],
    'sort' => ['x' => -1],
];

$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $manager->executeQuery('xjtudance_test.collection', $query);

foreach ($cursor as $document) {
    var_dump($document);
}
//echo $manager;
echo "1\n"; */

// db::createUser测试
/* $db = db::getMongoDB();
$doc_user = array(
		"gender" => 'boy', // 性别
		"degree" => array(
			"last_attend" => '1' // 上次签到时间
		));
$templateData = '
{
	"gender": "boy",
	"degree": {
		"last_attend": "2"
	}
}';
return $templateData;
$sec = explode(' ', microtime()); // get t value
$micro = explode('.', $sec[0]);
date_default_timezone_set("Asia/Shanghai");
$time = date('YmdHis').".".substr($micro[1], 0, 3);
$user = db::createUser($db, $templateData, $time, false);
return json_encode($user);

// 拼接测试
$a = array('1a' => '1v', '2a' => array('21v', '22v'));
$b = array('1a' => '1vb', '2a' => array('21vb', '22vb'));
$dbclass = new db;
echo json_encode($dbclass->updateArray($a, $b));
return;
$a = array('2' => 'hao');
$aa = json_encode($a);
$b = 'haha';
$c = <<<END
{
  "form_id": "{{$aa} or {$b}}",         
}
END;
echo $c;
return; */

// 鉴权
/* $mongo = new MongoClient();
$db = $mongo->aishangsalsa;
$db->authenticate("aishangsalsa", "f73682a01093a6adea044e7333d46c90");
$alldb = $db->getCollectionInfo();
echo $db;
echo json_encode($alldb); */

// bmy发文测试
//$db = db::getMongoDB();
//$sessionurl = bmybbs::login($db, new MongoId('599300d155357a4407735072'), 'jajupmochi', 'byy191710 ');
//echo $sessionurl;
//$bmybbs = get_bmybbs();
//$connectable = $bmybbs->getConnectable();
//header('errMsg: 0');

//echo $connectable ? 1 : 0;
//bmybbs::postArticle($sessionurl, 'test', 'test'); // 发表文章
//echo iconv("GB18030//IGNORE", "UTF-8", $result);
//$bmyurl = bmybbs::getLatestArticleUrl($sessionurl, 'jajupmochi'); // 获取该文的bmyurl
//echo $bmyurl;
//$alldb = $db->getCollectionInfo();
//echo json_encode($alldb);

// 正则表达式
/* $mongo = new MongoClient();
$db = $mongo->$dance_db;
$collection_diaries = $db->diaries;
$regex = new MongoRegex('/^jajupmochi（/');
$diary_list = $collection_diaries->find(array('author' => $regex))->sort(array('dnumber' => -1));
echo json_encode(iterator_to_array($diary_list)); */
	
	
/*     $mongo = new MongoClient();
	$db = $mongo->xjtudance;
	$collection = $db->globaldata;
	$contents = $collection->findOne(array('name' => 'wxmini'), array('contents' => true));
	echo $contents["contents"]["appid"];

	$mongo = new MongoClient(); // 连接
	$db = $mongo->test; // 获取名称为 "test" 的数据库，如果数据库在mongoDB中不存在，mongoDB会自动创建
	// 创建集合
	$collection = $db->createCollection("runoobClient");
	echo "Collection created successfully</br>";

    // 插入文档
    $collection = $db->testcollection; // 选择集合
 	$document = array(
	"title" => "MongoDB", 
	"description" => "database", 
	"likes" => 100,
	"url" => "http://www.runoob.com/mongodb/",
	"by", "林林（中文测试）"
	);
	$collection->insert($document);
	echo "data inserted successfully</br>";
	echo "这是一条中文测试</br>";
	
	// 查找文档
	$cursor = $collection->find();
	// 迭代显示文档标题
	echo "查找文档：</br>";
	foreach ($cursor as $document) {
		echo $document["title"] . "</br>";
	}
	
	// 更新文档
	echo "更新文档：</br>";
	$collection->update(array("title"=>"MongoDB"), array('$set'=>array("title"=>"MongoDB 教程")));
	// 显示更新后的文档
	$cursor = $collection->find();
	foreach ($cursor as $document) {
		echo $document["title"] . "</br>";
	}
	
	// 删除文档
	echo "删除文档：</br>";
	$collection->remove(array("title"=>"MongoDB"), array("justOne" => true));
	// 显示更新后的文档
	$cursor = $collection->find();
	foreach ($cursor as $document) {
		echo $document["title"] . "</br>"; 
	}*/
	
//	$alldb = $mongo->listDBs();//获取所有数据库名称
//	echo $alldb;
?>