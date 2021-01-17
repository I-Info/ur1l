<?php
include_once "API/config.php";
/** @var $redis_host */
/** @var $redis_port */
$code = trim($_GET['code']);
$redis = new Redis();
$redis->connect($redis_host, $redis_port);
try {
    $redis->ping();//验证redis服务的连接状况
} catch (RedisException $e) {
    die("Internal Server Error.");
}
if (strlen($code) == 5)
    $code .= "0";
elseif (strlen($code) > 6 || strlen($code) < 5)
    die("Bad Request");

//限制IP请求数
include "API/functions.php";
$key = 'ip:' . getIp();
if ($redis->exists($key)) {
    $request_num = $redis->get($key);
    if ($request_num >= 3) {
        if ($redis->ttl($key) < 15)
            $redis->expire($key, 15);
        die ("Too many requests!");
    }
    $redis->incr($key);
} else {
    $redis->set($key, 1);
    $redis->expire($key, 40);
}

$url = null;
if ($url = $redis->hGet("urls:" . $code, "URL")) {
    $redis->hIncrBy('urls:' . $code, "clicks", 1);
    header("location:$url", true, 301);
} else {
    die("Bad Request");
}
$redis->close();