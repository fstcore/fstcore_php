<?php

class Proccess{

    public $debug;
    public $fstcore;

    function __construct()
    {
        
    }
    
    function execute($cmd) {
        if(function_exists('system')) { 		
            @ob_start(); 		
            @system($cmd); 		
            $buff = @ob_get_contents(); 		
            @ob_end_clean(); 		
            return $buff; 	
        } elseif(function_exists('exec')) { 		
            @exec($cmd,$results); 		
            $buff = ""; 		
            foreach($results as $result) { 			
                $buff .= $result; 		
            } return $buff; 	
        } elseif(function_exists('passthru')) { 		
            @ob_start(); 		
            @passthru($cmd); 		
            $buff = @ob_get_contents(); 		
            @ob_end_clean(); 		
            return $buff; 	
        } elseif(function_exists('shell_exec')) { 		
            $buff = @shell_exec($cmd); 		
            return $buff; 	
        } 
    }

    public function cmd($command){
        return self::execute($command);
    }

    function __destruct()
    {
        
    }
}

?>