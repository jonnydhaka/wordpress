<?php
namespace smartimageoverlay\process\images;

use smartimageoverlay\process\siooptionlist;

class imagewatermark extends imageModule
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
        $watermarkFile = __DIR__ . "/img/star.png"; // 133 x 133
        $image_2 = imagecreatefrompng($watermarkFile);
        $x = imagesx($sio_image);
        $y = imagesy($sio_image);
        $sx = imagesx($image_2);
        $sy = imagesy($image_2);
        $new_x = $new_y = 0;
        if ($x > $sx && $y > $sy) {
            $new_x = (($x - $sx) / 2);
            $new_y = (($y - $sy) / 2);
            //imagecopy($sio_image, $image_2, $new_x, $new_y, 0, 0, $sx, $sy);
        }
        $final_img = imagecreatetruecolor($x, $y);
        imagealphablending($final_img, true);
        imagesavealpha($final_img, true);
        imagecopy($final_img, $sio_image, $this->image_border_width, $this->image_border_width, $this->image_border_width, $this->image_border_width, $x - ($this->image_border_width * 2), $y - ($this->image_border_width * 2));
        imagecopy($final_img, $image_2, $new_x, $new_y, 0, 0, $sx, $sy);
        return $final_img;
    }
}

// $stamp = imagecreatefrompng('stamp.png');
// $im = imagecreatefromjpeg('photo.jpg');
// $save_watermark_photo_address = 'watermark_photo.jpg';

// // Set the margins for the stamp and get the height/width of the stamp image

// $marge_right = 10;
// $marge_bottom = 10;
// $sx = imagesx($stamp);
// $sy = imagesy($stamp);

// // Copy the stamp image onto our photo using the margin offsets and the photo
// // width to calculate positioning of the stamp.

// imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

// // Output and free memory
// // header('Content-type: image/png');

// imagejpeg($im, $save_watermark_photo_address, 80);
// imagedestroy($im);
