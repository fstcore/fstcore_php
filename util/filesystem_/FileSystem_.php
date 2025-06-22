<?php
$basedir = preg_replace('/\\\lib.*|\/lib.*/', '', __DIR__);
require_once($basedir . '/lib/util/text/Text.php');

class FileSystem_
{
    public $debug;
    public $fstcore;

    function __construct()
    {
        
    }

    public function topath($path)
    {
        return $this->fstcore->util->text->regex_replace_all('/\\|\\\\|\/\//i', '/', $path);
    }

    public function readfile($folder, $file)
    {
        $data = null;
        $filepath = self::topath($folder . '/' . $file);
        $size = filesize($filepath);
        $fopen = @fopen($filepath, 'r+');
        if($fopen){
            try{
                $data = fread($fopen, $size);
            }catch(Exception $e){
            }
            fclose($fopen);
        }else{
        }        
        return $data;
    }

    public function writefile($folder, $file, $data)
    {
        $filepath = self::topath($folder . '/' . $file);
        $fopen = @fopen($filepath, 'w');
        fwrite($fopen, $data);
        fclose($fopen);
    }

    public function appendfile($folder, $file, $data)
    {
        $filepath = self::topath($folder . '/' . $file);
        $fopen = @fopen($filepath, 'a+');
        fwrite($fopen, $data);
        fclose($fopen);
    }

    public function makefolder($folder)
    {
        $filepath = self::topath($folder);
        if (file_exists($filepath) == false) {
            mkdir($filepath);
        }
    }

    public function makefile($folder, $file)
    {
        $filepath = self::topath($folder . '/' . $file);
        if (file_exists($filepath) == false) {
            touch($filepath);
        }
    }

    public function deletefolder($folder)
    {
        $filepath = self::topath($folder);
        if (file_exists($filepath)) {
            rmdir($filepath);
        }
    }

    public function deletefile($folder, $file)
    {
        $filepath = self::topath($folder . '/' . $file);
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    public function renamefolder($folder, $foldernew)
    {
        $from = self::topath($folder);
        $new = self::topath($foldernew);
        @rename($from, $new);
    }

    public function renamefile($folder, $file, $foldernew, $filenew)
    {
        $from = self::topath($folder . '/' . $file);
        $new = self::topath($foldernew . '/' . $filenew);
        @copy($from, $new);
    }

    public function delete_linefile($folder, $file, $r)
    {
        $data = '';
        $filepath = self::topath($folder . '/' . $file);
        $handle = fopen($filepath, "rb");
        while (!feof($handle)) {
            $line = fread($handle, 8192);
            if (($line == $r) == false) {
                $data .= fread($handle, 8192);
            }
        }
        fclose($handle);
        return $data;
    }

    public function replace_linefile($folder, $file, $r, $replacement)
    {
        $data = '';
        $filepath = self::topath($folder . '/' . $file);
        $handle = fopen($filepath, "rb");
        while (!feof($handle)) {
            $line = fread($handle, 8192);
            if (($line == $r)) {
                $data .= $replacement;
            } else {
                $data .= $line;
            }
        }
        fclose($handle);
        return $data;
    }

    public function is_existonfile($folder, $file, $r)
    {
        $returned = false;
        $filepath = self::topath($folder . '/' . $file);
        $handle = fopen($filepath, "rb");
        while (!feof($handle)) {
            $line = fread($handle, 8192);
            if (($line == $r)) {
                $returned = true;
                break;
            }
        }
        fclose($handle);
        return $returned;
    }

    public function filetoarray($folder, $file, $expl)
    {
        $filepath = self::topath($folder . '/' . $file);
        $fopen = @fopen($filepath, 'r+');
        $data = fread($fopen, filesize($filepath));
        fclose($fopen);
        return $this->text->split($expl, $data);
    }
}
