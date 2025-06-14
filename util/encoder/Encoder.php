<?php

class Encoder{

    public $debug;
    public $fstcore;

    function __construct()
    {
        
    }

    public function base64_encode($str){
        return base64_encode($str);
    }

    public function base64_decode($str){
        return base64_decode($str);
    }

    public function jsonn_encode($str){
        return json_encode($str);
    }

    public function jsonn_decode($str){
        return json_decode($str);
    }

    public function md5($str){
        return md5($str);
    }

    public function sha1($str){
        return sha1($str);
    }

    public function url_encode($str){
        return urlencode($str);
    }

    public function url_decode($str){
        return urldecode($str);
    }

    public function htmlentities_encode($str){
        return htmlentities($str);
    }

    public function html_entity_decode($str){
        return html_entity_decode($str);
    }

    public function htmlspecialchars_encode($str){
        return htmlspecialchars($str);
    }

    public function htmlspecialchars_decode($str){
        return htmlspecialchars_decode($str);
    }

    public function hmac($str, $algo, $key){
        return hash_hmac($algo, $str, $key);
    }

    public function hex_encode($str){
        return hex2bin($str);
    }

    public function hex_decode($str){
        return hexdec($str);
    }
}

?>