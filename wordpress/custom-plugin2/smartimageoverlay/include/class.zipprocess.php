<?php
namespace smartimageoverlay\process;

use ZipArchive;

class zipprocess
{
    use siooptionlist {
        siooptionlist::__construct as private __tConstruct;
    }

    public $filepath;
    public $filename;
    public $fulltargetpath;
    public $scandir;
    public function __construct($path, $name, $scandirname)
    {
        $this->__tConstruct();
        $this->filepath = $path;
        $this->filename = $name;
        $this->fulltargetpath = $this->filepath . $this->filename;
        $this->scandir = $this->filepath . $scandirname;
    }

    public function smartimageoverlay_image_zip_build()
    {
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if (file_exists($this->fulltargetpath)) {
                unlink($this->fulltargetpath);
            }
            if ($zip->open($this->fulltargetpath, ZipArchive::CREATE) != true) {
                return array('success' => false, 'massage' => 'Could not open archive');
            }
            $files = $this->smartimageoverlay_get_Dir_Contents($this->scandir);
            foreach ($files as $file) {
                if ($this->debug) {
                    echo 'zipprocess Class: file name:- ' . $file;
                    echo '<br>';
                }
                $relativePath = substr($file, strlen($this->scandir));
                if ($this->debug) {
                    echo 'zipprocess Class: relative path name:- ' . $relativePath;
                    echo $relativePath;
                    echo '<br>';
                }
                // Add current file to archive
                $zip->addFile($file, $relativePath);
            }
            $zip->close();
            return array('success' => true, 'massage' => $this->smartimageoverlay_download_url());
        } else {
            return array('success' => false, 'massage' => 'ZipArchive not active.');
        }
    }

    public function smartimageoverlay_download_url()
    {
        $siteurl = get_site_url();
        $siteurl = trim($siteurl, '/');
        return $siteurl . '/' . $this->sio_contentdir_target . $this->filename;
    }

    public function smartimageoverlay_get_Dir_Contents($dir, &$results = array())
    {
        //$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::LEAVES_ONLY);
        //if (!$file->isDir()) {
        //$filePath = $file->getRealPath();
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            $path = realpath($dir . "/" . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                $this->smartimageoverlay_get_Dir_Contents($path, $results);
            }
        }
        return $results;
    }

}
