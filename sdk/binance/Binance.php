<?php

class Binance
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
		$this->url = $this->configjson->binance->url;
		$this->key = $this->configjson->binance->key;
		$this->secretKey = $this->configjson->binance->secretKey;
		$this->api = $this->configjson->binance->api;
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
				return $curlrequest->curl_get($url . "/?" . $query, $headers);
			} else {
				return $curlrequest->curl_get($url, $headers);
			}
		} else {
			if ($query != null) {
				return $curlrequest->curl_post($url . "/?" . $query, $headers, $postdata);
			} else {
				return $curlrequest->curl_post($url, $headers, $postdata);
			}
		}
	}

	public function get_nonce()
	{
		$response = self::request("get", $this->api . "/api/v3/time", null, null);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["serverTime"])) {
				return $jsondata["body"]["serverTime"] + 5;
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
			'recvWindow' => '5000',
			'signature' => $sign
		];
		$query = http_build_query($data, '', '&');
		$response = self::request("get", "/api/v3/klines", $query, null);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if ($jsondata["body"][0][4] != "") {
				return $jsondata["body"][0][4];
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
			'needBtcValuation' => true,
			'recvWindow' => 500,
			'timestamp' => $servertime
		];
		$sign = self::get_sign($data);
		$query = http_build_query($data, '', '&');
		$response = self::request("get", $this->api . "/sapi/v3/asset/getUserAsset", $query, null);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"][0]["free"])) {
				return $jsondata["body"][0]["free"];
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
			'symbol' => $pair,
			'orderId' => $orderid,
			'origClientOrderId' => '',
			'recvWindow' => 500,
			'timestamp' => $servertime
		];
		$sign = self::get_sign($data);
		$query = http_build_query($data, '', '&');
		$response = self::request("get", $this->api . "/api/v3/order", $query, null);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["status"])) {
				return strtolower($jsondata["body"]["status"]);
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
			'quantity' => $amount,
			'quoteOrderQty' => 0,
			'price' => $lastprice,
			'newClientOrderId' => '',
			"recvWindow" => 500,
			'timestamp' => $servertime
		];
		$sign = self::get_sign($data);
		$query = http_build_query($data, '', '&');
		$response = self::request("post", $this->api . "/api/v3/order", null, $data);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["orderId"])) {
				if ($jsondata["body"]["orderId"] != "") {
					return $jsondata["body"]["orderId"];
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
		$typcoin = explode('_', $pair);
		$servertime = self::get_nonce();
		$data = [
			'symbol' => $pair,
			'side' => $this->OrderSide->MARKET,
			'type' => $this->OrderType->SELL,
			'quantity' => $amount,
			'quoteOrderQty' => 0,
			'price' => $lastprice,
			'newClientOrderId' => '',
			"recvWindow" => 500,
			'timestamp' => $servertime
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("post", $this->api . "/api/v3/order", null, $data);
		$jsondata = json_decode($response);
		if ($jsondata["code"] == 200) {
			if (isset($jsondata["body"]["return"])) {
				if ($jsondata["body"]["orderId"] != "") {
					return $jsondata["body"]["orderId"];
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
			'symbol' => $pair,
			'orderId' => $order_id,
			'origClientOrderId' => '',
			'recvWindow' => 500,
			'timestamp' => $servertime
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("delete", $this->api . "/api/v3/order", null, $data);
		$jsondata = json_decode($response);
		return true;
	}

	public function cancel_buy($pair = 'xmr_idr', $order_id)
	{
		$servertime = self::get_nonce();
		$data = [
			'symbol' => $pair,
			'orderId' => $order_id,
			'origClientOrderId' => '',
			'recvWindow' => 500,
			'timestamp' => $servertime
		];
		$post_data = http_build_query($data, '', '&');
		$sign = self::get_sign($post_data);
		$response = self::request("delete", $this->api . "/api/v3/order", null, $data);
		$jsondata = json_decode($response);
		return true;
	}

}