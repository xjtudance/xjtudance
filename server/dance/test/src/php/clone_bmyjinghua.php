<?php
    ob_start();
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?>
<?php

//phpinfo();

require_once 'config.php';

set_time_limit(0); // 设置超时时间为无限

$stime = microtime(true);

ob_end_clean();
//ob_implicit_flush(1);

$bmybbs = get_bmybbs();
if ($bmybbs->__get('connectable')) {
	$bmybbs->getJinghua();
    echo "\nall articles are downloaded from bmybbs dance jinghuaqu!";
    unset($GLOBALS['db_dance']);
    unset($GLOBALS['article_count']);
    unset($GLOBALS['output_length']);

    // // iconv(): Detected an incomplete multibyte character in input string问题测试
    // $doc = $bmybbs->getArticle('http://bbs.xjtu.edu.cn/BMY/bbsanc?path=/groups/GROUP_8/dance/M1412307047/M1449670994&item=/M.1241662122.A');

    // // 某些文章获取内容为空测试，部分不存在的图片依然返回true和img_path问题测试
    // $doc = $bmybbs->getArticle('http://bbs.xjtu.edu.cn/BMY/bbsanc?path=/groups/GROUP_8/dance/M1412293824/M1412300339/M1449207155&item=/M.1226309251.A');

    // // PHP Notice:  iconv(): Detected an illegal character in input string问题测试
    // $doc = $bmybbs->getArticle('http://bbs.xjtu.edu.cn/BMY/bbsanc?path=/groups/GROUP_8/dance/M1412293824/M1412300339/M1412305141&item=/M.1255658475.A');

    // // 获取整理人测试
    // $bmybbs->getJinghua();

    // // 中文图片url下载测试
    // $doc = $bmybbs->getArticle('http://bbs.xjtu.edu.cn/BMY/bbsanc?path=/groups/GROUP_8/dance/M1412336511/M1412477975&item=/M.1174892640.A');

    // // 英文图片url下载测试，去除空内容测试，去除font标签测试
    // $doc = $bmybbs->getArticle('http://bbs.xjtu.edu.cn/BMY/bbsanc?path=/groups/GROUP_8/dance/M1412336511/M1451536892&item=/M.1460623116.A');
    
    // $doc = $bmybbs->getArticle('http://bbs.xjtu.edu.cn/BMY/bbsanc?path=/groups/GROUP_8/dance/M1412293824/M1412300339/M1449122852&item=/M.1150974759.A');

    // var_dump(json_encode($doc, JSON_UNESCAPED_UNICODE));
} else {
	echo 'error: can\'t connect to bmy bbs.';
}

?> 
