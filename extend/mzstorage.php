<?php

// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://dingxiaoyu.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed dingdayu.
// +----------------------------------------------------------------------
// | Author: dingdayu 614422099@qq.com
// +----------------------------------------------------------------------
// | DATE: 2016/12/18 01:27
// +----------------------------------------------------------------------
// | Explain: 请在这里填写说明
// +----------------------------------------------------------------------

class mzstorage
{
    public $token;

    public function setUrl($url = '')
    {
        if (empty($url)) {
            $this->tipUpdateToken('Token 为空！');
            //throw new Exception("token url not empty！");
        }

        return $this->getToken(trim($url));
    }

    /**
     * 从token文件中获取token内容.
     *
     * @param string $url [description]
     *
     * @return string token内容
     */
    public function getToken($url = '')
    {
        if (empty($url)) {
            return null;
        }
        if (strpos($url, 'http') === false) {
            $this->token = $url;

            return $this->token;
        }
        $urlArr = parse_url($url);
        $query = $this->convertUrlQuery($urlArr['query']);
        $this->token = $query['token'];

        return $this->token;
    }

    /**
     * 提示用户更新token.
     *
     * @param string $msg 附加提示语
     */
    public function tipUpdateToken($msg = '')
    {
        if (!empty($msg)) {
            echo '[MSG] '.$msg.PHP_EOL;
        }
        echo '[ERROR] TOKEN 失效，请更新token！'.PHP_EOL;
        echo "\t > 刷新flyme云服务的相册页面，复制获取token的js方法到Console窗口下获取token，并更新到token文件中。".PHP_EOL;
        fwrite(STDOUT, '[INPUT] 请输入新的Token:'.PHP_EOL);
        $this->token = trim(fgets(STDIN, 9216));
        if (empty($this->token)) {
            $this->token = file_get_contents('token');
        } else {
            //fscanf(STDIN, "%s\n", $this->token);
            file_put_contents('token', $this->token);
        }
        fwrite(STDOUT, '[NOTICE] Token 已更新。');
    }

    /**
     * 转换url到数组.
     *
     * @author: dingdayu(614422099@qq.com)
     *
     * @param $query
     *
     * @return array
     */
    private function convertUrlQuery($query)
    {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }

        return $params;
    }

    /**
     * 获取签名.
     *
     * code => 401 用户验证失败
     *
     * @author: dingdayu(614422099@qq.com)
     *
     * @return mixed
     */
    public function getSig()
    {
        $url = 'https://mzstorage.meizu.com/file/get_sig';
        $output = $this->curl($url, 'post', ['type' => 2, 'token' => $this->token]);

        return json_decode($output, true);
    }

    /**
     * 执行网络请求
     *
     * @author: dingdayu(614422099@qq.com)
     *
     * @param string $url
     * @param string $method
     * @param array  $data
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function curl($url = '', $method = 'get', $data = [])
    {
        $ch = curl_init();
        if ($method === 'get') {
            $url = $url.'?'.http_build_query($data);
        } else {
            // post数据
            curl_setopt($ch, CURLOPT_POST, 1);
            // post的变量
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名

        $output = curl_exec($ch);

        if ($output === false) {
            throw new Exception('CURL ERROR: '.curl_error($ch));
        }
        curl_close($ch);

        return $output;
    }

    /**
     * 获取相册列表.
     *
     * @author: dingdayu(614422099@qq.com)
     *
     * @param int $dirId  相册id
     * @param int $offset 偏移量  offset 为 page*limit
     * @param int $limit  每页记录数
     *
     * @return mixed
     */
    public function getAlbumList($dirId = 0, $offset = 0, $limit = 48)
    {
        $url = 'https://mzstorage.meizu.com/album/list';
        //偏移量  offset 为 page*limit
        $output = $this->curl(
            $url,
            'post',
            ['dirId' => $dirId, 'limit' => $limit, 'offset' => $offset, 'order' => 1, 'token' => $this->token]
        );

        return json_decode($output, true);
    }

    /**
     * 获取图册列表（相册内容）.
     *
     * @author: dingdayu(614422099@qq.com)
     *
     * @param int $offset 偏移量  offset 为 page*limit
     * @param int $limit  每页记录数
     *
     * @return mixed
     */
    public function getDirList($offset = 0, $limit = 30)
    {
        $url = 'https://mzstorage.meizu.com/album/dir/list';
        //偏移量  offset 为 page*limit
        $output = $this->curl(
            $url,
            'post',
            ['limit' => $limit, 'offset' => $offset, 'order' => 1, 'token' => $this->token]
        );

        return json_decode($output, true);
    }

    /**
     * 获取时间轴列表.
     *
     * @author: dingdayu(614422099@qq.com)
     *
     * @return mixed
     */
    public function getGroup()
    {
        $url = 'https://mzstorage.meizu.com/album/group';
        //偏移量  offset 为 page*limit
        $output = $this->curl(
            $url,
            'post',
            ['order' => 1, 'token' => $this->token]
        );

        return json_decode($output, true);
    }

    /**
     * 获取时间轴.
     *
     * @author: dingdayu(614422099@qq.com)
     *
     * @param int $startTime
     * @param int $endTime
     * @param int $limit
     * @param int $offset
     *
     * @return mixed
     */
    public function getListRange($startTime = 0, $endTime = 0, $limit = 100, $offset = 0)
    {
        $url = 'https://mzstorage.meizu.com/album/listRange';
        //偏移量  offset 为 page*limit
        $output = $this->curl(
            $url,
            'post',
            ['startTime' => $startTime, 'endTime' => $endTime, 'limit' => $limit, 'offset' => $offset, 'order' => 1, 'isWebp' => true, 'token' => $this->token]
        );

        return json_decode($output, true);
    }

    /**
     * 获取用户相册信息.
     *
     * @json {"code":200,"message":"ok","value":{"vipId":2,"maxFileNum":0,"vip":2,"modifyTime":1499672059000,"isMaxLevel":0,"vipName":"免费标准版","createTime":1477824798000,"endTime":0,"now":1499789886901,"fileNum":4732,"usedVolume":17761892038,"maxVolume":21474836480}}
     *
     * @return mixed
     */
    public function getUserInfo()
    {
        $url = 'https://mzstorage.meizu.com/user/info';
        $output = $this->curl(
            $url,
            'post',
            ['type' => 0, 'token' => $this->token]
        );

        return json_decode($output, true);
    }
}
