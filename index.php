<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/5/29
 * Time: 下午3:32
 */
ini_set('date.timezone','Asia/Shanghai');

Session_start();

header("Content-type:text/html;charset=utf-8");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: https://0e.io");


/*mysql远程链接*/
define('DSN',"mysql:host=x.x.x.x;dbname=xxxx");
define('NAME',"xxxx");
define('PASS',"xxxx");
define('PREV',"xxxx");

require('pdo.mysql.class.php');
require('model.php');

$model = new model();

$_v = $_REQUEST["ref"];

if(isset($_v) && !empty($_v)){
    $_url = $model->read_by_keyword(urlencode($_v));
    $_url = json_decode(json_encode($_url),TRUE);
    header("location:".urldecode(urldecode($_url["url"])));
    exit;
}

$str = $_REQUEST["str"];

if(isset($str) && !empty($str)) {

    $_a = explode("0e.io",strtolower(urldecode($str)));
    if(sizeof($_a)>1){
        echo json_encode(array(
            "code" => -2,
            "msg" => "包含非法字符",
            "data" => array(
                "str" => "包含非法字符"
            )
        ));
        exit;
    }

    $_keyword = $model->write_for_url(urlencode($str));

    echo json_encode(array(
        "code" => 0,
        "msg" => "",
        "data" => array(
            "str" => "https://0e.io/" . $_keyword
        )
    ));
    exit;
}

echo json_encode(array(
    "code" => -1,
    "msg" => "非法访问"
));