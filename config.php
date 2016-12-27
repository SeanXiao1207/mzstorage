<?php

// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://dingxiaoyu.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed dingdayu.
// +----------------------------------------------------------------------
// | Author: dingdayu 614422099@qq.com
// +----------------------------------------------------------------------
// | DATE: 2016/12/23 14:24
// +----------------------------------------------------------------------
// | Explain: 请在这里填写说明
// +----------------------------------------------------------------------

$root = dirname(__FILE__);

return array(
    'DB' => [
        'host' => '127.0.0.1',      // 数据库ip地址
        'port' => 3306,             // 数据库开发端口
        'username' => 'root',       // 数据库用户名
        'passwd' => 'root',         // 数据库用户密码
        'dbname' => 'task'          // 数据库名称
    ],
    // 本地保存目录  默认，目录下的 down 文件夹
    'DOWN_FILE' => $root . DIRECTORY_SEPARATOR. 'down'
);