<?php
$ip = getenv('REMOTE_ADDR');
$ua = $_SERVER['HTTP_USER_AGENT'];
if($ua == 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)'){
    $pc = true;
}elseif(preg_match('/Darwin/',$ua)){
    $ios = true;
    $ios_ua = $ua;
}elseif($ua == '' || preg_match('/Dalvik\//',$ua)){
    $android = true;
    $android_ua = $ua;
}elseif($ua == 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_4 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12H143 Safari/600.1.4'){
    die();
}elseif($ua == 'Java/1.6.0_21'){
    die();
}else{
    $unknown = true;
    $unknown_ua = $ua;
}
$user_file = base64_decode($_GET['_T']).'.json';
$img_url = base64_decode($_GET['_U']);

touch($user_file);//如果没有此文件，则创建
$user = file_get_contents($user_file);
$user = json_decode($user,true);

$i = 0;
$do = false;
foreach($user['result'] as $value){
    if($user['result'][$i]['ip'] == $ip){
        if($user['result'][$i]['pc'] != true){
            $user['result'][$i]['pc'] = $pc;
        }
        if($user['result'][$i]['android'] != true){
            $user['result'][$i]['android'] = $android;
            $user['result'][$i]['android_ua'] = $android_ua;
        }
        if($user['result'][$i]['ios'] != true){
            $user['result'][$i]['ios'] = $ios;
            $user['result'][$i]['ios_ua'] = $ios_ua;
        }
        if($user['result'][$i]['unknown'] != true){
            $user['result'][$i]['unknown'] = $unknown;
            $user['result'][$i]['unknown_ua'] = $unknown_ua;
        }
        $user['result'][$i]['ts'] = time();
        $do = true;
    }
    $i++;
}
if($do != true){
    $i = count($user['result']);
    $user['result'][$i]['pc'] = $pc;
    $user['result'][$i]['android'] = $android;
    $user['result'][$i]['android_ua'] = $android_ua;
    $user['result'][$i]['ios'] = $ios;
    $user['result'][$i]['unknown'] = $unknown;
    $user['result'][$i]['unknown_ua'] = $unknown_ua;
    $user['result'][$i]['ios_ua'] = $ios_ua;
    $user['result'][$i]['ip'] = $ip;
    $user['result'][$i]['ua'] = $ua;
    $user['result'][$i]['ts'] = time();
}
$user = json_encode($user);

$fp = fopen($user_file,'w');
flock($fp,LOCK_EX);
fputs($fp,$user);
flock($fp,LOCK_UN);
fclose($fp);

header('location:'.$img_url);
