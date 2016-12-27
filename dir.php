<?php

$token = file_get_contents ('token');

include_once "vendor/autoload.php";
include_once "extend/mzstorage.php";
include_once 'extend/SaveToDB.php';

$mzstorage = new mzstorage();
$mzstorage->setUrl($token);

$dir = $mzstorage->get_dir_list();

if($dir['code'] == 200) {
    $SaveToDB = new SaveToDB();
    //var_dump($dir['value']);
    $SaveToDB->dir($dir['value']['dir']);
    echo "相册更新成功！\r\n";
    // TODO::循环下页

} else {
    echo $dir['message'] . PHP_EOL;
    echo "[ERROR] TOKEN 失效，请更新token！". PHP_EOL;
    echo "> 请获取携带token的url并复制到token中！";
}