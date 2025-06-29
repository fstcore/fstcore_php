<?php
$basedir = preg_replace('/\\\lib.*|\/lib.*/', '', __DIR__);
require_once($basedir . '/lib/thirdparty/ThirdParty.php');

class Image{

    public $debug;
    public $fstcore;
    private $ffmpeg_app;
    
    function __construct(){
        $this->ffmpeg_app = $this->fstcore->data;
    }

    function convertImage(string $sourcePath, string $destinationPath, string $outputFormat, int $quality = 90): bool {
        $info = getimagesize($sourcePath);
        $mime = $info['mime'];
    
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }
    
        switch (strtolower($outputFormat)) {
            case 'jpeg':
            case 'jpg':
                return imagejpeg($image, $destinationPath, $quality); // 0 (worst) to 100 (best)
            case 'png':
                $pngQuality = (int)((100 - $quality) / 10); // PNG uses 0 (no compression) to 9
                return imagepng($image, $destinationPath, $pngQuality);
            case 'gif':
                return imagegif($image, $destinationPath);
            default:
                return false;
        }
    }

    function __destruct()
    {
        
    }
}

?>
