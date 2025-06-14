<?php

$basedir = preg_replace('/\/include.*/', '', __DIR__);
include($basedir.'/include/FstradeCore.php');

class Fstrade{

    private $fstrade_core;
    public $debug;
    public $fstcore;

    function __construct(){
        $this->fstrade_core = new FstradeCore();
        $this->fstrade_core->debug = $this->debug;
        $this->fstrade_core->fstcore = $this->fstcore;
    }
    
    function open_data($filename)
    {
        $open = @fopen("data/" . $filename, "r+");
        $data = fread($open, filesize($open));
        fclose($data);
        return $data;
    }

    function create_folder($folder){
        mkdir($folder);
        mkdir($folder."/config");
        mkdir($folder."/data");
        mkdir($folder."/data/price");
        mkdir($folder."/log");
        mkdir($folder."/setting");
    }

    public function start($folder){
        self::create_folder($folder);
        $setting_files = scandir($folder."/setting/", SCANDIR_SORT_ASCENDING);
        $this->fstrade_core->folder = $folder;
        $this->fstrade_core->fstrade_opt->folder = $folder;
        while (true) {
        	sleep(60);
            foreach ($setting_files as $filename_) {
                $data_obj = self::open_data($folder."/setting/".$filename_);
                $this->fstrade_core->start($data_obj);
            }
        }
    }

    function __destruct(){

    }
}

?>
