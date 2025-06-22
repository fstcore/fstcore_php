<?php
$basedir = preg_replace('/\\\lib.*|\/lib.*/', '', __DIR__);
require_once($basedir . '/lib/thirdparty/ThirdParty.php');

use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionAudio;

class SpeechRecognation
{

    public $debug;
    public $fstcore;

    function __construct()
    {
        putenv("GOOGLE_APPLICATION_CREDENTIALS=" . $this->fstcore->util->data->env . "gca.json");
    }

    public function speech()
    {
        $returned = "";
        $speechClient = new SpeechClient();
        // Path to local audio file
        $audioFile = "speech_" . $this->fstcore->util->text->random() . ".wav";
        $returned["audio_file"] = $this->fstcore->util->data->temp . $audioFile;
        $audioData = file_get_contents($this->fstcore->util->data->temp . $audioFile);
        // Prepare recognition audio and config
        $audio = (new RecognitionAudio())
            ->setContent($audioData);
        $config = (new RecognitionConfig())
            ->setEncoding(RecognitionConfig\AudioEncoding::LINEAR16)
            ->setSampleRateHertz(16000)
            ->setLanguageCode("en-US");
        // Recognize speech
        $response = $speechClient->recognize($config, $audio);
        foreach ($response->getResults() as $result) {
            $returned["transcript"] .= $result->getAlternatives()[0]->getTranscript() . PHP_EOL;
        }
        $speechClient->close();
        return $returned;
    }

    function __destruct()
    {
    }
}
