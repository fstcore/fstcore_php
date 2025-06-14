<?php

class Tokocrypto
{

	private $configjson;
	protected $url;
	protected $key;
	protected $secretKey;
	protected $api;
	private $SymbolType;
	private $OrderStatus;
	private $OrderType;
	private $OrderSide;
	private $KlineCandlesIntervals;
	public $folder;
	public $debug;
    public $fstcore;

	public function __construct()
	{
		$this->configjson = json_decode(self::open_config());
		$this->url = $this->configjson->tokocrypto->url;
		$this->key = $this->configjson->tokocrypto->key;
		$this->secretKey = $this->configjson->tokocrypto->secretKey;
		$this->api = $this->configjson->tokocrypto->urlpublic;
		$this->SymbolType = json_decode("{
			MAIN: 1,
			NEXT: 2
		}");
		$this->OrderStatus = json_decode("{
			-2: SYSTEM_PROCESSING,
			0: OPEN,
			1: PARTIALLY_FILLED,
			2: FILLED,
			3: CANCELED,
			4: PENDING_CANCEL,
			5: REJECTED,
			6: EXPIRED
		}");
		$this->OrderType = json_decode("{
			LIMIT: 1,
			MARKET: 2,
			STOP_LOSS: 3,
			STOP_LOSS_LIMIT: 4,
			TAKE_PROFIT: 5,
			TAKE_PROFIT_LIMIT: 6,
			LIMIT_MAKER: 7
		}");
		$this->OrderSide = json_decode("{
			BUY: 0,
			SELL: 1
		}");
		$this->KlineCandlesIntervals = json_decode("{
			MINUTES: m,
			HOURS: h,
			DAYS : d,
			WEEKS: w,
			MONTHS: M
		}");
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
		$sign = hash_hmac('sha256', $post_data, $this->secretKey);
		return $sign;
	}

	function request($type, $url, $query = null, $postdata = null)
	{
		$headers = ['UserAgent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36', 'X-MBX-APIKEY:' . $this->key];
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
		$response = self::request("get", $this->url . "/open/v1/common/time", null, null);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["timestamp"])) {
				return $jsondata["body"]["timestamp"] + 5;
			}
			sleep(5);
			return self::get_nonce();
		} else {
			return self::get_nonce();
		}
	}

	public function get_lastprice($pair = 'xmr_idr')
	{
		$servertime = self::get_nonce();
		$sign = self::get_sign(null);
		$data = [
			'symbol' => $pair,
			'interval' => "1" . $this->KlineCandlesIntervals->minutes,
			'startTime' => $servertime,
			'endTime' => $servertime,
			'limit' => 200,
			'timestamp' => $servertime,
			'recvWindow' => 500,
			'signature' => $sign
		];
		$query = http_build_query($data, '', '&');
		$response = self::request("get", "https://cloudme-toko.2meta.app/api/v1/klines", $query, null);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if ($jsondata["body"]["data"][4] != "") {
				return $jsondata["body"]["data"][4];
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
			'asset' => $typcoin[0],
			'timestamp' => $servertime,
			'recvWindow' => '5000'
		];
		$sign = self::get_sign($data);
		$query = http_build_query($data, '', '&');
		$response = self::request("get", $this->url . "/open/v1/account/spot/asset", $query, null);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["data"]["free"])) {
				return $jsondata["body"]["data"]["free"];
			} else {
				return "0";
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
			'order_id' => $orderid
		];
		$sign = self::get_sign($data);
		$query = http_build_query($data, '', '&');
		$response = self::request("get", $this->url . "/open/v1/orders/detail", $query, null);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["data"]["status"])) {
				$status = $jsondata["body"]["data"]["status"];
				return array("status" => strtolower($this->OrderStatus->$status), "type" => $jsondata["body"]["data"]["type"]);
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
			'symbol' => $pair,
			'side' => $this->OrderSide->MARKET,
			'type' => $this->OrderType->SELL,
			'price' => $lastprice,
			"quantity" => $amount
		];
		$sign = self::get_sign($data);
		$query = http_build_query($data, '', '&');
		$response = self::request("post", $this->url . "/open/v1/orders", null, $data);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["data"]["orderId"])) {
				if ($jsondata["body"]["data"]["orderId"] != "") {
					return $jsondata["body"]["data"]["orderId"];
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
		$nonce = self::get_nonce();
		$data = [
			'symbol' => $pair,
			'side' => $this->OrderSide->MARKET,
			'type' => $this->OrderType->BUY,
			'price' => $lastprice,
			"quantity" => $amount
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("post", $this->url . "/open/v1/orders", null, $data);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["data"]["orderId"])) {
				if ($jsondata["body"]["data"]["orderId"] != "") {
					return $jsondata["body"]["data"]["orderId"];
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
		$nonce = self::get_nonce();
		$data = [
			'order_id' => $order_id
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("post", $this->url . "/open/v1/orders/cancel", null, $data);
		$jsondata = json_decode($response);
		return $jsondata["body"]["data"]["orderId"];
	}

	public function cancel_buy($pair = 'xmr_idr', $order_id)
	{
		$nonce = self::get_nonce();
		$data = [
			'order_id' => $order_id
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("post", $this->url . "/open/v1/orders/cancel", null, $data);
		$jsondata = json_decode($response);
		return $jsondata["body"]["data"]["orderId"];
	}

	function __destruct()
	{
		
	}
}

?>
