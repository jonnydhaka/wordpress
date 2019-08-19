<?php
namespace smartimageoverlay\process\images;

use smartimageoverlay\process\siooptionlist;

class imagetextwatermark extends imageModule
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
        $width = imagesx($sio_image);
        $height = imagesy($sio_image);
        if ($this->image_watermark_text_repater) {
            $final_img = imagecreatetruecolor($width, $height);
            imagecopyresampled($final_img, $sio_image, 0, 0, 0, 0, $width, $height, $width, $height);
            $arrayfinal = array();
            $arrayfinal[] = $sio_image;
            foreach ($this->font_color as $childkey => $val) {
                $arrayfinal[] = $val;
            }
            $black = call_user_func_array("imagecolorallocate", $arrayfinal);
            $bbox = imagettfbbox($this->font_size, 0, $this->font, $this->image_watermark_text);
            $x = $bbox[0] + (imagesx($sio_image) / 2) - ($bbox[4] / 2) + 10;
            $y = $bbox[1] + (imagesy($sio_image) / 2) - ($bbox[5] / 2) - 5;
            $text_height = -($bbox[5] - 5);
            $text_width = $bbox[4] + 10;
            if ($this->image_watermark_text_repater_height > 0 && $this->image_watermark_text_repater_width > 0) {
                $loop_img = ceil($height / $this->image_watermark_text_repater_height);
                $loop_img_wid = ceil($width / $this->image_watermark_text_repater_width);
            } else {
                $loop_img = ceil($height / $text_height);
                $loop_img_wid = ceil($width / $text_width);
            }
            $distance_img = $height / $text_height;
            $distance_img_wid = $width / $text_width;
            for ($j = 0; $j < $loop_img_wid; $j++) {
                for ($i = 0; $i < $loop_img; $i++) {
                    if ($this->image_watermark_text_repater_height > 0 && $this->image_watermark_text_repater_width > 0) {
                        imagettftext($final_img, $this->font_size, $this->font_angel, $this->image_watermark_text_repater_height * $j, $this->image_watermark_text_repater_width * $i, $black, $this->font, $this->image_watermark_text);
                    } else {
                        imagettftext($final_img, $this->font_size, $this->font_angel, ($distance_img_wid + $text_width) * $j, ($distance_img_wid + $text_height) * $i, $black, $this->font, $this->image_watermark_text);
                    }
                }
            }
        } else {
            $final_img = imagecreatetruecolor($width, $height);
            imagecopyresampled($final_img, $sio_image, 0, 0, 0, 0, $width, $height, $width, $height);
            $black = imagecolorallocate($final_img, 255, 0, 0);
            $bbox = imagettfbbox($this->font_size, 0, $this->font, $this->image_watermark_text);
            $x = $bbox[0] + (imagesx($sio_image) / 2) - ($bbox[4] / 2) + 10;
            $y = $bbox[1] + (imagesy($sio_image) / 2) - ($bbox[5] / 2) - 5;
            imagettftext($final_img, $this->font_size, 0, $x, $y, $black, $this->font, $this->image_watermark_text);
        }
        return $final_img;

    }
}
