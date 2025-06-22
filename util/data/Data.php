<?php
$basedir = preg_replace('/\\\lib.*|\/lib.*/', '', __DIR__);

class Data{

    public $env = "";
    public $temp = "";
    public $tool = "";
    public $init = "";
    public $db = "";
    public $app = "";
    public $session = "";
    public $smtp = "";
    public $bin = "";
    public $ports = "";
    public $ads = "";
    
    public $debug;
    public $fstcore;

    function __construct(){
        $this->env = $GLOBALS["basedir"]."/env/";
        $this->temp = $GLOBALS["basedir"]."/temp/";
        $this->tool = $GLOBALS["basedir"]."/tool/";
        $this->init = $this->fstcore->util->encoder->json_decode($this->fstcore->filesystem->readfile($GLOBALS["basedir"]."/data/env/", "init.json"));
        $this->db = $this->fstcore->util->encoder->json_decode($this->fstcore->filesystem->readfile($GLOBALS["basedir"]."/data/env/", "db.json"));
        $this->app = $this->fstcore->util->encoder->json_decode($this->fstcore->filesystem->readfile($GLOBALS["basedir"]."/data/tool/", "app.json"));
        $this->bin = $this->fstcore->util->encoder->json_decode($this->fstcore->filesystem->readfile($GLOBALS["basedir"]."/data/tool/", "bin.json"));
        $this->ports = $this->fstcore->util->encoder->json_decode($this->fstcore->filesystem->readfile($GLOBALS["basedir"]."/data/tool/", "ports.json"));
        $this->ads = $this->fstcore->util->encoder->json_decode($this->fstcore->filesystem->readfile($GLOBALS["basedir"]."/data/tool/", "ads.json"));
        $this->session = $this->fstcore->util->encoder->json_decode($this->fstcore->filesystem->readfile($GLOBALS["basedir"]."/data/tool/", "session.json"));
        $this->smtp = $this->fstcore->util->encoder->json_decode($this->fstcore->filesystem->readfile($GLOBALS["basedir"]."/data/tool/", "smtp.json"));
    }

    function __destruct()
    {
        
    }
}

?>
