<?php
function tokenVerify($token, $recaptcha_key, $ip)
{
    /*验证g-recaptcha的token
    g-recaptcha的api文档参考https://developers.google.cn/recaptcha/docs/verify?hl=en*/
    $recaptcha_host = "https://www.recaptcha.net/recaptcha/api/siteverify";
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $recaptcha_host,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => http_build_query(array('secret' => $recaptcha_key, 'response' => $token, "remoteip" => $ip))
    ));
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data, true);
}

function getIp(): string
{
    $unknown = 'unknown';
    $ip = null;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    if (false !== strpos($ip, ',')) {
        $array = explode(',', $ip);
        $ip = reset($array);
    }
    return $ip;
}