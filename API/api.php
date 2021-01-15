<?php
header('Content-Type:application/json; charset=utf-8;', true, 200);
include_once "config.php";//引入一些服务器配置变量
include_once "functions.php";//定义的一些函数
$json_response = array("status" => null, "code" => null);
$ip = getIp();
/** @var $redis_host */
/** @var $redis_port */
/** @var $recaptcha_key */
/*验证请求的合法性*/
$verify = tokenVerify(trim($_POST['token']), $recaptcha_key, $ip);
if (!$verify['success'] || $verify['score'] < 0.3) {
    $json_response['status'] = 400;
    $json_response['code'] = 'Bad Request';
    die(json_encode($json_response));
}
/*url验证*/
$url = htmlspecialchars(trim($_POST["URL"]));
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
    $redis->ping();//验证redis服务的连接状况
} catch (RedisException $e) {
    $json_response["status"] = 500;
    $json_response["code"] = "Internal server error";
    die (json_encode($json_response));
}
//限制IP请求数
$key = 'ip:'.$ip;
if($redis->exists($key)) {
    $request_num=$redis->get($key);
    if ($request_num >= 5) {
        if ($redis->ttl($key) < 15)
            $redis->expire($key,15);
        $json_response['status'] = 403;
        $json_response['code'] = "Too many requests!";
        die (json_encode($json_response));
    }
    $redis->incr($key);
} else {
    $redis->set($key,1);
    $redis->expire($key,60);
}


$json_response['status'] = 200;
$json_response["code"] = "OK";
$redis->close();
echo json_encode($json_response);
