<?php
namespace smartimageoverlay\process;

trait siooptionlist
{
    public $test = '';
    public $debug = false; // For  dibuging
    public $image_filter = 'textwatermark'; // Demo Image Type:- gray,blur,custom,watermark,textwatermark
    public $loop_count = false; //loop size Number Value/false
    public $image_watermark_text = "DEMO"; //textwatermark . Show This text as watermarker
    public $image_border_width = 0; //watermark .Show Border watermarker
    public $image_watermark_text_repater = true; //textwatermark. true/false . repeat text
    public $image_watermark_text_repater_height = 0; //textwatermark. true/false . repeat text custbox width
    public $image_watermark_text_repater_width = 0; //textwatermark. true/false . repeat text custbox height
    public $font = __DIR__ . '/fonts/myfont.ttf'; //textwatermark, Font family
    public $font_size = 20; //textwatermark, Font size
    public $font_color = array('0' => 250, '1' => 5, '2' => 5); //textwatermark, Font Color
    public $font_angel = -45; //textwatermark, Font View Angle
    public $sio_rootdir = ABSPATH; // ROOT DIR
    public $sio_contentdir_target = 'smartimageoverlay/'; // terget dir,zip and image folder unside this.
    public $sio_contentdir_name = 'uploadsio'; //  zip and image folder name.
    public $sio_overlay_min_width_range = '100'; //  overlay image if width is bigger then this value.
    public $sio_overlay_min_height_range = '100'; //  overlay image if height is bigger then this value..
    public $image_loop = array(
        'IMG_FILTER_GRAYSCALE' => '',
        //'IMG_FILTER_COLORIZE' => array('0' => 100, '1' => 80, '2' => 0, '3' => 0),
        'IMG_FILTER_SMOOTH' => array('0' => 99),
        'IMG_FILTER_BRIGHTNESS' => array('0' => 10),
    ); //custom.  this is use GD lib IMG_FILTER

    //  filter for change trait defult value.
    //  EXAMPLE:----------------------------
    // function example_callback($example)
    // {
    //     $example['image_watermark_text'] = 'Smartdatasoft';
    //     $example['sio_contentdir_name'] = 'smartdatasoft';
    //     $example['image_watermark_text_repater_height'] = 70;
    //     $example['image_watermark_text_repater_width'] = 70;
    //     return $example;
    // }
    // Filter Calling.
    //add_filter('smartimageoverlay_option_hook', 'example_callback');
    //  END EXAMPLE:------------------------

    public function __construct()
    {
        $array = array();
        $newarray = apply_filters('smartimageoverlay_option_hook', $array);
        if (!empty($newarray)) {
            foreach ($newarray as $key => $value) {
                $this->{$key} = $value;
                echo $this->{$key};
            }
        }
    }

}
