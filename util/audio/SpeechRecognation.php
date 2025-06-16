<?php
require_once($basedir . '/include/lib/util/3rdparty.php');

use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionAudio;

putenv('GOOGLE_APPLICATION_CREDENTIALS='.$basedir.'/data/gca.json');

class SpeechRecognation
{

    public $debug;
    public $fstcore;

    function __construct()
    {
    }

    public function speech()
    {
        $speechClient = new SpeechClient();
        // Path to local audio file
        $audioFile = 'audio.wav';
        $audioData = file_get_contents($audioFile);
        // Prepare recognition audio and config
        $audio = (new RecognitionAudio())
            ->setContent($audioData);
        $config = (new RecognitionConfig())
            ->setEncoding(RecognitionConfig\AudioEncoding::LINEAR16)
            ->setSampleRateHertz(16000)
            ->setLanguageCode('en-US');
        // Recognize speech
        $response = $speechClient->recognize($config, $audio);
        foreach ($response->getResults() as $result) {
            echo 'Transcript: ' . $result->getAlternatives()[0]->getTranscript() . PHP_EOL;
        }
        $speechClient->close();
    }

    function __destruct()
    {
    }
}
