<?php
$basedir = preg_replace('/\\\lib.*|\/lib.*/', '', __DIR__);
require_once($basedir . '/lib/thirdparty/ThirdParty.php');
require_once($basedir . '/lib/util/filesystem_/FileSystem_.php');
//require("phpmailer/PHPMailer.php");
//require("phpmailer/SMTP.php");
//require("phpmailer/Exception.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Smtp{

    private $encoder;
    private $fs;
    private $smtp_json;
    private $smtp;
    private $host;
    private $port;
    private $username;
    private $password;
    private $from;
    private $to;
    private $ssl;
    private $title;
    private $message;
    private $file;
    public $debug;
    public $fstcore;

    function __construct(){
        $this->encoder = new Encoder();
        $this->fs = new FileSystem();
        $this->smtp_json = $this->encoder->jsonn_decode($this->fs->readfile($GLOBALS['basedir'].'/data/env/', 'smtp.json'));
    }

    public function set_smtp($smtp){
        $this->smtp = $smtp;
    }

    public function set_title($title){
        $this->title = $title;
    }

    public function set_message($message){
        $this->message = $message;
    }

    public function set_attach($file){
        $this->file = $file;
    }

    public function set_host($host){
        $this->host = $host;
    }

    public function set_port($port){
        $this->port = $port;
    }
    
    public function set_username($username){
        $this->username = $username;
    }

    public function set_password($password){
        $this->password = $password;
    }

    public function set_ssl($ssl){
        $this->ssl = $ssl;
    }

    public function send(){
        $data_array = array();
        if(!empty($smtp)){
            $this->host = $this->smtp_json[$this->smtp]['host'];
            $this->port = $this->smtp_json[$this->smtp]['port'];
        }
        if($this->ssl){
            $this->ssl = 'ssl';
        }
        else{
            $this->ssl = '';
        }
        $smtp = new PHPMailer(true);
        $from = $this->smtp_json[$this->smtp]['from'];
        try{
            $smtp->IsSMTP(); // telling the class to use SMTP
            $smtp->SMTPAuth = true; // enable SMTP authentication
            $smtp->SMTPSecure = $this->ssl; // sets the prefix to the servier
            $smtp->Host = $this->host; // sets GMAIL as the SMTP server
            $smtp->Port = $this->port; // set the SMTP port for the GMAIL server
            $smtp->Username = $this->username; // GMAIL username
            $smtp->Password = $this->password; // GMAIL password
            $smtp->AddAddress($this->to, 'You');
            $smtp->SetFrom($this->from, 'SMTPER');
            $smtp->Subject = $this->title;
            $smtp->Body = $this->message;
            $smtp->Send();
            $data_array['host'] = $this->host;
            $data_array['port'] = $this->port;
            $data_array['username'] = $this->username;
            $data_array['password'] = $this->password;
            $data_array['ssl'] = $this->ssl;
            $data_array['from'] = $this->from;
            $data_array['to'] = $this->to;
            $data_array['title'] = $this->title;
            $data_array['message'] = $this->message;
            $data_array['status'] = 'sent';
            return $data_array;
        }
        catch(Exception $e){
            $e->getMessage();
            $data_array['host'] = $this->host;
            $data_array['port'] = $this->port;
            $data_array['username'] = $this->username;
            $data_array['password'] = $this->password;
            $data_array['ssl'] = $this->ssl;
            $data_array['from'] = $this->from;
            $data_array['to'] = $this->to;
            $data_array['title'] = $this->title;
            $data_array['message'] = $this->message;
            $data_array['status'] = 'failed';
            return $data_array;
        }
    }

    function __destruct(){
        
    }

}

?>