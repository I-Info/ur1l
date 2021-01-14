<?php
function post($url,$data){
    /*搬运自runoob.com上的PHP-cURL笔记*/
    $data  = json_encode($data);
    $headerArray =array("Content-type:application/json;charset='utf-8'","Accept:application/json");
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl,CURLOPT_HTTPHEADER,$headerArray);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return json_decode($output,true);
}
function tokenVerify($token) {
    /*验证g-recaptcha的token
    g-recaptcha的api文档参考https://developers.google.cn/recaptcha/docs/verify?hl=en*/
    /** @var STRING $recaptcha_key */
    /** @var STRING $recaptcha_host */
    $data = array("secret"=>$recaptcha_key,"response"=>$token,"remoteip"=>$_SERVER["REMOTE_ADDR"]);
    return post($recaptcha_host,$data);
}