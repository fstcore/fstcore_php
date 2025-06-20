<?php
require_once($basedir . '/include/lib/util/3rdparty.php');

class Audio
{

    public $debug;
    public $fstcore;

    function __construct()
    {
    }

    function convertAudio(string $sourceFile, string $targetFile, string $format = 'mp3') {
        $ffmpegCmd = sprintf(
            'ffmpeg -i %s -y %s',
            escapeshellarg($sourceFile),
            escapeshellarg($targetFile)
        );    
        exec($ffmpegCmd, $output, $returnCode);
        return $returnCode === 0;
    }

    function convertToMkv(string $sourceFile, string $targetFile) {
        $ffmpegCmd = sprintf(
            'ffmpeg -i %s -c:v copy -c:a copy -y %s',
            escapeshellarg($sourceFile),
            escapeshellarg($targetFile)
        );
    
        exec($ffmpegCmd, $output, $returnCode);
        return $returnCode === 0;
    }

    public function audio_converter($audio, $format, $destination_output)
    {
        $ffmpeg = FFMpeg\FFMpeg::create();
        $audio_format = null;
        $video = $ffmpeg->open($audio);
        switch ($format) {
            case "aac":
                $audio_format = new FFMpeg\Format\Audio\Aac();
                $video->save($audio_format, $destination_output."/audio.".$format);
                break;
            case "mp3":
                $audio_format = new FFMpeg\Format\Audio\Mp3();
                $video->save($audio_format, $destination_output."/audio.".$format);
                break;
            case "mp4":
                $this->convertAudio($audio, $destination_output."/audio.".$format, $format);
                break;
            case "mkv":
                $this->convertToMkv($audio, $destination_output."/audio.".$format);
                break;
            case "wav":
                $audio_format = new FFMpeg\Format\Audio\Wav();
                $video->save($audio_format, $destination_output."/audio.".$format);
                break;
            case "flac":
                $audio_format = new FFMpeg\Format\Audio\Flac();
                $video->save($audio_format, $destination_output."/audio.".$format);
                break;
            case "vorbis":
                $audio_format = new FFMpeg\Format\Audio\Vorbis();
                $video->save($audio_format, $destination_output."/audio.".$format);
                break;
            default:
                break;
        }
    }

    function __destruct()
    {
    }
}
