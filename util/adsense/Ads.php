<?php
require_once($basedir.'/include/lib/util/encoder/Encoder.php');
require_once($basedir.'/include/lib/util/filesystem/FileSystem.php');
require_once($basedir.'/include/lib/util/text/Text.php');

class Ads
{

    private $ad_name = null;
    private $ad_json = null;
    public $debug;
    public $fstcore;

    function __construct()
    {
        //$this->ad_json = $this->encoder->jsonn_decode($this->fs->readfile($GLOBALS['basedir'].'/data/env/', 'ads.json'));
    }

    public function set_ad($name)
    {
        $this->ad_name = $name;
    }

    public function get_js()
    {
        return $this->ad_json[$this->ad_name]['js'];
    }

    public function get_ad_unit($type)
    {
        return $this->ad_json[$this->ad_name]['ad_unit'][$type];
    }

    function __destruct()
    {
    }
    
}
