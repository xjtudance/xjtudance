<?php
/*******************************************************************************
兵马俑BBS相关函数
Version: 0.1 ($Rev: 4 $)
Website: https://github.com/xjtudance/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-09-29
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
		
	function __construct() {
		$this->connectBmy();
	}
	
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
			$collection_users = $db->users;
			$bmysession = $collection_users->findOne(array('_id' => $user_id), array('bmy' => true));
			if ($bmysession['bmy']['sessionurl'] && time() - $bmysession['bmy']['sessiontime'] < 2592000) {
				return $bmysession['bmy']['sessionurl'];
			} else {
				// 获取当前时间
				$sec = explode(' ', microtime());
				$micro = explode('.', $sec[0]);
				$time = $sec[1].substr($micro[1], 0, 3);
				
				// 通过bmy的proxy_url获取sessionurl
				$proxy_url = "http://bbs.xjtu.edu.cn/BMY/bbslogin?ipmask=8&t={$time}&id={$bmy_id}&pw={$bmy_password}";
				$result = file_get_html($proxy_url);
					
				if(strstr($result, iconv("UTF-8", "GB2312//IGNORE", "错误! 密码错误!")) || strstr($result, iconv("UTF-8", "GB2312//IGNORE", "错误! 错误的使用者帐号!"))) {
					return array('msg' => '错误! 账号或密码错误!');
				} else {
					if(strstr($result, iconv("UTF-8", "GB2312//IGNORE", "错误! 两次登录间隔过密!!"))) {
						return array('msg' => '错误! 两次登录间隔过密!');
					} else { // 成功登录
						$sessionurl_t = myfind($result, "url=/", "/", 0);
						$sessionurl = $sessionurl_t[0];
						$collection_users->update(array('_id' => $user_id), array('$set' => 
							array('bmy.id' => $bmy_id, 'bmy.password' => $bmy_password, 
							'bmy.sessionurl' => $sessionurl, 'bmy.sessiontime' => time())), array('multiple' => true));
						return $sessionurl;			
					}
				}
			}
		}

	}
	
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
			
			if(strstr($result, iconv("UTF-8", "GB2312//IGNORE", "错误! 两次发文间隔过密, 请休息几秒后再试!"))) {
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
	
	// 类属性get和set函数
	// -----------------------------------------------------------------------------
 	function getConnectable() {
		return $this->connectable;
	}	
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
			[0;36m这是dance的第[m[1;36m[4m".($db->diaries->count() + 1)."[m[m[0;36m篇文章[m\n
			[0;36m我与[m[1;32m[4m".$db->users->count()."[m[m[0;36m位舞友在dance切磋[m
			[0;36m我的称号是[m[0;31m[4m".$level."[m[m
			[1;34m********************************************************************************[m";
	return $watermark;
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
