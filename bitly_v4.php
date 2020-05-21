<?php
// https://dev.bitly.com/  国外 bitly 短网址api
// author: aben
// create date: 2020-2-22

Class bitlyApi {
    //The URI of the standard bitly v4 API.
    const BITLY_API_V4 = 'https://api-ssl.bitly.com/v4/';

    private $token = '';
    private $group_guid = '';

    function __construct() {
        $this->token = 'bilty_token';//put your token here
        $this->group_guid = 'bilty_group_guid';//put one group id here
    }

    function __destruct() {
    }

    /**
     * 获取短网址, 返回数组
     * @param string $long_url 要转换的长网址
     * @param string $token api使用的token
     * @return array
     */
    function get_short_url($long_url, $token = null, $group_guid = null) {
        if ($token === null || strlen($token) == 0) {
            $token = $this->token;
        }
        if ($group_guid === null || strlen($group_guid) == 0) {
            $group_guid = $this->group_guid;
        }

        if (empty($token) || empty($group_guid)) {
            return array('err_code' => 'error', 'err_msg' => 'token/group not set', 'short_url' => '');
        }
        $params = [];
        $params['access_token'] = $token;
        $params['longUrl'] = $long_url;
        $params['group_guid'] = $group_guid;//v4
        $rt = $this->bitly_get_v4('shorten', $params);
        //返回结果示例
        // {"created_at": "2019-08-31T03:13:46+0000","id": "bit.ly/2ZL5Ood","link": "http://bit.ly/2ZL5Ood","custom_bitlinks": [],"long_url": "https://www.takeyourtrip.com/","archived": false,"tags": [],"deeplinks": [],"references": {"group": "https://api-ssl.bitly.com/v4/groups/xxxx"}}
        // {"message": "FORBIDDEN","resource": "bitlinks","description": "You are currently forbidden to access this resource."}

        if (!is_array($rt)) {
            return array('err_code' => 'error', 'err_msg' => 'request failed', 'short_url' => '');
        }
        if (!array_key_exists('id', $rt)) {
            if (array_key_exists('message', $rt)) {
                $err_msg = 'request failed: ' . $rt['message'];
            } else {
                $err_msg = 'request failed';
            }
            return array('err_code' => 'error', 'err_msg' => $err_msg, 'short_url' => '');
        }

        $err_code = 'success';
        $err_msg = 'success';
        $long_url = $rt['long_url'];
        $short_url = $rt['id'];

        return array('err_code' => $err_code, 'err_msg' => $err_msg, 'long_url' => $long_url, 'short_url' => $short_url);
    }

    function bitly_get_v4($endpoint, $params) {
        /*
        POST https://api-ssl.bitly.com/v4/shorten HTTP/1.1
        Host: api-ssl.bitly.com
        Header设置:
            Authorization: Bearer ACCESS_TOKEN
            Content-Type: application/json
        body数据:
            {"long_url": "https://www.takeyourtrip.com/","group_guid": "xxxx"}

        实际返回数据:
        {
            "created_at": "2019-08-31T03:13:46+0000",
            "id": "bit.ly/2ZL5Ood",
            "link": "http://bit.ly/2ZL5Ood",
            "custom_bitlinks": [],
            "long_url": "https://www.takeyourtrip.com/",
            "archived": false,
            "tags": [],
            "deeplinks": [],
            "references": {
                "group": "https://api-ssl.bitly.com/v4/groups/xxxx"
            }
        }
         */
        $url = self::BITLY_API_V4 . $endpoint;
        $header = [
            'Authorization: Bearer ' . $params['access_token'],
            'Content-Type: application/json'
        ];
        $data = [
            'long_url' => $params['longUrl'],
            'group_guid' => $params['group_guid']
        ];
        $rt = $this->curl_post($url, $header, $data);
        //返回结果示例
        // {"created_at": "2019-08-31T03:13:46+0000","id": "bit.ly/2ZL5Ood","link": "http://bit.ly/2ZL5Ood","custom_bitlinks": [],"long_url": "https://www.takeyourtrip.com/","archived": false,"tags": [],"deeplinks": [],"references": {"group": "https://api-ssl.bitly.com/v4/groups/xxxx"}}
        // {"message": "FORBIDDEN","resource": "bitlinks","description": "You are currently forbidden to access this resource."}

        $result = json_decode($rt, true);

        return $result;
    }

    public function curl_post($url, $header = null, $data = null) {
        $output = '';
        //$header = ['Authorization: Bearer ACCESS_TOKEN', 'Content-Type: application/json'];
        //$data = ['long_url'=>'https://www.takeyourtrip.com', 'group_guid'=>'GUID'];
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);//post
            if (!empty($header)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_HEADER, 0);//返回response头部信息
            }
            // 把post的变量加上
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $output = curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            //tep_error_log('Bitly request failed, url: '.$url);
        }
        //状态返回200的才是正常
        return $output;
    }
}
