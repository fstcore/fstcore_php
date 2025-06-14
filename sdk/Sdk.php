<?php

$basedir = preg_replace('/\/include.*/', '', __DIR__);
require_once($basedir.'/include/lib/sdk/binance/Binance.php');
require_once($basedir.'/include/lib/sdk/censys/Censys.php');
require_once($basedir.'/include/lib/sdk/dropbox/Dropbox.php');
require_once($basedir.'/include/lib/sdk/dubox/Dubox.php');
require_once($basedir.'/include/lib/sdk/fstrade/Fstrade.php');
require_once($basedir.'/include/lib/sdk/indodax/Indodax.php');
require_once($basedir.'/include/lib/sdk/payeer/Payeer.php');
require_once($basedir.'/include/lib/sdk/shodan/Shodan.php');
require_once($basedir.'/include/lib/sdk/tokocrypto/Tokocrypto.php');

class Sdk{

    public $binance;
    public $censys;
    public $dropbox;
    public $dubox;
    public $fstrade;
    public $indodax;
    public $payeer;
    public $shodan;
    public $tokocrypto;
    public $debug;
    public $fstcore;

    function __construct(){
        //INIT CLASS
        $this->binance = new Binance();
        $this->censys = new Censys();
        $this->dropbox = new Dropbox();
        $this->dubox = new Dubox();
        $this->fstrade = new Fstrade();
        $this->indodax = new Indodax();
        $this->payeer = new Payeer();
        $this->shodan = new Shodan();
        $this->tokocrypto = new Tokocrypto();
        
        //SET FSTCORE
        $this->binance->fstcore = $this->fstcore;
        $this->censys->fstcore = $this->fstcore;
        $this->dropbox->fstcore = $this->fstcore;
        $this->dubox->fstcore = $this->fstcore;
        $this->fstrade->fstcore = $this->fstcore;
        $this->indodax->fstcore = $this->fstcore;
        $this->payeer->fstcore = $this->fstcore;
        $this->shodan->fstcore = $this->fstcore;
        $this->tokocrypto->fstcore = $this->fstcore;

        //SET DEBUG
        $this->binance->debug = $this->debug;
        $this->censys->debug = $this->debug;
        $this->dropbox->debug = $this->debug;
        $this->dubox->debug = $this->debug;
        $this->fstrade->debug = $this->debug;
        $this->indodax->debug = $this->debug;
        $this->payeer->debug = $this->debug;
        $this->shodan->debug = $this->debug;
        $this->tokocrypto->debug = $this->debug;
    }

    function __destruct()
    {
        
    }
}
?>
