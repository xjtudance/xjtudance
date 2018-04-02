<?php
/*******************************************************************************
读取数据库中的数据列表，发送给小程序。
Version: 0.1 ($Rev: 3 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-11-05
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

$time1 = microtime();
	
include_once('config.php');

/* if ($dance_release) {
	// 禁止直接从浏览器输入地址访问.PHP文件
	$fromurl="https://xjtudance.top/"; // 跳转往这个地址。
	if( $_SERVER['HTTP_REFERER'] == "" )
	{
		header("Location:".$fromurl);
		exit;
	}
} */

// 从小程序端获取数据
$data = file_get_contents('php://input');
$data = json_decode($data, true);

if (array_key_exists('collection_name', $data)) {

	$arr = getData($data['collection_name'], $data['skip'], $data['limit'], $data['list_order'], $data['query'], $data['getValues'], $data['extraData']);

	header('errMsg: 0');
	echo json_encode($arr);
	//echo microtime() - $time1;
} else {
	header('errMsg: MISS_COLLECTION_NAME');
}

/**
* 从mongo数据库获取数据
* @param string $collection_name 集合名称
* @param integer $skip 跳过的数据数量
* @param integer $limit 获取的数据数量
* @param string $list_order 获取数据的顺序
* @param array $query 查询条件
* @param string $getValues 要获取的具体项
* @param array $extraData 需要从其他集合获取相关的数据
* @return array 获取的数据
*/
function getData($collection_name, $skip = 0, $limit = 1, $list_order = '_id', $query = array(), $getValues = '', $extraData = array()) {
	// 获取当前集合数据
	// -----------------------------------------------------------------------------
	// 获取查询条件
	if (array_key_exists('_id', $query) && is_string($query['_id'])) {
		$query['_id'] = new MongoDB\BSON\ObjectId($query['_id']); // 将字符串格式的_id转为MongoId
	}

	// 获取需要返回的values
	if ($getValues != '') {
		$values = explode('/', $getValues);
		$values[] = '_id'; // 所有返回值都添加_id项
		//$extraQuery = array_column($extraData, 'query');
		//	echo json_encode($extraData);
		//return 'haha';
		array_unique($values); // 去除重复项
		$projection = array_fill_keys($values, 1); // 将所有项的值设为1
	} else {
		$projection = array();
	}

	// 从数据库读取列表
	$db = get_db();
	$doc = $db->read($collection_name, $query, 
		['projection' => $projection,
		'sort' => [$list_order => -1],
		'skip' => $skip,
		'limit' => $limit,
	]);
		
	// 从其他集合获取相关数据
	// -----------------------------------------------------------------------------
/* 	foreach ($arr_doc as &$one_doc) { // 当前集合中取出的每条数据
		if ($extraData != array()) {
			foreach ($extraData as $one_data) { // 所有需要的额外数据列表
				if (array_key_exists('collection_name', $one_data)) {
					// 获取查询条件
					if (array_key_exists('query', $one_data)) {
						foreach ($one_data['query'] as $query_key => $query_item) { // 查询条件列表
							$query_key_tree = explode('.', $getValues); // 把'.'格式的查询条件转换为数组形式
							$this_query = 
							foreach ($query_key_tree as $query_key_node) {
								
							}
							$one_data['query'][$query_key] = $one_doc[];
						}
					}
					$arr = getData($one_data['collection_name'], 
						$one_data['skip'] ? $one_data['skip'] : 0, 
						$one_data['limit'] ? $one_data['limit'] : 1, 
						$one_data['list_order'] ? $one_data['list_order'] : '_id', 
						$one_data['query'] ? $one_data['query'] : array(), 
						$one_data['getValues'] ? $one_data['getValues'] : '', 
						$one_data['extraData'] ? $one_data['extraData'] : array());
				}
			}
			// 
		}
	} */
		
	return $doc;
}
?>