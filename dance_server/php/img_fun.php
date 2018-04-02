<?php  
/*******************************************************************************
图像处理函数
Version: 0.1 ($Rev: 3 $)
Website: https://github.com/xjtudance/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-11-05
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

/** 
* 下载远程图片保存到本地 
* 参数：文件url,保存文件目录,保存文件名称，使用的下载方式 
* 当保存文件名称为空时则使用远程文件原来的名称
* @param string $url 文件url
* @param string $save_dir 保存文件目录
* @param string $filename 保存文件名称
* @param integer $type 获取文件的方式
* @return string 图片文件相对保存路径
* @access public
* @note 该函数来源于网络：http://blog.csdn.net/blueinsect314/article/details/29861399
*/
function saveImage($url, $save_dir = '', $filename = '', $type = 1) {
	if (check_remote_file_exists($url)) {
		if(trim($url) == '') { // url为空
			return array('errMsg' => "URL_NOT_SET");
		}
		
		// 获取远程文件
		if($type) {
			$ch = curl_init();		
			$timeout = 30;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$img = curl_exec($ch);
			curl_close($ch);
			if (strstr($img, '<!DOCTYPE html>')) { // @todo: 此方法不好，可能有误，需要改进
				return false;
			}
		} else {
			ob_start();
			if (!readfile($url)) {
				return false;
			};
			$img = ob_get_contents(); // @todo: 此方法存在不完善之处，有可能依然在无法获得数据的情况下返回true
			ob_end_clean();
		}
		
		// 创建目录，保存文件
		if(trim($save_dir) == '') { // 保存路径为空，默认保存路径以年/月/日为目录
			$save_dir = 'data/images/xjtudance/bmyjinghua/'.date('Y')."/".date('m')."/".date('d')."/";
		}	
		if ($_SERVER['DOCUMENT_ROOT']) {	
			$save_dir_abs = $_SERVER['DOCUMENT_ROOT']."/".$save_dir;
		} else {
			$save_dir_abs = '/data/release/dance/'.$save_dir;
		}
		
		if(trim($filename) == '') { // 保存文件名为空
			$ext = strrchr($url, '.');
	//        if ($ext != '.jpg' && $ext != '.png' && $ext != '.gif') { // 不是图片文件
	//            return array('file_name' => '', 'save_path' => '', 'errMsg' => "FILE_NOT_IMAGE");
	//        }
			$ext = ($ext == '' || $ext == '.') ? '.jpg' : $ext; // 后缀为空默认保存为.jpg
			$sec = explode(' ', microtime()); // get t value
			$micro = explode('.', $sec[0]);
			date_default_timezone_set("Asia/Shanghai");
			$filename = time().substr($micro[1], 0, 3).$ext; // 以时间命名
		}
		if(0 !== strrpos($save_dir_abs, '/')) { // 在保存路径前加"/"
			// $save_dir_abs .= '/';
		}
		// 创建保存目录
		if(!is_dir($save_dir_abs) && !mkdir($save_dir_abs, 0777, true)) {
			return array('errMsg' => "PATH_NOT_EXIST");
		}
		// 保存文件
		file_put_contents($save_dir_abs.$filename, $img);
		unset($img, $url);
		return $save_dir.$filename;
	} else {
		return false;
	}
}

/** 
* 检查远程文件是否存在
* @reference http://blog.sina.com.cn/s/blog_5f66526e0100pwzn.html, 2018.4
*/
function check_remote_file_exists($url) {
    $curl = curl_init($url);
    // 不取回数据
    curl_setopt($curl, CURLOPT_NOBODY, true);
    // 发送请求
    $result = curl_exec($curl);
    $found = false;
    // 如果请求没有发送失败
    if ($result !== false) {
        // 再检查http响应码是否为200
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		return $statusCode;
        if ($statusCode == 200) {
            $found = true;   
        }
    }
    curl_close($curl);			
}
	
?>  
