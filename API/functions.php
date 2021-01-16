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

/*hash函数，随意。仅作参考，性能，碰撞率没有保证。base随意*/
function getHash($str, $base): string
{
    $len = strlen($base);
    $output = substr($base, crc32($str . "Bilibili") % $len, 1);#2
    $output .= substr($base, (crc32($str) * 7) % $len, 1);#3
    $output .= substr($base, crc32(strrev($str)) % $len, 1);#4
    $output .= substr($base, (crc32(strrev($str)) * 91) % $len, 1);#5
    $output .= substr($base, crc32(strrev($str . "I_Info")) % $len, 1);#6
    return $output;
}