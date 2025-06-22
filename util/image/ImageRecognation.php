<?php
$basedir = preg_replace('/\\\lib.*|\/lib.*/', '', __DIR__);
require_once($basedir . '/lib/thirdparty/ThirdParty.php');

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

putenv('GOOGLE_APPLICATION_CREDENTIALS='.$basedir.'/data/env/gca.json');

class ImageRecognation{

    public $debug;
    public $fstcore;
    
    function __construct()
    {
        
    }
    
    function recognizeImageLabels(string $imagePath) {
        $returned = array();
        $imageAnnotator = new ImageAnnotatorClient();
        $imageData = file_get_contents($imagePath);
        $response = $imageAnnotator->labelDetection($imageData);
        $labels = $response->getLabelAnnotations();
        if ($labels) {
            foreach ($labels as $label) {
                $returned["description"] = $label->getDescription();
                $returned["score"] = $label->getScore();
                //printf("%s (score: %.2f)\n", $label->getDescription(), $label->getScore());
            }
        } else {
            $returned["description"] = "";
            $returned["score"] = 0;
        }
        $imageAnnotator->close();
        return $returned;
    }

    function __destruct()
    {
        
    }
}

?>
