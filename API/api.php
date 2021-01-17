<?php
header('Content-Type:application/json; charset=utf-8;', true, 200);
include_once "config.php";//引入一些服务器配置变量
include_once "functions.php";//定义的一些函数
$json_response = array("status" => null, "code" => null);
$ip = getIp();
/** @var $redis_host */
/** @var $redis_port */
/** @var $recaptcha_key */
/** @var $min_score */
/*验证请求的合法性*/
$verify = tokenVerify(trim($_POST['token']), $recaptcha_key, $ip);
if (!$verify['success'] || $verify['score'] < $min_score) {
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
if (strlen($url) > 110 || !preg_match($patten, $url)) {
    $json_response['status'] = 400;
    $json_response['code'] = 'Bad Request';
    die(json_encode($json_response));
}
//新建redis连接
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
$key = 'ip:' . $ip;
if ($redis->exists($key)) {
    $request_num = $redis->get($key);
    if ($request_num >= 3) {
        if ($redis->ttl($key) < 15)
            $redis->expire($key, 15);
        $json_response['status'] = 403;
        $json_response['code'] = "Too many requests!";
        die (json_encode($json_response));
    }
    $redis->incr($key);
} else {
    $redis->set($key, 1);
    $redis->expire($key, 60);
}

/** @var $base */
//生成hash值
$hash = getHash($url, $base);

//存储。带碰撞，重复检测。
$dic = "0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";//防碰撞位字符定义,随意
$index = 0;
$sIndex = substr($dic, $index, 1);
$flag = false;//重复记录
while (!$redis->hSetNx("urls:" . $hash . $sIndex, "URL", $url) && !($flag = $redis->hGet("urls:" . $hash . $sIndex, "URL") == $url)) {
    $sIndex = substr($dic, ++$index, 1);
}
if (!$flag) {
    $redis->expire("urls:" . $hash . $index, 86400);//设置销毁时间1天
}

if ($index != 0) {
    $hash .= $sIndex;
}

$json_response['status'] = 200;
$json_response["code"] = $hash;
$redis->close();
echo json_encode($json_response);
