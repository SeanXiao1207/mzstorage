# mzstorage
魅族相册备份脚本

### 使用方法

0、先准备PHP环境和安装MySQL数据库，推荐直接安装一个StudyPHP集成环境

1、 [配置数据库](#创建数据表 "配置数据库") 

2、 [修改配置](#修改配置 "修改配置") 

3、 [获取token]

登录 https://cloud.flyme.cn/browser/main.jsp 点击 `云相册`

> 打开浏览器调试工具，在`Console`中输入下面的代码：

```
function getCookie(name){var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");if(arr=document.cookie.match(reg)){return unescape(arr[2])}else{return null}}getCookie("_utoken");
```

下面会出现一段乱码，将引号里面的字符串复制出来替换到`token`即可。
更新token时，请先刷新页面，再执行上述操作获取新的token。

> 如果在拉取过程中出现失效，请刷新页面，然后重新按照上面的方法更新 `token` 即可

### 特别注意

1、目前由于`token`失效时间较快，且没实现通过`cookie`自动更新`token`，只能在提示失败后，再按照`使用方法`手动更新`token`

2、下载脚本，魅族相册存储在`阿里OSS（对象存储）`上，目前web上面是通过`STS授权`访问，所以，同样受制于token的失效，幸运的是在获得STS授权后，一个小时才会失效，也就是说，如果你的相册可以在一个小时内下载完毕，那么就不需要手动更新`token`。

3、 大部分错误都有对应的错误提示，并有处理指引，你可以根据提示，进行相关的操作。

### 命令提示

#### 拉取相册：
```
php dir.php
```

#### 拉取相册图片
1、 查看相册列表
```
php album.php
```
2、拉取对应的相册
```
php album.php 277

```
3、更新最近图片
```
php update.php
```


#### 下载图片到本地
```
php down.php
```

### 创建数据表

1、手动创建数据库

> 注意创建数据库时填写的数据库名称，修改配置时需要

2、在数据库内执行下面的`SQL`

```
# 创建图片记录表
CREATE TABLE `dy_mz_album` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `album_id` int(10) unsigned DEFAULT '0' COMMENT '照片id',
  `dirId` int(10) unsigned DEFAULT '0' COMMENT '相册id',
  `dirName` varchar(64) DEFAULT NULL COMMENT '相册名称',
  `fileName` varchar(255) DEFAULT NULL COMMENT '照片名称',
  `groupDirId` varchar(64) DEFAULT NULL,
  `groupId` varchar(64) DEFAULT NULL,
  `height` int(10) unsigned DEFAULT '0' COMMENT '照片高度',
  `width` int(10) unsigned DEFAULT '0' COMMENT '照片宽度',
  `isVideo` tinyint(3) unsigned DEFAULT NULL COMMENT '是否视频',
  `md5` varchar(64) DEFAULT NULL COMMENT '文件md5',
  `createTime` bigint(13) unsigned DEFAULT NULL COMMENT '创建时间',
  `modifyTime` bigint(13) unsigned DEFAULT NULL COMMENT '修改时间',
  `remainTrashTime` bigint(13) unsigned DEFAULT NULL,
  `shootTime` bigint(13) unsigned DEFAULT NULL,
  `size` bigint(20) DEFAULT NULL,
  `sqlNow` bigint(13) unsigned DEFAULT NULL COMMENT '最后查询时间',
  `tags` varchar(255) DEFAULT NULL COMMENT '标签',
  `thumb256` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `thumb1024` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `uid` varchar(10) DEFAULT NULL COMMENT 'uid',
  `userId` int(10) unsigned DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL COMMENT '图片路径',
  `local` varchar(255) DEFAULT '' COMMENT '本地路径（代表是否已下载）',
  `status` int(2) unsigned DEFAULT NULL COMMENT '状态',
  `create_time` datetime DEFAULT NULL COMMENT '上传时间',
  `update_time` datetime DEFAULT NULL COMMENT '采集时间',
  `is_delted` tinyint(1) unsigned DEFAULT '0' COMMENT '是否被删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3500 DEFAULT CHARSET=utf8mb4;

# 创建相册表
CREATE TABLE `dy_mz_dir` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `dir_id` int(10) unsigned DEFAULT '0' COMMENT '相册id',
  `dirName` varchar(64) DEFAULT NULL COMMENT '相册名称',
  `fileNum` int(10) unsigned DEFAULT '0' COMMENT '相册数量',
  `icon` varchar(255) DEFAULT '' COMMENT '缩略图',
  `sqlNow` bigint(13) unsigned DEFAULT '0' COMMENT '最后一次查询时间',
  `modifyTime` bigint(13) unsigned DEFAULT '0' COMMENT '最后修改时间',
  `createTime` bigint(13) unsigned DEFAULT '0' COMMENT '相册创建时间',
  `userId` int(10) unsigned DEFAULT '0' COMMENT '用户id',
  `totalSize` bigint(20) DEFAULT NULL COMMENT '相册总大小',
  `status` tinyint(2) unsigned DEFAULT NULL COMMENT '相册状态',
  `create_time` datetime DEFAULT NULL COMMENT '上传时间',
  `update_time` datetime DEFAULT NULL COMMENT '采集时间',
  `is_delted` tinyint(1) unsigned DEFAULT '0' COMMENT '是否被删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COMMENT='魅族相册表';
```

### 修改配置

修改`config.php`文件（参考文件内相关注释），数据库名称，需和上面新建数据库名称对应！

### 问题处理

1、如果重复执行出现OSS.php signUrl的问题，需要清空.alioss_sigin文件内容，然后再次执行命令；

2、如果出现大量关闭流的问题，可以中断执行，然后再次重复执行即可（这里一般是下载的时候token过期了）；

欢迎加我QQ沟通：516854140
