<?php
/*******************************************************************************
session函数，使用mongodb储存，为微信小程序提供服务
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/aishangsalsa/aishangsalsa
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-11-11
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

// helper functions
// -----------------------------------------------------------------------------

/**
* 获取session类

* @param db $db 用于存储session的数据库实例
*/
function wx_session_start($db) {
	$session = new Session($db);
}

/**
* session类
*
* @see https://stackoverflow.com/questions/16667548/how-to-create-session-in-php-with-mongodb
*/
class Session {

	protected $db; // 用于session的数据库

	/**
	 * This decides the lifetime (in seconds) of the session
	 *
	 * @access private
	 * @var int
	 */
	protected $life_time = '+30 days'; // session的生命周期

	/**
	 * This stores the found session collection so that we don't
	 * waste resources by constantly going back for it
	 *
	 * @access private
	 * @var sessions
	 */
	private $_session = array();

	function __construct($db) {
		var_dump('_construct is called.');
		include_once('db_fun.php');
		$this->db = $db;
		$this->open();
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
	 * Constructor
	 */
	function open() {
		var_dump('open is called.');

		// Create index on Session ID
		// @todo 该句执行会抛出异常Authentication failed.
		$this->db->createIndexes('session', [['name' => 'session', 'key' => ['sessionid' => 1], 'unique' => true]]);

		// Register this object as the session handler
		session_set_save_handler(
			array( $this, "openSession" ),
			array( $this, "closeSession" ),
			array( $this, "readSession" ),
			array( $this, "writeSession"),
			array( $this, "destroySession"),
			array( $this, "gcSession" )
		);

		session_start(); // Start the damn session
	}

	/**
	 * Open session
	 *
	 * This function opens a session from a save path.
	 * The save path can be changed the method of opening also can
	 * but we do not change that we just do the basics and return
	 *
	 * @param string $save_path
	 * @param string $session_name
	 */
	function openSession( $save_path, $session_name ) {
		var_dump('openSession is called.');

		global $sess_save_path;

		$sess_save_path = $save_path;

		// Don't need to do anything. Just return TRUE.
		return true;

	}

	/**
	 * This function closes the session (end of session)
	 */
	function closeSession() {
		var_dump('closeSession is called.');

		// Return true to indicate session closed
		return true;

	}

	/**
	 * This is the read function that is called when we open a session.
	 * This function attempts to find a session from the Db. If it cannot then
	 * the session class variable will remain null.
	 *
	 * @param string $sessionid session id.
	 */
	function readSession( $sessionid ) {
		var_dump('readSession is called: '.$sessionid);

		// Set empty result
		$data = '';

		// Fetch session data from the selected database
		$time = time();

		$tempSession = $this->db->read('session', ['sessionid' => $sessionid], ['limit' => 1,]);
		if (!empty($tempSession)) {
			$this->_sessions = json_decode(json_encode($tempSession[0]), true);
			unset($tempSession);
			$this->_sessions['_id'] = new MongoDB\BSON\ObjectId($this->_sessions['_id']['oid']);
			$data = $this->_sessions['session_data'];
		}

		return $data;

	}

	/**
	 * This is the write function. It is called when the session closes and
	 * writes all new data to the DB. It will do two actions depending on whether or not
	 * a session already exists. If the session does exist it will just update the session
	 * otherwise it will insert a new session.
	 *
	 * @param string $sessionid session id
	 * @param mixed $data session data
	 *
	 * @todo Need to make this function aware of other users since php sessions are not always unique maybe delete all old sessions.
	 */
	function writeSession( $sessionid, $data ) {
		var_dump('writeSession is called: '.$sessionid);

		// Write details to session table
		$time = strtotime('+30 days');

		// If the user is logged in record their uid
		$uid = $_SESSION['logged'] ? $_SESSION['uid'] : 0;

		$fields = array(
			"sessionid" => $sessionid,
			"user_id" => $uid,
			"session_data" => $data,
			"expires" => $time,
			"active" => 1
		);

		$fg = $this->db->update('session', 
			['sessionid' => $sessionid], 
			['$set' => $fields],
			['multi' => false, 'upsert' => true]
		);

		// DONE
		return true;
	}

	/**
	 * This function is called when a user calls session_destroy(). It
	 * kills the session and removes it.
	 *
	 * @param string $sessionid
	 */
	function destroySession( $sessionid ) {
		var_dump('destroySession is called: '.$sessionid);

		// Remove from DB
		$this->db->remove('session', ['sessionid' => $sessionid], ['limit' => 0]); // limit 为 0 时，删除所有匹配数据

		return true;
	}

	/**
	 * This function GCs (Garbage Collection) all old and out of date sessions
	 * which still exist in the DB. It will remove by comparing the current to the time of
	 * expiring on the session record.
	 *
	 * @todo Make a cronjob to delete all sessions after about a day old and are still inactive
	 */
	function gcSession() {
		var_dump('gcSession is called.');
		$this->db->remove('session', ['expires' => ['$lt' => strtotime($this->life_time)]], ['limit' => 0]);
		return true;
	}
}

?>