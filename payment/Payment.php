<?php

$basedir = preg_replace('/\\\lib.*|\/lib.*/', '', __DIR__);
require_once($basedir.'/lib/payment/bank/Bank.php');
require_once($basedir.'/lib/payment/credit/Credit.php');
require_once($basedir.'/lib/payment/crypto/Crypto.php');

class Payment{

    public $bank;
    public $credit;
    public $crypto;
    public $debug;
    public $fstcore;

    function __construct(){
        //INIT CLASS
        $this->bank = new Bank();
        $this->credit = new Credit();
        $this->crypto = new Crypto();
        
        //INIT FSTCORE
        $this->bank->fstcore = $this->fstcore;
        $this->credit->fstcore = $this->fstcore;
        $this->crypto->fstcore = $this->fstcore;

        //INIT DEBIG
        $this->bank->debug = $this->debug;
        $this->credit->debug = $this->debug;
        $this->crypto->debug = $this->debug;
    }

    function __destruct()
    {
        
    }
}
?>
