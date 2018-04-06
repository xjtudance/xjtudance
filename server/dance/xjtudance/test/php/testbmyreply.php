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

include('config.php');

// 同步到兵马俑BBS
$sec = explode(' ', microtime()); // get t value
$micro = explode('.', $sec[0]);
date_default_timezone_set("Asia/Shanghai");
$time = date('YmdHis').".".substr($micro[1], 0, 3);
$timeBmy = $sec[1].substr($micro[1], 0, 3);

$bmy_id = 'jajupmochi';
$bmy_password = 'byy191710 ';

$proxy_url = "http://bbs.xjtu.edu.cn/BMY/bbslogin?ipmask=8&t={$timeBmy}&id={$bmy_id}&pw={$bmy_password}";
$result = file_get_html($proxy_url);
$sessionurl_t = myfind($result, "url=/", "/", 0); // 通过bmy的proxy_url获取sessionurl
$_SESSION["sessionurl"] = $sessionurl_t[0];

$url = 'M.1505444690.A';
$urlt = rtrim($url, '.A');
echo $urlt;

/* $bmy_title = 'Re: 回复测试2';
$bmy_content = 'rt';
$postdata = "title=".urlencode(iconv("UTF-8", "GB18030//IGNORE", $bmy_title))."&text=".urlencode(iconv("UTF-8", "GB18030//IGNORE", $bmy_content));
	$url = "http://bbs.xjtu.edu.cn/".$_SESSION["sessionurl"]."/bbssnd?board=dance&th=-1&ref=M.1505544745&rid=-1";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	curl_close($ch);

$proxy_url = "http://bbs.xjtu.edu.cn/".$_SESSION["sessionurl"]."/pst?B=dance&F=M.1503842068&num=23391";
$result = file_get_html($proxy_url);
echo iconv("GB18030//IGNORE", "UTF-8", $result)."\n\n2";

$proxy_url = "http://bbs.xjtu.edu.cn/".$_SESSION["sessionurl"]."/pst?B=dance&F=M.1503842068";
$result = file_get_html($proxy_url);
echo "2\n".iconv("GB18030//IGNORE", "UTF-8", $result); */

/* $postdata = "title=".urlencode(iconv("UTF-8", "GB18030//IGNORE", $bmy_title))."&text=".urlencode(iconv("UTF-8", "GB18030//IGNORE", $bmy_content));
$url = "http://bbs.xjtu.edu.cn/".$_SESSION["sessionurl"]."/bbssnd?board=dance&th=-1&signature=1";
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_URL, $url);    
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$result = curl_exec($ch);
curl_close($ch);

$bmyurl = "";  // 获取该文的bmyurl
$proxy_url = "http://bbs.xjtu.edu.cn/".$_SESSION["sessionurl"]."/home?B=dance&S=";
$result = file_get_html($proxy_url);
$user_list = $result->find('td[class=tduser] a');
$article_list = $result->find('.tdborder a');
for ($offset = 19; $offset >= 0; $offset--) {
	if ($user_list[$offset]->innertext == $bmy_id) { // 寻找该作者最近发布的文章
		$article_f = myfind(substr($article_list[$offset]->href, 3), "?B=dance&F=", "&N=", 0);
		$bmyurl = $article_f[0];
		break;
	}
} */


?>