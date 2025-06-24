<?php

$basedir = preg_replace('/\\\lib.*|\/lib.*/', '', __DIR__);
if(file_exists($basedir.'/lib/thirdparty/vendor/autoload.php')){
    exit;
}
require_once($basedir.'/lib/thirdparty/vendor/autoload.php');

?>
