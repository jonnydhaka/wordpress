<?php
namespace smartimageoverlay\process\images;

use smartimageoverlay\process\siooptionlist;

class imagegray extends imageModule
{
    use siooptionlist {
        siooptionlist::__construct as private __tConstruct;
    }
    public function __construct()
    {
        $this->__tConstruct();
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
        imagefilter($sio_image, IMG_FILTER_GRAYSCALE);
        imagefilter($sio_image, IMG_FILTER_SMOOTH, 99);
        imagefilter($sio_image, IMG_FILTER_BRIGHTNESS, 10);
        return $sio_image;
    }

}
