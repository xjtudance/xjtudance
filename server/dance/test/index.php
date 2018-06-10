<?php
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, minimum-scale=1.0, user-scalable=yes" />
	<meta name="viewport" content="height=device-height, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=yes">
	 <!–[if lt IE 9]><script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script><![endif]–>

    <title>欢迎来到dance！</title>

    <script type="text/javascript">
    $(document).ready(function(){
        $(this).scrollTop(0);
    });
    </script>

    <style type="text/css">

    .align-center{
    margin: 0 auto; /* 居中 这个是必须的，，其它的属性非必须 */ 
    background: #FFFFFFFF; /* 背景色 */ 
    text-align: left; /* 文字等内容 */
    word-break: break-all;
    overflow: auto;
}

/* 浏览器宽度小于800px */
    @media screen and (max-device-width: 800px) {
        .align-center{
            width: 100%; /* 给个宽度 顶到浏览器的两边就看不出居中效果了 */ 
        }

        .diary-block{
        witdh: 100%;
    margin-bottom: 10%;
 }

.title{
        width: 90%;
            margin-left: 5%;
    text-align: center;
            font-size: 1.3rem;
    color: #8B008B;
        text-shadow: 0.05rem 0.05rem 0.05rem #787878;
    font-family: 'Open Sans','Helvetica Neue',Arial,'Hiragino Sans GB','Microsoft YaHei','WenQuanYi Micro Hei',sans-serif;
 }

.content-block{
        width: 90%;
    margin-left: 5%;
        color: #404040;
    font-weight: 500;
        font-size: 1.1rem;
        line-height: 2.08rem;
        font-family: "Lucida Grande", "Lucida Sans Unicode", Helvetica, Arial, Verdana, sans-serif;
        letter-spacing: 0.05rem;
            text-align: justify;
 }

        .img-block{
            width: 100%;
            color: #404040;
            font-weight: 500;
            font-size: 1.5rem;
            line-height: 2.7rem;
            font-family: "Lucida Grande", "Lucida Sans Unicode", Helvetica, Arial, Verdana, sans-serif;
            letter-spacing: 0.2rem;
            text-align: center;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }


.author-block{
        width: 90%;
margin-top: 5%;
    margin-left: 5%;
        color: #404040;
        text-align: right;
        font-weight: 500;
        font-size: 0.9rem;
        line-height: 1.3rem;
        font-family: "Lucida Grande", "Lucida Sans Unicode", Helvetica, Arial, Verdana, sans-serif;
        letter-spacing: 0.1rem;
 }

.time-block{
        width: 90%;
    margin-left: 5%;
        color: #404040;
        text-align: right;
        font-weight: 500;
        font-size: 0.9rem;
        line-height: 1.3rem;
        font-family: "Lucida Grande", "Lucida Sans Unicode", Helvetica, Arial, Verdana, sans-serif;
        letter-spacing: 0.1rem;
 }

        .img{
            width: auto;
            height: auto;
            max-width: 100%;
            max-width: 100%;
        }


    }

/* 浏览器宽度大于800px */
    @media screen and (min-device-width: 800px) {
        .align-center{
            width: 850px; /* 给个宽度 顶到浏览器的两边就看不出居中效果了 */ 
        }

        .diary-block{
        witdh: 100%;
    margin-bottom: 5%;
 }

.title{
        width: 90%;
    margin-left: 5%;
    text-align: center;
    color: #8B008B;
        text-shadow: 0.05rem 0.05rem 0.05rem #787878;
    font-family: 'Open Sans','Helvetica Neue',Arial,'Hiragino Sans GB','Microsoft YaHei','WenQuanYi Micro Hei',sans-serif;
 }

.content-block{
        width: 90%;
    margin-left: 5%;
        color: #404040;
    font-weight: 500;
        font-size: 1.2rem;
        line-height: 1.7rem;
        font-family: "Lucida Grande", "Lucida Sans Unicode", Helvetica, Arial, Verdana, sans-serif;
        letter-spacing: 0.1rem;
 }

        .img-block{
            width: 100%;
            color: #404040;
            font-weight: 500;
            font-size: 2.4rem;
            line-height: 3.4rem;
            font-family: "Lucida Grande", "Lucida Sans Unicode", Helvetica, Arial, Verdana, sans-serif;
            letter-spacing: 0.2rem;
            text-align: center;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }


.author-block{
        width: 90%;
    margin-left: 5%;
        color: #404040;
        text-align: right;
        font-weight: 500;
        font-size: 1.2rem;
        line-height: 1.7rem;
        font-family: "Lucida Grande", "Lucida Sans Unicode", Helvetica, Arial, Verdana, sans-serif;
        letter-spacing: 0.1rem;
 }

.time-block{
        width: 90%;
    margin-left: 5%;
        color: #404040;
        text-align: right;
        font-weight: 500;
        font-size: 1.2rem;
        line-height: 1.7rem;
        font-family: "Lucida Grande", "Lucida Sans Unicode", Helvetica, Arial, Verdana, sans-serif;
        letter-spacing: 0.1rem;
 }

        .img{
            width: auto;
            height: auto;
            max-width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }

    }


</style> 
</head>
<body>

<div class="align-center">
    <?php
        $server_url = 'https://xjtudance.top/';
    include_once($_SERVER['DOCUMENT_ROOT']."/xjtudance/test/php/config.php");
$url = $server_url.'xjtudance/test/php/wx_listData.php';
    $data_request = array('collection_name' => 'jinghua',
                          'skip' => rand(0, 4233),
                          'limit' => 1,
                 'list_order' => 'update_time',
                          'query' => [],
                 'getValues' => '',
                 'extraData' => []);
list($httpCode, $list_jinghua) = http_post_json($url, json_encode($data_request, JSON_UNESCAPED_UNICODE));
$list_jinghua = json_decode($list_jinghua, true);

foreach ($list_jinghua as $diary) {

    echo '<div class="diary-block">';

    echo '<h2 class="title">'.$diary['title'].'<h2/>';

    foreach ($diary['content'] as $content) {
        if ($content['metatype'] == 'text') {
            echo '<div class="content-block">';

            $content['body'] = str_replace("\n", "<br/>", $content['body']);
            $content['body'] = str_replace("<br/><br/>", "<br/>", $content['body']);
            $content['body'] = str_replace("<br/><br/>", "<br/>", $content['body']);
            echo str_replace("<br/><br/>", "<br/>", $content['body']);
         
            echo '</div>';
        } elseif ($content['metatype'] == 'img') {
            echo '<div class="img-block">';

            $img_src = $server_url.$content['body'];

            echo '<img src="'.$img_src.'" alt="你好我是alt属性，你的网太慢了，图都加载不出来了，羞羞~" class="img"/>';

            echo '</div>';
        }
    }

    echo '<div class="author-block">';
    echo '作者：'.($diary['author'] == '' ? '佚名' : $diary['author']);
    echo '</div>';

    echo '<div class="time-block">';
    $create_time = substr($diary['create_time'], 0, 4)."年".
                 substr($diary['create_time'], 4, 2)."月".
                 substr($diary['create_time'], 6, 2)."日";
    echo $create_time;
    echo '</div>';


    echo '</div>';
}

    ?>
</div>

    </body>
</html>
