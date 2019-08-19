<?php
namespace smartimageoverlay\process\images;

use smartimageoverlay\process\siooptionlist;

class imagecustom extends imageModule
{
    use siooptionlist {
        siooptionlist::__construct as private __tConstruct;
    }
    public $constent_array = array();
    public function __construct()
    {
        $this->__tConstruct();
        if (!empty($this->image_loop)) {
            $this->constent_array = $this->image_loop;
        }
    }

    public function smartimageoverlay_jpeg_image_filter($sio_path)
    {
        $image = imagecreatefromjpeg($sio_path);
        $image = $this->smartimageoverlay_image_filter($image);
        imagejpeg($image, $sio_path);
        imagedestroy($image);
    }

    public function smartimageoverlay_png_image_filter($sio_path)
    {
        if (is_file($sio_path) && mime_content_type($sio_path) == 'image/png') {
            $image = imagecreatefrompng($sio_path);
            $image = $this->smartimageoverlay_image_filter($image);
            imagepng($image, $sio_path);
            imagedestroy($image);
        } else {
            if ($this->debug) {
                echo '<br>';
                echo $sio_path . " Not Png Type";
                echo '<br>';
            }
        }
    }

    public function smartimageoverlay_gif_image_filter($sio_path)
    {
        $image = imagecreatefromgif($sio_path);
        $image = $this->smartimageoverlay_image_filter($image);
        imagegif($image, $sio_path);
        imagedestroy($image);
    }

    public function smartimageoverlay_image_filter($sio_image)
    {
        foreach ($this->constent_array as $key => $value) {
            if (is_array($value)) {
                $arrayfinal = array();
                $arrayfinal[] = $sio_image;
                $arrayfinal[] = constant($key);
                foreach ($value as $childkey => $val) {
                    $arrayfinal[] = $val;
                }
                call_user_func_array("imagefilter", $arrayfinal);
            } else {
                imagefilter($sio_image, constant($key));
            }
        }
        return $sio_image;
    }

}
