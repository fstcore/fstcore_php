<?php
session_start();

class Session{

    function __construct($val=null)
    {

    }

    public function add_array($keys, $values){
        for($i=0;$i<count($keys);$i++){
            $_SESSION[$keys[$i]] = $values[$i];
        }
    }

    public function add($session_key, $session_data){
        $_SESSION[$session_key] = $session_data;
    }

    public function replace_array($keys, $values){
        for($i=0;$i<count($keys);$i++){
            $_SESSION[$keys[$i]] = $values[$i];
        }
    }

    public function replace($key, $value){
        $_SESSION[$key] = $value;
    }

    public function remove($key){
        if(isset($_SESSION[$key])){
            unset($_SESSION[$key]);
        }
    }

    public function destroy(){
        foreach ($_SESSION as $session){
            if(isset($session)){
                unset($session);
            }
        }
        session_destroy();
    }

    public function check_array($keys){
        foreach ($keys as $key){
            if(isset($_SESSION[$key]) && !empty($_SESSION[$key])){
            }else{
                return false;
            }
        }
        return true;
    }

    public function check($key){
        if(isset($_SESSION[$key]) && !empty($_SESSION[$key])){
            return true;
        }else{
            return false;
        }
    }

    public function get(){
        return $_SESSION;
    }

    function __destruct(){
        
    }

}

?>