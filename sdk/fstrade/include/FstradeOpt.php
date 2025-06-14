<?php

class FstradeOpt
{

    public $folder;
    public $debug;
    public $fstcore;
    
    function __construct()
    {

    }

    function percentage($amount)
    {
        $percent = 0.2;
        $result = ($amount / 100) * $percent + $amount;
        $result_clear = preg_replace('/\..*/', '', $result);
        //echo "[+] with percentage : ".$percent."% : ".$result_clear."\n";
        return $result_clear;
    }

    public function stopless($pair){
        $returned = 99999999999999999999999999999999999999999999999999;
		$mins = 0;
		switch ($pair) {
			case "xmr_idr":
				$returned = 3300000;
				$mins = 1800;
				break;
			case "btc_idr":
				$returned = 636683000;
				$mins = 0;
				break;
			case "usdt_idr":
				$returned = 17000;
				$mins = 0;
				break;
			default:
				break;
		}
        return $returned;
    }

    public function calculator($type, $price, $amount) {
        if ($type == "buy") {
            return ($amount / $price);
        } else if ($type == "sell") {
            return ($amount * $price);
        }
    }

    function array_explode($filename, $d)
    {
        $openfile_duplicat = fopen($filename, 'r');
        $data_duplicat = fread($openfile_duplicat, filesize($filename));
        fclose($openfile_duplicat);
        $arrayc = array_values(array_filter(array_map('trim', explode("\n", $data_duplicat))));
        if ($arrayc[count($arrayc) - 1] == $d) {
            echo "duplicate entry (skip) : " . $arrayc[count($arrayc) - 1] . " == " . $d . "\n";
            return false;
        } else {
            echo "duplicate entry (append) : " . $arrayc[count($arrayc) - 1] . " != " . $d . "\n";
            return true;
        }
    }

    public function logging($log){
        $this->fstcore->util->filesystem->appendfile($this->folder."/log/", "fstrade.log", $log);
    }

    public function save_data($folder, $data_obj){
        $this->fstcore->util->filesystem->writefile($folder."/setting/", $data_obj->filename, json_encode($data_obj));
    }

    public function append_history_price($folder, $market, $pair, $price)
    {
        mkdir($folder);
        mkdir($folder."/data/");
        mkdir($folder."/data/price");
        mkdir($folder."/data/price/" . $market);
        $filename = $folder."/data/price/" . $market . "/" . $pair . ".txt";
        if (self::array_explode($filename, $price)) {
            $this->fstcore->util->filesystem->appendfile($folder."/data/price/" . $market."/", $pair.".txt", $price."\n");
        }
    }

    public function open_history_price($folder, $market, $pair)
    {
        $data_buy_order = $this->fstcore->util->filesysten->readfile($folder."/data/price/" . $market . "/", $pair . ".txt");
        $array_price = array_values(array_filter(array_map('trim', explode("\n", $data_buy_order))));
        //$array_buy_order = array_combine(range(0, count($array_buy_ordertmp)), array_values($array_buy_ordertmp));
        //var_dump($array_buy_order);
        return $array_price;
    }

    function __destruct(){

    }
}

?>
