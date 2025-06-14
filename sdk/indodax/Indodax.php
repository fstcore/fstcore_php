<?php

class Indodax
{

	private $configjson;
	protected $urlpublic;
	protected $url;
	protected $key;
	protected $secretKey;
	public $folder;
	public $debug;
    public $fstcore;

	public function __construct()
	{
		$this->configjson = json_decode(self::open_config());
		$this->url = $this->configjson->indodax->url;
		$this->key = $this->configjson->indodax->key;
		$this->secretKey = $this->configjson->indodax->secretKey;
		$this->urlpublic = $this->configjson->indodax->urlpublic;
	}

	function open_config()
	{
		$open = @fopen($this->folder."/config/config.json", "r+");
		$data = fread($open, filesize($open));
		fclose($data);
		return $data;
	}

	private function get_sign($post_data)
	{
		$sign = hash_hmac('sha512', $post_data, $this->secretKey);
		return $sign;
	}

	function request($type, $sign=null, $url, $query = null, $postdata = null)
	{
		$headers = ['UserAgent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36', 'Key: ' . $this->key, 'Sign: ' . $sign, 'Cookie: _fbp=fb.1.1677055446150.283664719; _ga=GA1.2.1270320565.1677055447; _tt_enable_cookie=1; _ttp=LGL9AWYlMD1pLyn2ejqdfyDqb8f; servedby=2; _gcl_au=1.1.1377962022.1677055610; __auc=49162434186784dd25820e17931; __zlcmid=1EYlcY80vFOoN25; IDX_auth.strategy=local; _gid=GA1.2.724326741.1679587441; cf_clearance=LATwBzs_W7FnFu6K6ztFInnZAkywGz8yJBkLiZauomc-1679594920-0-160; __cf_bm=o3kSfg4bfM_S.CJjo2hoVESYQ9SL3Uzpu2LXynpRxBc-1679594920-0-ASHASQr9Vy17mQL/zGBB6YKUL5qP5MPfqdGm+Re0WHgmwfsv0CoAMnbHWfA+jbnl/tvOXYROT/jLjllZdX+agSs=; _gat_gtag_UA_46363731_7=1; _gat_gtag_UA_46363731_11=1'];
		$curlrequest = $this->fstcore->util->http;
		if ($type == "get") {
			if ($query != null) {
				return $curlrequest->curl_get($this->url . "/?" . $query, $headers);
			} else {
				return $curlrequest->curl_get($this->url, $headers);
			}
		} else {
			if ($query != null) {
				return $curlrequest->curl_post($this->url . "/?" . $query, $headers, $postdata);
			} else {
				return $curlrequest->curl_post($this->url, $headers, $postdata);
			}
		}
	}

	public function get_nonce()
	{
		$data = [
			'method' => 'getInfo',
			'timestamp' => '1578304294000',
			'recvWindow' => '1578303937000'
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("post", $sign, $this->url, null, $data);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["return"]["server_time"])) {
				return ($jsondata["body"]["return"]["server_time"] + 5);
			}
		} else {
			sleep(5);
			return self::get_nonce();
		}
	}

	public function get_lastprice($pair = 'xmr_idr')
	{
		$servertime = self::get_nonce();
		$response = self::request("post", null, $this->urlpublic, null, "n=n");
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if ($jsondata["body"]["tickers"][$pair]["last"] != "") {
				return $jsondata["body"]["tickers"][$pair]["last"];
			} else {
				return self::get_lastprice($pair);
			}
		} else {
			return self::get_lastprice($pair);
		}
	}

	public function get_balance($pair = 'xmr_idr')
	{
		$typcoin = explode('_', $pair);
		$servertime = self::get_nonce();
		$data = [
			'method' => 'getInfo',
			'timestamp' => '1578304294000',
			'recvWindow' => '1578303937000'
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("post", $sign, $this->url, null, $data);
		$jsondata = json_decode($response);
		if ($response["code"] == 200) {
			if ($jsondata["body"]["return"]["balance"][$typcoin[0]]) {
				return ($jsondata["body"]["return"]["balance"][$typcoin[0]]);
			} else {
				return '0';
			}
		} else {
			sleep(5);
			return self::get_balance($pair);
		}
	}

	public function get_order_status($pair = 'btc_idr', $orderid)
	{
		$servertime = self::get_nonce();
		$data = [
			'method' => 'getOrder',
			'nonce' => $servertime,
			'pair' => $pair,
			'order_id' => $orderid
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("post", $sign, $this->url, null, $data);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["return"])) {
				return $jsondata;
			}
		} else {
			return self::get_order_status($pair, $orderid);
		}
	}

	public function sell($pair = 'xmr_idr', $lastprice, $amount)
	{
		$typcoin = explode('_', $pair);
		$servertime = self::get_nonce();
		$data = [
			'method' => 'trade',
			'nonce' => $servertime,
			'pair' => $pair,
			'type' => 'sell',
			'price' => $lastprice,
			$typcoin[0] => $amount
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("post", $sign, $this->url, null, $data);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["return"])) {
				if ($jsondata["body"]["return"]["order_id"] != "") {
					return $jsondata["body"]["return"]["order_id"];
				} else {
					return self::sell($pair, $lastprice, $amount);
				}
			}
		} else {
			return self::sell($pair, $lastprice, $amount);
		}
	}

	public function buy($pair = 'xmr_idr', $lastprice, $amount)
	{
		$servertime = self::get_nonce();
		$data = [
			'method' => 'trade',
			'nonce' => $servertime,
			'pair' => $pair,
			'type' => 'buy',
			'price' => $lastprice,
			'idr' => $amount
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("post", $sign, $this->url, null, $data);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["return"])) {
				if ($jsondata["body"]["return"]["order_id"] != "") {
					return $jsondata["body"]["return"]["order_id"];
				} else {
					return self::buy($pair, $lastprice, $amount);
				}
			}
		} else {
			return self::buy($pair, $lastprice, $amount);
		}
	}

	public function cancel_sell($pair = 'xmr_idr', $order_id)
	{
		$servertime = self::get_nonce();
		$data = [
			'method' => 'cancelOrder',
			'nonce' => $servertime,
			'pair' => $pair,
			'order_id' => $order_id,
			'type' => 'sell'
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("post", $sign, $this->url, null, $data);
		return true;
	}

	public function cancel_buy($pair = 'xmr_idr', $order_id)
	{
		$servertime = self::get_nonce();
		$data = [
			'method' => 'cancelOrder',
			'nonce' => $servertime,
			'pair' => $pair,
			'order_id' => $order_id,
			'type' => 'buy'
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("post", $sign, $this->url, null, $data);
		return true;
	}

}