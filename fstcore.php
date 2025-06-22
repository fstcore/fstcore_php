<?php

$basedir = preg_replace('/\\\lib.*|\/lib.*/', '', __DIR__);
require_once($basedir.'/lib/datacore/Datacore.php');
require_once($basedir.'/lib/payment/Payment.php');
require_once($basedir.'/lib/sdk/Sdk.php');
require_once($basedir.'/lib/ui/Ui.php');
require_once($basedir.'/lib/util/Util.php');

class fstcore{

    public $datacore;
    public $payment;
    public $sdk;
    public $ui;
    public $util;

    public $config;
    public $debug;
    public $fstcore;

    function __construct(){
        //INIT CLASS
        $this->datacore = new Datacore();
        $this->payment = new Payment();
        $this->sdk = new Sdk();
        $this->ui = new Ui();
        $this->util = new Util();

        //OPEN CONFIG
        self::open_config();
        $this->fstcore = $this;
        $this->debug = $this->config->debug;
        
        //SET FSTCORE
        $this->datacore->fstcore = $this->fstcore;
        $this->payment->debug = $this->fstcore;
        $this->util->debug = $this->fstcore;
        
        //SET DEBUG
        $this->datacore->debug = $this->debug;
        $this->payment->debug = $this->debug;
        $this->util->debug = $this->debug;
        
    }

    function open_config(){
        $this->config = $this->util->data->init;
    }

    function __destruct(){

    }
    
}

?>
