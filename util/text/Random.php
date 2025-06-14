<?php

class Random
{

    public $debug;
    public $fstcore;

    function __construct()
    {

    }

    private function random($array)
    {
        return $array[array_rand($array)];
    }

    public function random_number($length)
    {
        $returned = '';
        $number = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        for ($i = 0; $i < $length; $i++) {
            $returned .= self::random($number);
        }
        return $returned;
    }

    public function random_char_lower($length)
    {
        $returned = '';
        $char_lower = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        for ($i = 0; $i < $length; $i++) {
            $returned .= self::random($char_lower);
        }
        return $returned;
    }

    public function random_char_upper($length)
    {
        $returned = '';
        $char_upper = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        for ($i = 0; $i < $length; $i++) {
            $returned .= self::random($char_upper);
        }
        return $returned;
    }

    public function random_symbol($length)
    {
        $returned = '';
        $symbol = array('`', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '=', '+', '[', '{', ']', '}', ';', ':', '\'', '"', '\\', '|', ',', '<', '.', '>', '/', '?');
        for ($i = 0; $i < $length; $i++) {
            $returned .= self::random($symbol);
        }
        return $returned;
    }

    public function password($type=null, $length)
    {
        switch ($type) {
            case 'int':
                return self::random_number($length);
                break;
            case 'char_lower':
                return self::random_char_lower($length);
                break;
            case 'char_upper':
                return self::random_char_upper($length);
                break;
            case 'char_int':
                return self::random_char_lower($length - 3) . self::random_number(3);
                break;
            case 'random':
                return self::random_char_lower($length - 6) . self::random_char_upper(2) . self::random_number(1) . self::random_symbol(3);
                break;
            default:
                return self::random_char_lower($length - 6) . self::random_char_upper(2) . self::random_number(1) . self::random_symbol(3);
                break;
        }
    }

    function __destruct()
    {
        
    }    
}

?>
