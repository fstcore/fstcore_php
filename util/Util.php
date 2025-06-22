<?php
$basedir = preg_replace('/\\\lib.*|\/lib.*/', '', __DIR__);
require_once($basedir . '/lib/util/adsense/Ads.php');
require_once($basedir . '/lib/util/audio/Audio.php');
require_once($basedir . '/lib/util/data/Data.php');
require_once($basedir . '/lib/util/database/Database.php');
require_once($basedir . '/lib/util/debug/Debug.php');
require_once($basedir . '/lib/util/encoder/Encoder.php');
require_once($basedir . '/lib/util/filesystem_/FileSystem_.php');
require_once($basedir . '/lib/util/image/Image.php');
require_once($basedir . '/lib/util/net/Host.php');
require_once($basedir . '/lib/util/net/Http.php');
require_once($basedir . '/lib/util/net/Parser.php');
require_once($basedir . '/lib/util/net/Smtp.php');
//require_once($basedir . '/lib/util/net/Socket.php');
require_once($basedir . '/lib/util/net/UserAgent.php');
require_once($basedir . '/lib/util/proccess/Proccess_.php');
require_once($basedir . '/lib/util/proccess/ThreadStart.php');
require_once($basedir . '/lib/util/proccess/ThreadUtility.php');
require_once($basedir . '/lib/util/system_/System_.php');
require_once($basedir . '/lib/util/text/Random.php');
require_once($basedir . '/lib/util/text/Text.php');

class Util
{

    public $ads;
    public $audio;
    public $speech_recognation;
    public $data;
    public $database;
    public $debug_;
    public $encoder;
    public $filesystem;
    public $image;
    public $image_recognation;
    public $host;
    public $http;
    public $parser;
    public $smtp;
    public $socket;
    public $socketcallbackutility;
    public $useragent;
    public $websocket;
    public $proccess;
    public $threadstart;
    public $threadutility;
    public $system_;
    public $random;
    public $text;
    public $debug;
    public $fstcore;

    function __construct()
    {
        //INIT CLASS
        $this->ads = new Ads();
        $this->audio = new Audio();
        $this->speech_recognation = new SpeechRecognation();
        $this->data = new Data();
        $this->database = new Database();
        $this->debug_ = new Debug();
        $this->encoder = new Encoder();
        $this->filesystem = new FileSystem_();
        $this->image = new Image();
        $this->image_recognation = new ImageRecognation();
        $this->host = new Host();
        $this->http = new Http();
        $this->parser = new Parser();
        $this->smtp = new Smtp();
        //$this->socket = new Socket();
        //$this->socketcallbackutility = new SocketCallBackUtility();
        $this->useragent = new UserAgent();
        $this->websocket = new WebSocket();
        $this->proccess = new Proccess();
        $this->threadstart = new ThreadStart();
        $this->threadutility = new ThreadUtility();
        $this->system_ = new System();
        $this->random = new Random();
        $this->text = new Text();

        //SET FSTCORE
        $this->ads->fstcore = $this->fstcore;
        $this->audio->fstcore = $this->fstcore;
        $this->speech_recognation->fstcore = $this->fstcore;
        $this->data->fstcore = $this->fstcore;
        $this->database->fstcore = $this->fstcore;
        $this->debug_->fstcore = $this->fstcore;
        $this->encoder->fstcore = $this->fstcore;
        $this->filesystem->fstcore = $this->fstcore;
        $this->image->fstcore = $this->fstcore;
        $this->image_recognation->fstcore = $this->fstcore;
        $this->host->fstcore = $this->fstcore;
        $this->http->fstcore = $this->fstcore;
        $this->parser->fstcore = $this->fstcore;
        $this->smtp->fstcore = $this->fstcore;
        $this->socket->fstcore = $this->fstcore;
        $this->socketcallbackutility->fstcore = $this->fstcore;
        $this->useragent->fstcore = $this->fstcore;
        $this->websocket->fstcore = $this->fstcore;
        $this->proccess->fstcore = $this->fstcore;
        $this->threadstart->fstcore = $this->fstcore;
        $this->threadutility->fstcore = $this->fstcore;
        $this->system_->fstcore = $this->fstcore;
        $this->random->fstcore = $this->fstcore;
        $this->text->fstcore = $this->fstcore;

        //SET DEBUG
        $this->ads->debug = $this->debug;
        $this->audio->debug = $this->debug;
        $this->speech_recognation->debug = $this->debug;
        $this->data->debug = $this->debug;
        $this->database->debug = $this->debug;
        $this->debug_->debug = $this->debug;
        $this->encoder->debug = $this->debug;
        $this->filesystem->debug = $this->debug;
        $this->image->debug = $this->debug;
        $this->image_recognation->debug = $this->debug;
        $this->host->debug = $this->debug;
        $this->http->debug = $this->debug;
        $this->parser->debug = $this->debug;
        $this->smtp->debug = $this->debug;
        $this->socket->debug = $this->debug;
        $this->socketcallbackutility->debug = $this->debug;
        $this->useragent->debug = $this->debug;
        $this->websocket->debug = $this->debug;
        $this->proccess->debug = $this->debug;
        $this->threadstart->debug = $this->debug;
        $this->threadutility->debug = $this->debug;
        $this->system_->debug = $this->debug;
        $this->random->debug = $this->debug;
        $this->text->debug = $this->debug;
    }

    function __destruct()
    {
    }
}
