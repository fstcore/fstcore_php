<?php

class Data{

    public $init = "data/env/init.json";
    public $db = "data/env/db.json";
    public $app = "data/tool/app.json";
    public $session = "data/tool/session.json";
    public $smtp = "data/tool/smtp.json";
    public $bin = "data/tool/bin.json";
    public $ports = "data/tool/ports.json";
    public $ads = "data/tool/ads.json";
    
    public $debug;
    public $fstcore;

    function __construct(){
        $this->init = $this->fstcore->filesystem->readfile($this->init);
        $this->db = $this->fstcore->filesystem->readfile($this->db);
        $this->app = $this->fstcore->filesystem->readfile($this->app);
        $this->bin = $this->fstcore->filesystem->readfile($this->bin);
        $this->ports = $this->fstcore->filesystem->readfile($this->ports);
        $this->ads = $this->fstcore->filesystem->readfile($this->ads);
        $this->session = $this->fstcore->filesystem->readfile($this->session);
        $this->smtp = $this->fstcore->filesystem->readfile($this->smtp);
    }

    function __destruct()
    {
        
    }
}

?>
