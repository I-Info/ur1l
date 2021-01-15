<?php
header('Content-Type:application/json; charset=utf-8;', true, 200);
include_once "config.php";//引入一些服务器配置变量
include_once "functions.php";//定义的一些函数
$json_response = array("status" => null, "code" => null);
/** @var $redis_host */
/** @var $redis_port */
/** @var $recaptcha_key */
/*验证请求的合法性*/
$verify = tokenVerify(trim($_POST['token']), $recaptcha_key);
if (!$verify['success'] && $verify['score'] < 0.3) {
    $json_response['status'] = 400;
    $json_response['code'] = 'Bad Request';
    die(json_encode($json_response));
}
/*url验证*/
$url = urlencode(trim($_POST["URL"]));
$patten = '/^http[s]?:\/\/' .
    '(([0-9]{1,3}\.){3}[0-9]{1,3}|' .
    '([0-9a-z_!~*\'()-]+\.)*' .
    '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.' .
    '[a-z]{2,6})(:[0-9]{1,4})?((\/\?)|' .
    '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/';
if (!preg_match($patten, $url)) {
    $json_response['status'] = 400;
    $json_response['code'] = 'Bad Request';
    die(json_encode($json_response));
}

$redis = new Redis();
$redis->connect($redis_host, $redis_port);
try {
    $json_response["code"] = $redis->ping('ping');
} catch (RedisException $e) {
    $json_response["status"] = 500;
    $json_response["code"] = $e;
}
echo json_encode($json_response);