<?php
/*******************************************************************************
数据库函数
Version: 0.1 ($Rev: 4 $)
Website: https://github.com/aishangsalsa/aishangsalsa
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-11-07
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

// helper functions
// -----------------------------------------------------------------------------
/**
* 获取db类
*/
function get_db() {
	return new db;
}

/**
* 数据库类
*/
class db {
	
	protected $manager = null; // manager of the MongoDB Driver.
		
	protected $db_name = ''; // 使用的数据库名.
		
	function __construct() {
		include_once('config_global.php');
		
		// 设置数据库名
		$this->db_name = $dance_db;
		
		// 读取数据库用户名和密匙
		include_once($config_file_path);
		$this->manager = new MongoDB\Driver\Manager("mongodb://".$MongoDBUserName4Wxmini.":".$MongoDBUserPwd4Wxmini."@localhost:27017");
	}
	
	public function __get($property_name)  
	{  
		if(isset($this->$property_name)) {  
			return $this->$property_name;  
		}  
		else {  
			return NULL;  
		}  
	  
	} 
	
	public function __set($property_name, $value)  
	{    
		$this->$property_name = $value;  
	}
	
	// -----------------------------------------------------------------------------
	
	/**
	* read data from MongoDB.
	* @param string $collection_name 集合名
	* @param array $filter filter set for the query operation.
	* @param array $options options set for the query operation.
	* @return array 获取的数据
	*/
	function read($collection_name, $filter, $options) {
		$query = new MongoDB\Driver\Query($filter, $options);
		$cursor = $this->manager->executeQuery($this->db_name.".".$collection_name, $query);
		return iterator_to_array($cursor);
	}
	
	/**
	* insert data to MongoDB.
	* @param string $collection_name 集合名
	* @param array $document 插入的数据
	* @return MongoDB\BSON\ObjectId 插入数据的MongoId
	*/
	function insert($collection_name, $document) {
		$bulk = new MongoDB\Driver\BulkWrite;
		$_id = $bulk->insert($document);
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 0);
		$result = $this->manager->executeBulkWrite($this->db_name.".".$collection_name, $bulk, $writeConcern);
		return $_id;
	}
	
	/**
	* update data in MongoDB.
	* @param string $collection_name 集合名
	* @param array $filter filter set for the query operation.
	* @param array $document 更新的数据
	* @param array $options options set for the query operation.
	*/
	function update($collection_name, $filter, $document, $options) {
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->update($filter, $document, $options);  
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 0);
		$result = $this->manager->executeBulkWrite($this->db_name.".".$collection_name, $bulk, $writeConcern);
	}
	
	/**
	* update data in MongoDB.
	* @param string $collection_name 集合名
	* @param array $filter filter set for the query operation.
	* @param array $options options set for the query operation.
	*/
	function remove($collection_name, $filter, $options) {
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->delete($filter, $options);
		$result = $this->manager->executeBulkWrite($this->db_name.".".$collection_name, $bulk);
	}
	
	/**
	* create indexes for a collection.
	*
	* @param string $collection_name 集合名
	* @param array $indexes Specifies the indexes to create. Each document in the array specifies a separate index.
	* @return array the cursor that the command returned as array.
     */
    public function createIndexes($collection_name, $indexes)
    {
		$command = new MongoDB\Driver\Command(["createIndexes" => $collection_name, "indexes" => $indexes]);
		try {
			$cursor = $this->manager->executeCommand($this->db_name, $command);
			return current($cursor->toArray());
		} catch (MongoDB\Driver\Exception\Exception $e) {
			echo $e->getMessage(), "\n";
			throw ExceptionConverter::toLegacy($e);
		}
    }
	
	/**
	* Counts the number of documents in a collection.
	*
	* @param string $collection_name 集合名
	* @param array $query query operation.
	* @param array $options options set for the query operation.
	* @return integer the number of documents in a collection.
     */
    public function countDocument($collection_name, $query = [], $options = [])
    {
		$command = new MongoDB\Driver\Command(["count" => $collection_name, "query" => $query]);
		try {
			$cursor = $this->manager->executeCommand($this->db_name, $command);
			$current = current($cursor->toArray());
			return $current->n;
		
		} catch (MongoDB\Driver\Exception\Exception $e) {
			// echo $e->getMessage(), "\n";
			throw ExceptionConverter::toLegacy($e);
		}
    }
	
	/**
	* 创建user数据到mongo数据库
	* @param array/string $doc_user 用户数据
	* @param string $time 当前时间
	* @param boolean $return_json 是否以JSON字符串形式返回数据，为false时返回格式为数组
	* @return array 完整的用户数据
	*/
	function createUser($doc_user, $time, $return_json = true) {
 		if (is_string($doc_user)) {
			$doc_user = json_decode($doc_user, true);
			$json_err = json_last_error();
			if ($json_err != JSON_ERROR_NONE) { // 检查json结构
				return $json_err;
			}
		} elseif (!is_array($doc_user)) { // 检查$doc_user是否是数组
			return '数据类型错误！$doc_user数据类型应为JSON字符串或数组';
		}
		
		include_once('config.php');
		// 创建user默认值
		$doc_user_default = array(
			'id_dance' => '', // dance的id
			'nickname' => '', // 昵称
			'password' => '', // 密码
			'avatar_url' => '', //$avatar_url, // 头像图片url
			'gender' => '', // 性别
			'created' => $time, // 账号建立时间
			'degree' => array(
				'level' => credit2level($credit), // 等级
				'credit' => 400, // 首次登录积分
				'last_attend' => '' // 上次签到时间
			),
			'person_info' => array(
				'realname' => '', // 真实姓名
				'eggday' => '', // 生日
				'grade' => '', // 年级
				'major' => '', // 专业
				'hometown' => '', // 家乡
				'address' => '', // 所在地（经度 + 纬度）
				'QQ' => '', // QQ号
				'contact' => '', // 联系方式
				'height' => '' // 身高
			),
			'web' => array(
				'duration' => 0, // 上站时间/秒
				'visit_time' => $time, // 本次访问时间
				'visit_from' => '', // 访问位置
				'lastvisit' => $time, // 上次访问时间
				'ip' => '', // 访问使用的ip地址
				'net_type' => '', // 网络类型
				'online' => true, // 是否在线
				'visited' => 1 // 访问次数
			),
			'individualized' => array(
				'status' => '', // 状态
				'langue' => '', // 语言，使用微信的
				'contentsize' => 5, // 内容/字体大小 ????????????
				'frequent' => array(), // 用户常用
				'notify' => true, // 是否消息提醒
				'post2bmy' => true // 是否将文章同步到兵马俑
			),
			'diaries' => array(
				'posts' => '', // 发表文章
				'upup' => array(), // 顶帖文章
				'favori' => array(), // 收藏文章
				'viewd' => array(), // 已查看文章
				'drafts' => array(), // 草稿
				'list_order' => 'mama' // 排序方式默认为最近一次修改时间
			),
			'social' => array(
				'like' => array(), // ta喜欢的用户
				'liked' => array(), // 喜欢ta的用户
				'friends' => array(), // 朋友
				'blacklist' => array() // 黑名单
			),
			'letters' => array(), // 私信
			'coins' => array(
				'get' => 0, // 收入
				'give' => 0, // 支出
				'cashed' => 0, // 已提现金额
				'remains' => 0, // 余额
				'getnum' => 0, // 被打赏次数
				'givenum' => 0, // 打赏次数
				'getlist' => array(), // 被打赏记录
				'givelist' => array() // 打赏记录
			),
			'rights' => array(
				'silenced' => '', // 禁言结束时间，为空时未被禁言
				'banban' => array(
					'is' => false, // 是否是斑斑
					'apply' => '' // 申请帖/申请卸任帖，为''时表示没申请
				),
				'wingdance' => array(
					'is' => false, // 是否是客服人员
					'apply' => '' // 申请帖/申请卸任帖，为''时表示没申请
				),
				'littlesound' => array(
					'is' => false, // 是否是小音箱
					'apply' => '' // 申请帖/申请卸任帖，为''时表示没申请
				)
			),
			'bmy' => array(
				'id' => '', // 兵马俑id
				'nickname' => '', // 兵马俑昵称
				'password' => '' // 兵马俑登录密码
			),
			'wechat' => array(
				'openid_mini' => '', // 与dance微信小程序对应的用户openid
				'id' => '' // 微信id
			),
			'dance' => array(
				'baodao' => '', // 报到时间，为空时未报到
				'baodao_bmyurl' => '', // 报到对应的兵马俑BBS报到帖
				'ball_tickets' => array(), // 舞会门票
				'danceLevel' => '', // 初入dance时的舞蹈水平
				'knowdancefrom' => '', // 从哪里知道dance????????????????
				'selfIntro' => '', // 自我介绍
				'photos' => array() // 照片地址
			),
			'activities' => array(
				'my_acts' => array(), // 发起活动
				'in_acts' => array() // 参与活动
			),
			'feedbacks' => array(), // 反馈
			'messages' => array() // 消息
		);
		$doc_user = $this->updateArray($doc_user_default, $doc_user); // 用新数组更新默认值
		$doc_user = array_merge($doc_user, ['_id' => $this->insert('users', $doc_user)]);

		if ($return_json == true) {
			$doc_user = json_encode($doc_user);
		}
		return $doc_user;
	}
	
	/**
	* 创建diary数据到mongo数据库
	* @param array/string $doc_diary 用户数据
	* @param string $time 当前时间
	* @param boolean $return_json 是否以JSON字符串形式返回数据，为false时返回格式为数组
	* @return array 完整的用户数据
	*/
	function createDiary($doc_diary, $time, $return_json = true) {
 		if (is_string($doc_diary)) {
			$doc_diary = json_decode($doc_diary, true);
			$json_err = json_last_error();
			if ($json_err != JSON_ERROR_NONE) { // 检查json结构
				return $json_err;
			}
		} elseif (!is_array($doc_diary)) { // 检查$doc_diary是否是数组
			return '数据类型错误！$doc_diary数据类型应为JSON字符串或数组';
		}
		
		include_once('config.php');
		// 创建diary默认值
		$doc_diary_default = array(
			'title' => '', // 标题
			'author' => '', // 作者
			'content' => '', // 正文
			'time' => $time, // 发信时间
			'updated' => $time, // 最近一次修改时间
			'upup' => 0, // 顶帖数
			'favori' => 0, // 收藏数
			'viewed' => 0, // 查看次数
			'father' => '', // ObjectId of 父帖，如值为''则说明没有父帖
			'mama' => $time, // ObjectId of 对应主题帖，如为主帖则表示主帖和回复的最近修改时间
			'discuss' => 1, // 讨论人数，只有自己
			'reply' => array(), // 回帖
			'highlight' => '', // 精华区路径
			'top' => false, // 是否置顶
			'location' => '', // 定位
			'tags' => array(), // 标签
			'from' => '', // 发表位置
			'bmyurl' => '', // 兵马俑bbs链接
			'coiners' => array(), // 金主
			'device' => '', // 发帖设备
			'ip' => '', // 发帖ip地址
			'ipv6' => false, // 是否是ipv6
			'shared' => 0 // 被分享到微信的次数
		);
		$doc_diary = $this->updateArray($doc_diary_default, $doc_diary); // 用新数组更新默认值
		$doc_diary = array_merge($doc_diary, ['_id' => $this->insert('diaries', $doc_diary)]);
	
		if ($return_json == true) {
			$doc_diary = json_encode($doc_diary);
		}
		return $doc_diary;
	}
	
	/**
	* 使用新数组更新原有数组。对于相同键名，如键值非数组，则用新数组键值替换原数组键值；否则递归调用。
	* @param array $array1 原数组
	* @param array $array2 新数组
	* @return array 更新后的数组
	*/
	function updateArray(&$array1, &$array2) {
		static $recursive_counter = 0; // 限制递归调用深度，最多可递归到10层array数据，超过报警
		if (++ $recursive_counter > 10) { // 每次递归调用加1
			return 'possible deep recursion attack!</br>可能受到了深层递归调用攻击！';
		}
 		foreach ($array1 as $key => &$value) {
			if (is_array($value) && array_key_exists($key, $array2)) {
				$arrayTemp = $this->updateArray($value, $array2[$key]);
				$array2 = array_replace($array2, array($key => $arrayTemp));
			}
		}
		$recursive_counter --; // 递归返回后减1
		return array_merge($array1, $array2);
	}
	
	/**
	* 将文件中保存的json格式数据读取到Mongo数据库中
	* @param string $dir 保存数据库的文件目录
	* @return array 成功提示或错误信息
	* @access public
	* @note 此函数会在保存文件到数据库时覆盖同名db，请提前备份数据库。
	*/
	/* function getMongoDataFromFile($dir = '') {
		if(trim($dir) == '') { // 保存路径为空的默认保存路径
			$dir = "/data/release/xjtudance/data/mongodb-backup";
		}
		if(!is_dir($dir)) {
			return array('errMsg' => "PATH_NOT_EXIST");
		}
		
		$mongo = new Mongo();
		$db_names = scandir($dir);
		$db_names = array_diff($db_names, array('..', '.')); // 去除'..'和'.'这两个文件夹
		foreach ($db_names as $db_name) { // 数据库循环
			if ($db_name != 'admin' && $db_name != 'local') {
				$curr_db = $mongo->$db_name;
				$curr_db->drop();
				$curr_db = $mongo->$db_name;
				$db_path = $dir."/".$db_name;
				$collection_names = scandir($db_path);
				$collection_names = array_diff($collection_names, array('..', '.'));
				foreach ($collection_names as $collection_name) { // collection循环
					$collection_path = $db_path."/".$collection_name;
					$collection_name = explode('.', $collection_name);
					$collection_name = $collection_name[0];
					
					$curr_collection = $curr_db->$collection_name;
					$content = file_get_contents($collection_path); // 读取文件
					$content_json = json_decode($content, true); 
					foreach ($content_json as $document) { // 循环保存每条信息
						$_id = $document['_id']['$id'];
						$document['_id'] = new MongoId($_id);
						restoreMongoId($document);
						$curr_collection->insert($document);
					} 
				}
			}

		}
		return array('msg' => "FILE_SAVED_SUCCESS");
	} */

	/**
	* 将数据库内容以json格式保存到文件中
	* @param string $save_dir 保存文件目录
	* @return array 成功提示或错误信息
	* @access public
	*/
	/* function saveMongoDB2File($save_dir = '') {
		if(trim($save_dir) == '') { // 保存路径为空的默认保存路径
			$save_dir = "/data/release/xjtudance/data/mongodb-backup";
		}
		
		$mongo = new Mongo();
		$db_list = $mongo->listDBs();
		$db_list = $db_list['databases'];
		foreach ($db_list as $db_name) {
			$dir_db = $save_dir."/".$db_name['name']; // 创建保存目录，每个数据库一个目录
			if(!is_dir($dir_db) && !mkdir($dir_db, 0777, true)) {
				return array('errMsg' => "PATH_NOT_EXIST");
			}
			$curr_db = $mongo->$db_name['name'];
			$collection_list = $curr_db->getCollectionInfo();
			foreach ($collection_list as $collection_name) { // 保存collection，每个collection一个文件
				$curr_collection = $curr_db->$collection_name['name'];
				$cursor = $curr_collection->find();
				$content = json_encode(iterator_to_array($cursor));
				file_put_contents($dir_db."/".$collection_name['name'].".dancedb", $content);
			}
		}
		return array('msg' => "FILE_SAVED_SUCCESS");
	} */

	/**
	* 将从json文件中读取得到的数据库中的$id转换为MongoId
	* @param array $array 需要转换的数组的引用
	* @access public
	*/
	/* function restoreMongoId(&$array) {
		//static $recursive_counter = 0; // 限制递归调用深度，最多可递归到10层array数据，超过报警
		//if (++ $recursive_counter > 10) { // 每次递归调用加1
		//	die('possible deep recursion attack!</br>可能受到了深层递归调用攻击！');
		//}
		if (array_key_exists('$id', $array)) {
			$array = new MongoId($array['$id']);
		} else {
			foreach ($array as &$value) {
				if (is_array($value)) {
					restoreMongoId($value);
				}
			}
		}
		//$recursive_counter --; // 递归返回后减1
	} */
	
}

?>