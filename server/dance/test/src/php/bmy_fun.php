<?php
/*******************************************************************************
兵马俑BBS相关函数
Version: 0.1 ($Rev: 4 $)
Website: https://github.com/xjtudance/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-11-06
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

// helper functions
// -----------------------------------------------------------------------------
/**
* 获取兵马俑BBS类
*/
function get_bmybbs() {
	return new bmybbs;
}

/**
* 兵马俑BBS类
*/
class bmybbs {
	
 	protected $connectable = false; // 兵马俑bbs是否可连接

    protected $sessionurl_g = 'BMYAHADSPJYXLKWEMUBMUHOEIRMEEXFFPUUX_B'; // 匿名登录兵马俑的sessionurl
		
	function __construct() {
		return $this->connectBmy();
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
	* 连接兵马俑bbs
	* @param integer $timeout 连接超时时间
	* @return boolean 兵马俑bbs是否可连接
	*/
	function connectBmy($timeout = 5) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, 'http://bbs.xjtu.edu.cn');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // 返回curl爬取过程中获取的http_code
		curl_close($ch);
		$connectable = $httpcode == 0 ? false : true; // http_code为0时说明Unable to access
		$this->connectable = $connectable;
		return $connectable;
	}
	
	/**
	* 登录兵马俑账户
	* @param MongoDB $db 数据库
	* @param MongoId $user_id 用户id
	* @param string $bmy_id 用户兵马俑id
	* @param string $bmy_password 用户兵马俑登录密码
	* @return string sessionurl
	* @note 参考bmy_wap的loginindex.php
	*/
	function login($db, $user_id, $bmy_id, $bmy_password) {
		if ($this->connectable == true) {
			include_once('config.php');
			date_default_timezone_set("Asia/Shanghai");
			
			// 从数据库读取兵马俑sessionurl
			$bmysession = $db->read('users', 
				['_id' => $user_id], 
				['projection' => ['bmy' => 1],
				'limit' => 1,
			])[0];
			if ($bmysession->bmy->sessionurl && time() - $bmysession->bmy->sessiontime < 2592000) {
				return $bmysession->bmy->sessionurl;
			} else {
				// 获取当前时间
				$sec = explode(' ', microtime());
				$micro = explode('.', $sec[0]);
				$time = $sec[1].substr($micro[1], 0, 3);
				
				// 通过bmy的proxy_url获取sessionurl
				$proxy_url = "http://bbs.xjtu.edu.cn/BMY/bbslogin?ipmask=8&t={$time}&id={$bmy_id}&pw={$bmy_password}";
				$result = file_get_html($proxy_url);
					
				if(strstr($result, iconv("UTF-8", "GB18030//IGNORE", "错误! 密码错误!")) || strstr($result, iconv("UTF-8", "GB18030//IGNORE", "错误! 错误的使用者帐号!"))) {
					return array('msg' => '错误! 账号或密码错误!');
				} else {
					if(strstr($result, iconv("UTF-8", "GB18030//IGNORE", "错误! 两次登录间隔过密!!"))) {
						return array('msg' => '错误! 两次登录间隔过密!');
					} else { // 成功登录
						$sessionurl_t = myfind($result, "url=/", "/", 0);
						$sessionurl = $sessionurl_t[0];
						$db->update('users', 
							['_id' => $user_id], 
							['$set' => ['bmy.id' => $bmy_id, 'bmy.password' => $bmy_password, 
								'bmy.sessionurl' => $sessionurl, 'bmy.sessiontime' => time()]],
							['multi' => true, 'upsert' => false]
						);
						return $sessionurl;			
					}
				}
			}
		}

	}
	
	/**
	* 以dance身份登录兵马俑账户
	* @return string sessionurl
	* @note 参考bmy_wap的loginindex.php
	*/
/* 	function login_dance() {
		if ($this->connectable == true) {
			echo 'test1';
			include_once('config.php');
			date_default_timezone_set("Asia/Shanghai");
			
			// 获取当前时间
			$sec = explode(' ', microtime());
			$micro = explode('.', $sec[0]);
			$time = $sec[1].substr($micro[1], 0, 3);
			
			// 通过bmy的proxy_url获取sessionurl
			$proxy_url = "http://bbs.xjtu.edu.cn/BMY/bbslogin?ipmask=8&t={$time}&id=nothing&pw=nothing";
			$result = file_get_html($proxy_url);
				
			if(strstr($result, iconv("UTF-8", "GB18030//IGNORE", "错误! 密码错误!")) || strstr($result, iconv("UTF-8", "GB18030//IGNORE", "错误! 错误的使用者帐号!"))) {
				return array('msg' => '错误! 账号或密码错误!');
			} else {
				if(strstr($result, iconv("UTF-8", "GB18030//IGNORE", "错误! 两次登录间隔过密!!"))) {
					return array('msg' => '错误! 两次登录间隔过密!');
				} else { // 成功登录
					$sessionurl_t = myfind($result, "url=/", "/", 0);
					$sessionurl = $sessionurl_t[0];
					return $sessionurl;			
				}
			}
			
		}

	} */
	
	/**
	* 发表文章到兵马俑。
	* @param string $sessionurl 用户的sessionurl
	* @param string $title 文章标题
	* @param string $content 文章内容
	* @return array 发文返回信息
	*/
	function postArticle($sessionurl, $title, $content) {
		if ($this->connectable == true) {
			$postdata = "title=".urlencode(iconv("UTF-8", "GB18030//IGNORE", $title))."&text=".urlencode(iconv("UTF-8", "GB18030//IGNORE", $content));
			$url = "http://bbs.xjtu.edu.cn/".$sessionurl."/bbssnd?board=dance&th=-1&signature=1";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);    
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			$result = curl_exec($ch);
			curl_close($ch);
			
			if(strstr($result, iconv("UTF-8", "GB18030//IGNORE", "错误! 两次发文间隔过密, 请休息几秒后再试!"))) {
				return '错误! 两次发文间隔过密, 请休息几秒后再试!';
			} else {
				return '发文成功！';
			}
		}		
	}
	
	/**
	* 删除兵马俑文章。
	* @param string $sessionurl 用户的sessionurl
	* @param string $bmyurl 要删除的文章url
	*/
	function deleteArticle($sessionurl, $bmyurl) {
		if ($this->connectable == true) {
			$proxy_url = "http://bbs.xjtu.edu.cn/".$sessionurl."/del?B=dance&F=".$bmyurl;
			$result = file_get_html($proxy_url);
		}
	}
	
	/**
	* 回复兵马俑文章。
	* @param string $fatherUrl 被回复文章的兵马俑url
	* @param string $fatherTitle 被回复文章的标题
	* @param string $sessionurl 用户的sessionurl
	* @param string $content 文章内容
	* @param string $title 文章标题
	* @access public
	*/
	function replyArticle($fatherUrl, $fatherTitle, $sessionurl, $content, $title = '') {
		if ($this->connectable == true) {
			if ($fatherUrl == '') { // 父帖不存在
				return '父帖不存在！';
			}
			if ($title == '') { // 标题为空时使用被回复文章标题加Re
				$title = $fatherTitle;
			}
			if(!strstr($title, 'Re: ')) {
				$title = 'Re: '.$title;
			}
			$ref = rtrim($fatherUrl, '.A');
			$postdata = "title=".urlencode(iconv("UTF-8", "GB18030//IGNORE", $title))."&text=".urlencode(iconv("UTF-8", "GB18030//IGNORE", $content));
			$url = "http://bbs.xjtu.edu.cn/".$sessionurl."/bbssnd?board=dance&th=-1&ref=".$ref."&rid=-1";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($ch);
			curl_close($ch);
			
			if(strstr($result, iconv("UTF-8", "GB18030//IGNORE", "错误! 两次发文间隔过密, 请休息几秒后再试!"))) {
					return '错误! 两次发文间隔过密, 请休息几秒后再试!';
				} else {
					return '发文成功！';
			}
		}
	}
	
	/**
	* 获取用户最近发表文章的bmyurl
	* @param string $sessionurl 用户的sessionurl
	* @param string $bmy_id 用户兵马俑id
	* @return string bmyurl
	*/
	function getLatestArticleUrl($sessionurl, $bmy_id) {
		$bmyurl = '';
		if ($this->connectable == true) {
			$proxy_url = "http://bbs.xjtu.edu.cn/".$sessionurl."/home?B=dance&S=";
			$result = file_get_html($proxy_url);
			$user_list = $result->find('td[class=tduser] a');
			$article_list = $result->find('.tdborder a');
			for ($offset = 19; $offset >= 0; $offset--) {
				if ($user_list[$offset]->innertext == $bmy_id) { // 寻找该作者最近发布的文章
					$article_f = myfind(substr($article_list[$offset]->href, 3), "?B=dance&F=", "&N=", 0);
					$bmyurl = $article_f[0];
					break;
				}
			}		
		}
		return $bmyurl;
	}
	
	/**
	* 返回微信小程序报到内容。
	* @param string $id id
	* @param string $nickname 昵称
	* @param string $gender 性别
	* @param string $height 身高
	* @param string $grade 年级
	* @param string $major 专业
	* @param string $hometown 家乡
	* @param string $selfIntro 自我介绍
	* @param string $danceLevel 舞蹈水平
	* @return string 报到内容
	* @access public
	*/
	function wxminiBaodao($id, $nickname, $gender, $height, $grade, $major, $hometown, $selfIntro, $danceLevel) {
		$id = ($id == 'jiaodadance' ? '小dance代发' : $id);
		return "[0;1;31m[I您的id是:[m\n".$id.
			"\n\n[0;1;32m[I昵称呢?:[m\n".$nickname.
			"\n\n[0;1;33m[I性别:[m\n".$gender.
			"\n\n[0;1;34m[I身高可别忘了:[m\n".$height.
			"\n\n[0;1;35m[I学院/专业:[m\n".$major.
			"\n\n[0;1;36m[I年级:[m\n".$grade.
			"\n\n[0;1;31m[I家乡:[m\n".$hometown.
			"\n\n[0;1;32m[I再介绍一下自己啦:[m\n".$selfIntro.
			"\n\n[0;1;33m[I您的舞蹈水平(参加培训情况等)/擅长或喜欢的舞种?:[m\n".$danceLevel.
			"\n\n[0;1;34m[I打开微信小程序\"西交dance\"查看美照啦~[m";
	}

	/**
	* 微信小程序水印，从小程序发文到兵马俑BBS时添加到文末。
	* @param string $time 时间
	* @param MongoDB $db mongo数据库
	* @param string $level 用户等级
	* @return string 水印
	* @access public
	*/
	function wxminiWatermark4bmy($time, $db, $level) {
		include_once('config.php');
		$time = explode('.', $time);
		$time = $time[0];
		$time = substr_replace($time, '-', 4, 0);
		$time = substr_replace($time, '-', 7, 0);
		$time = substr_replace($time, ' ', 10, 0);
		$time = substr_replace($time, ':', 13, 0);
		$time = substr_replace($time, ':', 16, 0);
		$watermark = "\n\n
				[1;34m********************************************************************************[m
				[1;33m".$time."[m
				[0;36m我从[m [5m[0;35mdance微信小程序 - 西交dance[m[m [0;36m发来这篇文章[m
				[0;36m这是dance的第[m[1;36m[4m".($db->countDocument('diaries') + 1)."[m[m[0;36m篇文章[m\n
				[0;36m我与[m[1;32m[4m".$db->countDocument('users')."[m[m[0;36m位舞友在dance切磋[m
				[0;36m我的称号是[m[0;31m[4m".$level."[m[m
				[1;34m********************************************************************************[m";
		return $watermark;
	}


    /**
     * 从指定的兵马俑文章页获取文章内容
     * @todo 图片/附件使用的是精华区格式的url，如果从文章列表下载会得不到图片
     */
    function getArticle($url, $title_default = '') {
        // 获取文章各项数据
        $result = file_get_html($url); // 本页面所有内容
        // echo $result;
        // // echo iconv("GB18030", "UTF-8//IGNORE", $result->plaintext);
        // echo '                  ---------------------                               ';
        $result_main = $result->find('.bordertheme', 0);
        // echo iconv("GB18030", "UTF-8//IGNORE", $result_main);
        // echo 'result_main: '.$result_main."\n--------\n";
        $result_inner = $result_main->innertext;
        // echo iconv("GB18030", "UTF-8//IGNORE", $result_inner);
        // echo $result_inner;
        // echo '                  ---------------------                               ';
        $title = myfind($result_inner, mb_convert_encoding("标 &nbsp;题: ", "GB18030", "UTF-8"), "\n<br>", 0); // 标题
        if ($title) { // 存在标题，正常情况
            $title = trim($this->convertStr($title[0]));
            // echo iconv("GB18030", "UTF-8//IGNORE", $title);
            // echo '                  ---------------------                               ';
            $author_id = myfind($result_inner, mb_convert_encoding("发信人: ", "GB18030", "UTF-8"), " (", 0); // 作者id
            // echo iconv("GB18030", "UTF-8//IGNORE", $author_id[0]);
            // echo '                  ---------------------                               ';
            $author_nickname = myfind($result_inner, $author_id[0]." (", "),", 0); // 作者昵称
            $author_nickname = $author_nickname[0];
            // echo iconv("GB18030", "UTF-8//IGNORE", $author_nickname);
            // echo '                  ---------------------                               ';
            $beforetime = myfind($result_inner, mb_convert_encoding("发信站: ", "GB18030", "UTF-8"), "(", 0);
            $timeTemp = myfind($result_inner, $beforetime[0]."(", ")", 0);
            $time = str_replace(mb_convert_encoding("&nbsp;", "GB18030", "UTF-8"), "", $timeTemp[0]); // 发表时间
            $time = $this->convertTimeFormat($time); // 将时间转换为数据库储存形式
            // echo iconv("GB18030", "UTF-8//IGNORE", $time);
            // echo '                  ---------------------                               ';
            $beforecontent = myfind($result_inner, $timeTemp[0], "<br>", 0);    
            $content = myfind($result_inner, $beforecontent[0]."<br>\n", "\n<br><div class=\"con_sig\">--", 0); // 正文
            if ($content[0] == '') {
                $content = myfind($result_inner, $beforecontent[0]."<br>\n", "</div>", 0);
            }
            $content = $content[0];
            // echo iconv("GB18030", "UTF-8//IGNORE", $content);
            // echo '                  ---------------------                               ';        
        } else {
            // echo 'non title'."\n--------\n";
            $title = trim($this->convertStr($title_default));
            $author_nickname = '';
            $content = $result_main->find('div[id="filecontent"]', 0)->innertext;
            // echo iconv("GB18030", "UTF-8//IGNORE", $content);
            // echo '                  ---------------------                               ';
            date_default_timezone_set('Asia/Shanghai');
            $time = rand(1000, 2018).'0314150926.535'; // 没有时间时随机生成
        }

        // 正文特殊字符和格式处理
        $quote = myfind($content, mb_convert_encoding("\n<br>【 在 ", "GB18030", "UTF-8"), 
                        "\n<br></font>", 0); // 正文中引用之前文章的部分
        if ($quote && $quote[0] !== "") { // 去掉正文中引用之前文章的部分
            $content = str_replace(mb_convert_encoding("\n<br>【 在 ", "GB18030", "UTF-8").$quote[0]."\n<br></font>", "", $content);
        }
        $content = $this->convertStr($content);

        // 获取文中图片，并以图片为分隔符将文本分段
        $img_list = $result_main->find('img'); // 图片标签列表
        // echo json_encode($img_list);
        // echo '                  ---------------------                               ';
        // echo 'img_number: '.sizeof($img_list)."\n--------\n";
        $content_list = array();
        foreach ($img_list as $img) {

            $img_src = $this->convertStr($img->src);
            // echo 'img_src: '.json_encode($img_src)."\n--------\n";
            $img_url = 'http://bbs.xjtu.edu.cn/BMY/'.$img_src; // 图片url，通用格式，但目前（2018.3.31）兵马俑bbs的附件默认设置为无法浏览图片，因此获取文章列表图片需要采用下边被注释的url格式
            // $img_src = array_slice(explode('/', $img->src), -3);
            // $attach_name = $img_src[2];
            // $attach_pos = $img_src[1];
            // $pos_label = $img_src[0];
            // $img_url = 'http://bbs.xjtu.edu.cn/BMY/attach/bbscon/'.$attach_name.'?B=dance&F='.$pos_label.'&attachpos='.$attach_pos.'&attachname=/'.$attach_name; // 图片url，支持文章列表格式
            // echo 'img_url: '.$img_url."\n--------\n";
            $save_dir = 'data/images/xjtudance/bmyjinghua/'.substr($time, 0, 4).'/'.substr($time, 4, 2).'/'.substr($time, 6, 2).'/';
            $img_path = saveImage($img_url, $save_dir); // 保存图片到本地
            // echo "img path: ".$img_path."\n--------\n";

            if ($img_path) { // 如果图片获取成功
                $img_link = $result_main->find('a[href="'.$img->src.'"]');
                // 用img标签分隔文本
                $img_deli = $this->convertStr($img_link[0]->outertext); 
                if (strstr($content, $img_deli)) { // 排除正文字符串中不包含该img标签的情况
                    $content_tmp = explode($img_deli, $content); // 拆分文本
                    $content_tmp[0] = trim($content_tmp[0]);
                    if ($content_tmp[0] != '') { // 去除空内容
                        // 保存文档，strip_tags函数用于去除字符串中可能残留的html标签，如<font>
                        $content_list[] = array('metatype' => 'text',
                                                'subtype' => 'plain',
                                                'body' => mb_convert_encoding(strip_tags($content_tmp[0]), "UTF-8", "GB18030"));
                                                //iconv("GB18030//IGNORE", "UTF-8//IGNORE", strip_tags($content_tmp[0])));                         
                    }
                    $content_list[] = array('metatype' => 'img',
                                            'subtype' => substr(strrchr($img_url, '.'), 1),
                                            'body' => $img_path);
                    $content = $content_tmp[1];
                } else {
                    break;
                }                
            }
        }
        $content = trim($content);
        if ($content != '') {
            $content_list[] = array('metatype' => 'text',
                                    'subtype' => 'plain',
                                    'body' => mb_convert_encoding(strip_tags($content), "UTF-8", "GB18030"));            
        }
		
        // 拼接数据
        $doc_jinghua = array(
            'title' => mb_convert_encoding($title, "UTF-8", "GB18030"), // 标题
            'author' => mb_convert_encoding($author_nickname, "UTF-8", "GB18030"), // 作者
            'content' => $content_list, // 正文内容
            'create_time' => mb_convert_encoding($time, "UTF-8", "GB18030"), // 发信时间
            'update_time' => mb_convert_encoding($time, "UTF-8", "GB18030") // 最近一次更新时间
        );

        // var_dump(json_encode($doc_jinghua, JSON_UNESCAPED_UNICODE));

        return $doc_jinghua;
    }


    /**
     * 获取兵马俑bbs精华区全部文章并储存
     */
    function getJinghua($url = 'http://bbs.xjtu.edu.cn/BMYAHADSPJYXLKWEMUBMUHOEIRMEEXFFPUUX_B/0an?path=/groups/GROUP_8/dance', $path = '', $stime = 0) {
        $result = file_get_html($url); // 本页面所有内容
        $table = $result->find('table', 0); // 正文列表
        // echo iconv("GB18030", "UTF-8//IGNORE", $table);
        // echo '                  ---------------------                               ';        
	
        $list_all = $table->find('td[class=tdborder]'); // list of all items in this page
        // echo "list: ".$list_all->outertext."\n";
        $item = current($list_all); // 获取第一项
        while ($item)
        {
            $item = next($list_all); // 第一项为编号，跳过
            $type = $item->innertext; // type
            // echo iconv("GB18030", "UTF-8//IGNORE", $type);
            // echo '                  ---------------------                               ';

            $item = next($list_all)->find('a', 0);
            $title = $item->innertext; // title
            // echo iconv("GB18030", "UTF-8//IGNORE", $title);
            // echo '                  ---------------------                               ';
            $href = $item->href; // link to the content page
            // echo iconv("GB18030", "UTF-8//IGNORE", $href);
            // echo '                  ---------------------                               ';

            $item = next($list_all)->find('a', 0);
            $collector = $item ? $item->innertext : ''; // 整理者
            // echo "collector1: ".$collector."\n";

            if (strpos($type, mb_convert_encoding("目录", "GB18030", "UTF-8"))) { // 如果是目录
                //            print('this is 目录');
                $this_url = 'http://bbs.xjtu.edu.cn/'.$this->sessionurl_g.'/'.$href;
                $this_path = $path.'/'.$title;
                $this->getJinghua($this_url, $this_path, $stime);

            } else {
                // print('this is 文件');
                if (!array_key_exists('db_dance', $GLOBALS)) {
                    $GLOBALS['db_dance'] = get_db(); // @todo: 待优化，不推荐globals变量
                }
                if (!array_key_exists('article_count', $GLOBALS)) {
                    $GLOBALS['article_count'] = 0;
                }
                if (!defined('STIME')) {
                    define('STIME', microtime(true));
                }
                if (!array_key_exists('output_length', $GLOBALS)) {
                    $GLOBALS['output_length'] = 0;
                }

                $this_url = 'http://bbs.xjtu.edu.cn/'.$this->sessionurl_g.'/'.$href;
                $doc_jinghua = $this->getArticle($this_url, $title);
                $doc_jinghua['tags'] = $this->generateTag(mb_convert_encoding($path, "UTF-8", "GB18030").'/'.$doc_jinghua['title']); // 添加标签
                // 给带图文章加上‘倩影永驻’标签
                if (in_array('img', array_column($doc_jinghua['content'], 'metatype'))
                    && !in_array('倩影永驻', $doc_jinghua['tags'])) {
                    $doc_jinghua['tags'][] = '倩影永驻';
                }
                $doc_jinghua['collector'] = $collector; // 整理人

                // var_dump(json_encode($doc_jinghua, JSON_UNESCAPED_UNICODE));
                // echo '                  ---------------------                               ';

                // $db = get_db(); // @todo: 待优化，不要每次创建新的db对象
                $GLOBALS['db_dance']->insert('jinghua', $doc_jinghua); // 储存文章到数据库

                $GLOBALS['article_count']++;
                
                $output_msg =  "\r".$GLOBALS['article_count'].' articles saved, '.(microtime(true) - STIME)."s passed.";
                if (strlen($output_msg) < $GLOBALS['output_length']) {
                    $output_msg = str_pad($output_msg, $GLOBALS['output_length']);
                }
                $GLOBALS['output_length'] = strlen($output_msg);
                echo $output_msg;
            }

            $item = next($list_all);
        }
	
    }


    /**
     * 替换字符串中的特殊字符
     */
    function convertStr($str) {
        $str = str_replace(mb_convert_encoding("&quot;", "GB18030", "UTF-8"), "\"", $str);
        $str = str_replace(mb_convert_encoding("&amp;", "GB18030", "UTF-8"), "&", $str);
        $str = str_replace(mb_convert_encoding("&lt;", "GB18030", "UTF-8"), "<", $str);
        $str = str_replace(mb_convert_encoding("&gt;", "GB18030", "UTF-8"), ">", $str);
        $str = str_replace(mb_convert_encoding("&nbsp;", "GB18030", "UTF-8"), " ", $str);
        $str = str_replace(mb_convert_encoding("<br>", "GB18030", "UTF-8"), "", $str); // html换行符<br>直接去掉
        return $str;
    }


    /**
     * 根据兵马俑文章的题目和目录等内容生成标签
     */
    function generateTag($raw_text) {
        $tags = array();
        // 从tag到raw_text的映射
        $tag_map = array('虫虫报到' => '报道/报到',
                         '作业帖' => '作业',
                         '拜师收徒' => '开贴/开帖/收徒/拜师/拜.为师',
                         '灌水吐槽' => '灌水/吐槽',
                         '舞艺论坛' => '舞蹈☆知识/舞艺☆论坛/技术',
                         '资源共享' => '网络☆资源',
                         '舞会忽悠' => '舞会/思源/宪梓堂/sy/xzt/扫场',
                         '教学扫盲' => '扫盲/教学',
                         '活动纪实' => '活动☆纪实/腐败/版庆',
                         '虫虫生日' => '虫虫☆生日',
                         '离别的歌' => '离别☆的歌',
                         '倩影永驻' => '虫虫☆相册',
                         '有感而发' => '虫虫☆文集',
                         '虫虫考志' => '虫虫☆考志',
                         '表演比赛' => '表演/舞蹈大赛',
                         'dance版志' => 'DNC/版志/',
                         'dance版衫' => 'dance 版衫',
                         '舞服舞鞋' => '舞服☆舞鞋',
                         '舞曲' => '舞曲/音乐',
                         '斑斑' => '版主/斑斑/版务',
                         'dance发展' => 'dance版发展',
                         '小音箱' => '音箱管理员/小音箱',
                         // 舞种
                         '慢三' => '慢三',
                         '水兵' => '水兵/北京平四',
                         '吉特巴' => '吉特巴',
                         '舞厅伦巴' => '伦巴',
                         '布鲁斯' => '慢四/布鲁斯',
                         '快三' => '快三',
                         '中场' => '中场',
                         '拉四' => '拉四',
                         '中国交谊舞' => '交谊舞/慢三/水兵/北京平四/吉特巴/伦巴/慢四/布鲁斯/快三/拉四',
                         'salsa' => 'salsa/莎尔莎',
                         'bachata' => 'bachata',
                         '摩登' => '摩登/国际标准舞/国标舞/华尔兹',
                         '拉丁' => '拉丁/国际标准舞/国标舞',
                         'swing' => '摇摆舞/swing',
                         'tango' => 'tango',
                         '街舞' => '街舞/爵士',
                         '中国舞' => '中国舞/民族舞',
                         '肚皮舞' => '肚皮舞'
        );
        foreach ($tag_map as $key=>$value) {
            $pattern = '/'.str_replace('/', '|', $value).'/i';
            if (preg_match($pattern, $raw_text)) {
                $tags[] = $key;
            }
        }
        $tags[] = '西交dance';
        $tags[] = '精品收藏';
        return $tags;
    }


    /**
     * 将从兵马俑bbs文章页获取的发表时间转换为数据库储存形式（例：201803252035.001）
     */
    function convertTimeFormat($time) {
        $time_utf8 = mb_convert_encoding($time, "UTF-8", "GB18030");
        if (strpos($time_utf8, '年')) { // 第一种时间格式，例：2002年03月06日02:26:04 星期三
            // echo $time_utf8;
            $time_explode = explode(' ', $time_utf8);
            $time_f = str_replace(array('年', '月', '日', ':'), '', $time_explode[0]);
            return $time_f.'.000';
        } else { // 第二种时间格式，例：Tue Oct 25 10:04:49 2011
            $time_explode = explode(' ', $time_utf8);
            $time_year = $time_explode[4];
            $time_hms = $time_explode[3];
            $hms_explode = explode(':', $time_hms);
            $time_hour = $hms_explode[0];
            $time_min = $hms_explode[1];
            $time_sec = $hms_explode[2];
            $time_day = $time_explode[2];
            $time_day = (strlen($time_day) == 1 ? ("0".$time_day) : $time_day);
            switch ($time_explode[1])
            {
            case strpos($time_explode[1], 'Jan'):
                $time_mon = '01';
                break;
            case strpos($time_explode[1], 'Feb'):
                $time_mon = '02';
                break;
            case strpos($time_explode[1], 'Mar'):
                $time_mon = '03';
                break;
            case strpos($time_explode[1], 'Apr'):
                $time_mon = '04';
                break;
            case strpos($time_explode[1], 'May'):
                $time_mon = '05';
                break;
            case strpos($time_explode[1], 'Jun'):
                $time_mon = '06';
                break;
            case strpos($time_explode[1], 'Jul'):
                $time_mon = '07';
                break;
            case strpos($time_explode[1], 'Aug'):
                $time_mon = '08';
                break;
            case strpos($time_explode[1], 'Sep'):
                $time_mon = '09';
                break;
            case strpos($time_explode[1], 'Oct'):
                $time_mon = '10';
                break;
            case strpos($time_explode[1], 'Nov'):
                $time_mon = '11';
                break;
            case strpos($time_explode[1], 'Dec'):
                $time_mon = '12';
                break;
            default:
                $time_mon = '01';
            }
            return $time_year.$time_mon.$time_day.$time_hour.$time_min.$time_sec.".000";
        }
    }


   

}

/*
╔══╮╭══╮╭╮╭╮╭══╮╭══╮
║╭╮║║╭╮║║╰╮║║╭═╯║╭═╯
║║║║║╰╯║║　　║║║　　║╰═╮
║║║║║╭╮║║　　║║║　　║╭═╯
║╰╯║║║║║║╰╮║║╰═╮║╰═╮
╚══╯╰╯╰╯╰╯╰╯╰══╯╰══╯
╗╗╦╔╗╗╭╔═╯═╗　　╔╗　　╔╦╔══╗╔══╩═╗
╯╚╩╯╠╝║╔═══╗╔╗　║╔╗╔╠║　　║║╔═══╗
╯╔╩╗║║║╔═══╗║　　║　║╭╣╚══╯║　　╮═╯
║║╭╯╭╝╯╔═══╗║　　║　║║╠╔═╦╗║╔═╩╦╗
║║║║║║║║　　　║║　╔║　║║║　═╠　║　　　║　
╰╝╚╯╯╝╚╚═══╯╚╯╰╝╰╝╚╚╚═╩╝╯　╚═╯　

############################################################################################################
#                                                                                                          #
#        ii                                                                                                #
#      LDDLD                                                                                               #
#     EEWWKWE#                                                                                             #
#     EKWfff#W                                                                                             #
#    ,KWGfE#W:                                                                                             #
#    WWWffff##                                                                                             #
#    ;#WKffL#W         fLj                                                                                 #
#     W#ffjDWL      fEKDLfG,                                                                               #
#      Gtff#GDDE ti######, :                                                                               #
#      DtfLE,LDEL#########  D                                                                              #
#     DGjffD,,EW######K###   G                                                                             #
#    fLDDDD,,,;#######LfLE,  Gf                                                                            #
#    fLG,,,,,,;######GLiEW    G            ##, ##                                                          #
#   jLLG:,,,,,;jE#####GfWf  LLf            ##   ##f                                                        #
#   ,LLG:,,,,,,i;########GDLG.             ##   W##  D##### ### ###   :###i  t###G                         #
#    LGG,,,,,,,;#EG# jDDEGKD              t##   W## ##  f#E  ##, ##  W#  ## ##  ##                         #
#   DLGEi:,,,,,; :W  fLGEGG               ##i   ### ##  ##   ##  ##  ##  #E ##  #f                         #
#  ,LGD ,:,,,,,;    LLDGEEEE              ##    ## f##  ##   ##  ##  ##    f###i                           #
#  GGGD  :,,,,,i   Li  DEEEEt             ##   ##G D##  ##  W#i :##  ##   ,t##   ,                         #
#  LffD ,,:j,,,i,GD    GEEEE             ,##  ##    ## ###  ##  ,##  ### #  ##i #                          #
#   ffL ,::LGfGf       EEEE                          #  :#       W#    #i    f#                            #
#   :LL  ,:,j:,i       KEE:                                                                                #
#    iLG ,::,,,,      .EE:                                                                                 #
#     iL::,G,,,;  jEDDDE:                                                                                  #
#      :GGLiGD;; GGDDEEE                                                                                   #
#       i;GL:,,E DDDEEEE                                                                                   #
#      EEL:,:fED DDEEEEE                                                                                   #
#      DDEEEEDDD EDEEEEE                                                                                   #
#      DDDDDDDDK EDEEEEEt                  #  #  ##     ##   #          ##      #####D#####,       #i      #
#     DGDDDDDDD  KDEEEEEE                ,# #D## #      #########f      #,      # #  #f  ,#   ##########.  #
#     GGDDKKEDD  EDEEEEEE               t# :## #####   #f#      #       #        ,#  #   ##  t#            #
#    jGGDDGGGDE  KDEEEEEEK                ####### #   ## #######    ## ## #    #####D#####   #########G    #
#    DLDDDLGDG   EDEEEEEEEt              # #####.#E  ###            #  #. #E     #           #   ####      #
#    GLDEGLGDEG fEEEEEEEEEEt            ##    ## #   ##i ######,   #E  #  ##    ###f######  D#   W#f       #
#   .LGDELLGE LGEDEEEEEEEEEEKKKE       W#Et### ###    #           ##  ##  ##   ##D#  :#     #G########f    #
#   jLGEGLGDE  LEEEEEEEEEEEEKKKj        # #,#  ##    D# #######  ##   #   W#  ### #######  ,#    #f ##     #
#   LfGDEDDDE   EEEEEEEEEEEKKE:         # # ## #     #, #    #   t   :#       #f#  # # :E  #G    #         #
#   DDEDEDGDEK  ;EEEEEEEEKKKKf         #G#,######    # W######       ##        #f   ,#    ##    W#         #
#     WEEDDEEEf  EEEEEEEKEK            # # ### t#,  ## #i   ##     f##         # ######## #    ##.         #
#       KDDDEED   EEEEEED:                                                    f#                           #
#       KDDEEEf    DE,                                                                                     #
#       GDE EDi    LL.                                                                                     #
#      GDDi K,.    GL.                                                                                     #
#     LGDD  ;;     fL:                                                                                     #
#     tDDE  ,       fi                                                                                     #
#     EDDK          jf                                                                                     #
#    jDDDE           G                                                                                     #
#   .GDDD            i                                                                                     #
#   DGEDf            :                                                                                     #
#  ,,i,iD             i                                                                                    #
#                     D                                                                                    #
#                     D.                                                                                   #
#                     ;j                                                                                   #
#                                                                                                          #
############################################################################################################


*/
?>  
