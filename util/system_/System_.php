<?php

class System{

    public $debug;
    public $fstcore;
    
    function __construct()
    {
        
    }

    public function get_arch(){
        return php_uname('m');
    }

    public function get_os(){
        $os = php_uname('s');
        if($os == 'Windows'){
            return 'Windows';
        }else if($os == 'Darwin'){
            return 'macOS';
        }else if($os == 'Linux'){
            return 'Linux';
        }else if($os == 'Solaris'){
            return 'Solaris';
        }else{
            return null;
        }
    }

    public function get_os_full(){
        return php_uname('s'). ' '.php_uname('r').' '.php_uname('v').' '.php_uname('m');
    }

    public function get_computername(){
        return gethostname();
    }

    function __destruct()
    {
        
    }
}

?>
