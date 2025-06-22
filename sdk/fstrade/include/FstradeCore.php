<?php

$basedir = preg_replace('/\\\lib.*|\/lib.*/', '', __DIR__);
include($basedir.'/lib/sdk/fstrade/include/FstradeOpt.php');

class FstradeCore
{

	public $fstrade_opt;
	public $folder;
	private $market;
	private $pair;
	private $amount_buy;
	private $amount_sell;
	private $lastprice_buy;
	private $lastprice_sell;
	private $order_id;
	private $data_obj;
	public $debug;
    public $fstcore;

	function __construct()
	{
		$this->fstrade_opt = new FstradeOpt();
		$this->fstrade_opt->debug = $this->debug;
		$this->fstrade_opt->fstcore = $this->fstcore;
	}

	function logging($log){
		$this->fstrade_opt->logging($log);
	}

	function save_data()
	{
		$this->fstrade_opt->save_data($this->folder, $this->data_obj);
	}

	function save_history($lastprice){
		$this->fstrade_opt->append_history_price($this->folder, $this->data_obj->market, $this->data_obj->pair, $lastprice);
	}

	function open_history(){
		$this->fstrade_opt->open_history_price($this->folder, $this->data_obj->market, $this->data_obj->pair);
	}

	public function start($data_obj)
	{
		$this->data_obj = json_decode($data_obj);
		switch ($this->data_obj->market) {
			case "binance":
				$this->market = $this->fstcore->sdk->binance;
				break;
			case "indodax":
				$this->market = $this->fstcore->sdk->indodax;
				break;
			case "tokocrypto":
				$this->market = $this->fstcore->sdk->tokocrypto;
				break;
			default:
				break;
		}
		self::action();
	}

	function action()
	{
		if (!empty($this->data_obj->order_id)) {
			$response = $this->market->get_status($this->data_obj->order_id);
			$type = $response["type"];
			$status = $response["status"];
			//$with_price = $response['return']['order']['price'];
			if ($type == "sell" && $status == "filled") {
				self::buy();
			} else if ($type == "buy" && $status == "filled") {
				$this->data_obj->amount_sell = $this->market->get_balance($this->data_obj->pair);
				self::sell();
			} else if ($type == "sell" && $status == "open") {
				$this->data_obj->amount_sell = $this->market->get_balance($this->data_obj->pair);
				self::cancel($type);
			} else if ($type == "buy" && $status == "open") {
				self::cancel($type);
			} else if ($type == "sell" && $status == "cancelled") {
				$this->data_obj->amount_sell = $this->market->get_balance($this->data_obj->pair);
				self::sell();
			} else if ($type == "buy" && $status == "cancelled") {
				self::buy();
			}
		} else {
			if ($this->data_obj->type == "buy") {
				self::buy();
			} else {
				self::sell();
			}
		}
	}

	function cancel($action)
	{
		$next = false;
		$lastprice = $this->market->get_lastprice($this->data_obj->pair);
		if ($action == "buy") {
			$amount_calculated = self::$fstrade_opt->calculator($action, $lastprice, $this->data_obj->amount_buy);
		} else {
			$amount_calculated = self::$fstrade_opt->calculator($action, $lastprice, $this->data_obj->amount_sell);
		}
		if ($action == "buy") {
			if ($lastprice < $this->data_obj->with_price) {
				$this->market->cancel_buy($this->data_obj->pair, $this->data_obj->order_id);
				$jsondata = $this->market->get_openorder_id($this->data_obj->pair, $this->data_obj->order_id);
				$next = true;
				$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $this->data_obj->with_price . " action: cancel buy status: Lastprice > With price, Order buy is cancelled\n";
				echo $log;
				self::logging($log);
			} else {
				$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $this->data_obj->with_price . " action: cancel buy status: Lastprice > With price, Order buy cannot be cancelled\n";
				echo $log;
				self::logging($log);
			}
		} else {
			if ($lastprice > $this->data_obj->with_price && $amount_calculated > $this->data_obj->amount_buy) {
				$this->market->cancel_sell($this->data_obj->pair, $this->data_obj->order_id);
				$jsondata = $this->market->get_openorder_id($this->data_obj->pair, $this->data_obj->order_id);
				$next = true;
				$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $this->data_obj->with_price . " action: cancel sell status: Lastprice < With price, Order sell is cancelled\n";
				echo $log;
				self::logging($log);
			} else {
				$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $this->data_obj->with_price . " action: cancel sell status: Lastprice < With price, Order sell cannot be cancelled\n";
				echo $log;
				self::logging($log);
			}
		}
		if ($next) {
			if ($jsondata["return"]["order"]["status"] == "cancelled") {
				if ($action == "buy") {
					//$amount = $this->market->get_balance('idr_idr');
					//$this->data_obj->amount = $amount;
					$order_id = $this->market->buy_crypto($this->data_obj->pair, $lastprice, $this->data_obj->amount_buy);
				} else {
					//$amount = $this->market->get_balance($this->data_obj->pair);
					//$this->data_obj->amount = $amount;
					$order_id = $this->market->sell_crypto($this->data_obj->pair, $lastprice, $this->data_obj->amount_sell);
				}

				//APPEND PRICE TO HISTORY TXT
				self::save_history($lastprice);

				//UPDATE DATA ORDER JSON
				if ($action == "buy") {
					$this->data_obj->order_id = $order_id;
					$this->data_obj->type = "buy";
					$this->data_obj->status = "open";
					//$this->data_obj->income = "";
					$this->data_obj->amount_sell = $amount_calculated;
					//$this->data_obj->amount_buy = "";
					$this->data_obj->with_price = $lastprice;
					$this->data_obj->last_price_buy = $lastprice;
					//$this->data_obj->last_price_sell = "";
				} else {
					$this->data_obj->order_id = $order_id;
					$this->data_obj->type = "sell";
					$this->data_obj->status = "open";
					$this->data_obj->income = "+ " . ($amount_calculated - $this->data_obj->amount_buy);
					//$this->data_obj->amount_sell = "";
					//$this->data_obj->amount_buy = "";
					$this->data_obj->with_price = $lastprice;
					//$this->data_obj->last_price_buy = "";
					$this->data_obj->last_price_sell = $lastprice;
				}

				self::save_data();

				$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $this->data_obj->with_price . " action: buy/sell status: Successfull re-bid order\n";
				echo $log;
				self::logging($log);
			} else {
				$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $this->data_obj->with_price . " action: buy/sell status: Order already filled, cannot be cancelled\n";
				echo $log;
				self::logging($log);
			}
		}

	}

	function buy()
	{
		$stopless = $this->fstrade_opt->stopless($this->data_obj->pair);
		$lastprice = $this->market->get_lastprice($this->data_obj->pair);
		self::save_history($lastprice);
		$history_price = self::open_history();
		$amount_calculated = self::$fstrade_opt->calculator("buy", $lastprice, $this->data_obj->amount_buy);
		if (count($history_price) >= 3) {
			$lastprice = $history_price[count($history_price) - 1];
			if (($history_price[count($history_price) - 1]) < $stopless) {
				if (($history_price[count($history_price) - 3] < $history_price[count($history_price) - 4]) && ($history_price[count($history_price) - 1] > $history_price[count($history_price) - 2]) && ($history_price[count($history_price) - 2] < $history_price[count($history_price) - 3]) && ($history_price[count($history_price) - 1] != $history_price[count($history_price) - 3]) /*&& ($history_price[count($history_price) - 1] < $this->data_obj->with_price)*/) {
					//APPEND PRICE TO HISTORY TXT
					self::save_history($lastprice);

					$order_id = $this->market->buy($this->data_obj->pair, $lastprice, $this->data_obj->amount_buy);

					//UPDATE DATA ORDER JSON
					$this->data_obj->order_id = $order_id;
					$this->data_obj->type = "buy";
					$this->data_obj->status = "open";
					//$this->data_obj->income = "";
					$this->data_obj->amount_sell = $amount_calculated;
					//$this->data_obj->amount_buy = "";
					$this->data_obj->with_price = $lastprice;
					$this->data_obj->last_price_buy = $lastprice;
					//$this->data_obj->last_price_sell = "";

					self::save_data();

					$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $this->data_obj->with_price . " action: buy status: Successfull place order\n";
					echo $log;
					self::logging($log);
				} else {
					$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $lastprice . " action: buy status: Order still open\n";
					echo $log;
					self::logging($log);
				}
			} else {
				$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $lastprice . " action: buy status:  " . ($history_price[count($history_price) - 1]) . " > " . $stopless . " (price > stop)\n";
				echo $log;
				self::logging($log);
			}
		} else {
			$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $lastprice . " action: buy status: Order still open\n";
			echo $log;
			self::logging($log);
		}
	}

	function sell()
	{
		$stopless = $this->fstrade_opt->stopless($this->data_obj->pair);
		$lastprice = $this->market->get_lastprice($this->data_obj->pair);
		self::save_history($lastprice);
		$history_price = self::open_history();
		$amount_calculated = self::$fstrade_opt->calculator("sell", $lastprice, $this->data_obj->amount_sell);
		if (count($history_price) >= 3) {
			$lastprice = $history_price[count($history_price) - 1];
			$lastprice_buy = $this->data_obj->last_price_buy;
			if (($history_price[count($history_price) - 1] < $history_price[count($history_price) - 2]) && ($history_price[count($history_price) - 2] > $history_price[count($history_price) - 3]) && ($history_price[count($history_price) - 1] >= $lastprice_buy) && ($amount_calculated > $this->data_obj->amount_buy)) {
				//APPEND PRICE TO HISTORY TXT
				self::save_history($lastprice);

				$order_id = $this->market->sell($this->data_obj->pair, $lastprice, $this->data_obj->amount_sell);

				//UPDATE DATA ORDER JSON
				$this->data_obj->order_id = $order_id;
				$this->data_obj->type = "sell";
				$this->data_obj->status = "open";
				$this->data_obj->income = "+ " . ($amount_calculated - $this->data_obj->amount_buy);
				//$this->data_obj->amount_sell = "";
				//$this->data_obj->amount_buy = "";
				$this->data_obj->with_price = $lastprice;
				//$this->data_obj->last_price_buy = "";
				$this->data_obj->last_price_sell = $lastprice;

				self::save_data();

				$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $this->data_obj->with_price . " action: sell status: Successfull place order\n";
				echo $log;
				self::logging($log);
			} else {
				$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $lastprice . " action: sell status: Order still open\n";
				echo $log;
				self::logging($log);
			}
		} else {
			$log = "[*] market: " . $this->data_obj->market . " pair: " . $this->data_obj->pair . " price: " . $lastprice . " action: sell status: Order still open\n";
			echo $log;
			self::logging($log);
		}
	}

	function __destruct(){
		
	}
}

?>
