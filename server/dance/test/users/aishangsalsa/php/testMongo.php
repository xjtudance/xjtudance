<?php
include_once('config.php');

// 鉴权
/* $mongo = new MongoClient();
$db = $mongo->aishangsalsa;
$db->authenticate("aishangsalsa", "f73682a01093a6adea044e7333d46c90");
$alldb = $db->getCollectionInfo();
echo $db;
echo json_encode($alldb); */

$db = db::getMongoDB();
//$alldb = $db->getCollectionInfo();
echo $db;
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