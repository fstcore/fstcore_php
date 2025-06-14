<?php

class Text
{

    public $debug;
    public $fstcore;
    
    function __construct()
    {

    }

    public function trim($str)
    {
        return preg_replace('/\s+|\t+|\r+|\n+/i', '', $str);
    }

    public function split($expl, $str)
    {
        return explode($expl, $str);
    }

    public function removestr($remove, $str)
    {
        return preg_replace('/' . $remove . '/i', '', $str);
    }

    public function regex_replace($pattern, $replacement, $str)
    {
        return preg_replace($pattern, $replacement, $str);
    }

    public function regex_replace_all($pattern, $replacement, $str)
    {
        return preg_replace($pattern, $replacement, $str);
    }

    public function regex_match($pattern, $str)
    {
        if (preg_match_all($pattern, $str)) {
            return true;
        } else {
            return false;
        }
    }

    public function is_existonarray($r, $array){
        if(in_array($r, $array)){
            return true;
        }else{
            return false;
        }
    }

    public function is_existkeyonarray($r, $array){
        if(array_key_exists($r, $array)){
            return true;
        }else{
            return false;
        }
    }

    public function is_existonstr($r, $str)
    {
        if (strpos($str, $r)) {
            return true;
        } else {
            return false;
        }
    }

    function __destruct()
    {
    }    
}

?>
