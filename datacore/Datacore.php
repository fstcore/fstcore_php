<?php

$basedir = preg_replace('/\/include.*/', '', __DIR__);
require_once($basedir.'/include/lib/datacore/datamanager/DeleteData.php');
require_once($basedir.'/include/lib/datacore/datamanager/InsertData.php');
require_once($basedir.'/include/lib/datacore/datamanager/ListData.php');
require_once($basedir.'/include/lib/datacore/session/Session.php');

class Datacore{

    public $deletedata;
    public $insertdata;
    public $listdata;
    public $session;
    public $debug;
    public $fstcore;

    function __construct(){
        //INIT CLASS
        $this->deletedata = new DeleteData();
        $this->insertdata = new InsertData();
        $this->listdata = new ListData();
        $this->session = new Session();
        $this->debug = false;
    }

    function __destruct()
    {
        
    }
}
?>
