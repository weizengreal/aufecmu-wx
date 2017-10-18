<?php
namespace wxLib;

class Jssdk {
	private $appId;
	private $path;

	public function __construct( ) {
		$this->appId = "wx5aba40d737e98b5d";
        $this->path = "./public/static/jsapi_ticket_admin.json";
        $this->cardPath = "./public/static/api_ticket_admin.json";
	}

    public function getSignPackage($url=null) {
        $jsapiTicket = $this->getJsApiTicket();
        if(empty($url)) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "String" => $string,
            "jsapiTicket" => $jsapiTicket,
        );
        \think\Log::write(json_encode($signPackage),"WARNING11");
        return $signPackage;
    }

    public function getSignPackageForCard($url=null) {
        $jsapiTicket = $this->getJsApiTicket();
        $apiTicket = $this->getApiTicket();
        if(empty($url)) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        }
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "String" => $string,
            "apiTicket" => $apiTicket,
            "signature_card" => self::cal_sign(array($timestamp,$nonceStr,config("cardId"),$apiTicket))
        );
        \think\Log::write(json_encode($signPackage),"Warning22");
        return $signPackage;
    }



    /**
     * 计算签名
     * @param array $param_array
     */
    private static function cal_sign($param_array) {
        sort($param_array, SORT_STRING);
        $paramStr = implode("",$param_array);
        file_put_contents("test.txt",$paramStr.PHP_EOL,FILE_APPEND);
        return sha1($paramStr);

        $item_array = array();
        foreach ($names as $name) {
            $item_array[] = "{$name}={$param_array[$name]}";
        }
        $api_secret = 'E5197194A57187429C884CF96DDC505D'; //微校时提交给微校，32位字符串)
        $str = implode('&', $item_array) . '&key=' . $api_secret;
        return strtoupper(md5($str));
    }
	
	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	private function getJsApiTicket() {
		// jsapi_ticket 应该全局存储与更新
		$data = json_decode(file_get_contents($this->path ) ,true);
		if ( empty($data) || $data['expire_time'] < time() ) {
			$accessToken = $this->getAccessToken();
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$result=json_decode(file_get_contents($url),true);
			if($result['errcode']==40001){
				//如果accesstoken过期,重要求服务端更新accesstoken
				$accessToken = $this->updateAccessToken();
				$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
				$result=json_decode(file_get_contents($url),true);
			}
			$ticket = $result['ticket'];
			if ($ticket) {
				$data['expire_time'] = time()+7000;
				$data['ticket'] = $ticket;
				file_put_contents($this->path,json_encode($data));
			}
		} else {
			$ticket = $data['ticket'];
		}
		return $ticket;
	}

	/*
	 * 获得添加会员卡的apiTicket
	 * */
	private function getApiTicket() {
        $data = json_decode(file_get_contents($this->cardPath ) ,true);
        if ( empty($data) || $data['expire_time'] < time() ) {
            $accessToken = $this->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$accessToken&type=wx_card";
            $result=json_decode(file_get_contents($url),true);
            if($result['errcode']==40001){
                //如果accesstoken过期,重要求服务端更新accesstoken
                $accessToken = $this->updateAccessToken();
                $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$accessToken&type=wx_card";
                $result=json_decode(file_get_contents($url),true);
            }
            $ticket = $result['ticket'];
            if ($ticket) {
                $data['expire_time'] = time()+7000;
                $data['ticket'] = $ticket;
                file_put_contents($this->cardPath,json_encode($data));
            }
        }
        else {
            $ticket = $data['ticket'];
        }
        return $ticket;
    }

	private function getAccessToken() {
		return file_get_contents("http://api.aufe.vip/jssdk/zacToken");
	}

	private function updateAccessToken() {
		return file_get_contents("http://api.aufe.vip/jssdk/upZacToken");
	}
}

?>