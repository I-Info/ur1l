<?php
function tokenVerify($token,$recaptcha_key)
{
    /*验证g-recaptcha的token
    g-recaptcha的api文档参考https://developers.google.cn/recaptcha/docs/verify?hl=en*/
    $recaptcha_host = "https://www.recaptcha.net/recaptcha/api/siteverify";
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $recaptcha_host,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => http_build_query(array('secret' => $recaptcha_key, 'response' => $token, "remoteip" => $_SERVER["REMOTE_ADDR"]))
    ));
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data,true);
}