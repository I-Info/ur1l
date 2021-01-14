<?php
header('Content-Type:application/json; charset=utf-8;',true,200);
include_once "config.php";//引入一些服务器配置变量
include_once "functions.php";//定义的一些函数
/** @var $redis_host */
/** @var $redis_port */

die(json_encode(tokenVerify(trim($_POST["token"]))));
$json_response = array("status","code");
$redis = new Redis();
$redis->connect($redis_host,$redis_port);
try {
    $json_response["code"] = $redis->ping('ping');
} catch (RedisException $e) {
    $json_response["status"] = 500;
    $json_response["code"] = $e;
}
echo json_encode($json_response);