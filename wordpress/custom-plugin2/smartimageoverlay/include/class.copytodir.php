<?php
namespace smartimageoverlay\process;

require_once 'siooptions.php';
require_once 'images/class.image.php';
require_once 'images/class.imageblur.php';
require_once 'images/class.imagegray.php';
require_once 'images/class.imagecustom.php';
require_once 'images/class.imagewatermark.php';
require_once 'images/class.imagetextwatermark.php';

use smartimageoverlay\process\images\imageblur as imageblur;
use smartimageoverlay\process\images\imagecustom as imagecustom;
use smartimageoverlay\process\images\imagegray as imagegray;
use smartimageoverlay\process\images\imageModule as imageModule;
use smartimageoverlay\process\images\imagetextwatermark as imagetextwatermark;
use smartimageoverlay\process\images\imagewatermark as imagewatermark;

class imagecopy
{
    use siooptionlist {
        siooptionlist::__construct as private __tConstruct;
    }
    public $total_file = 0;
    public function __construct()
    {
        $this->__tConstruct();
    }

    public function smartimageoverlay_copy_image_to_dir($sio_media_array)
    {
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH . '/wp-admin/includes/file.php';
            WP_Filesystem();
        }
        $i = 0;
        foreach ($sio_media_array as $sio_media_url) {
            if ($this->debug) {
                echo '--------------------------------';
                echo '<br>';
                echo '<br>';
                echo 'File Name: ' . $sio_media_url['url'];
                echo '<br>';
            }
            $sio_media_content = $wp_filesystem->get_contents($sio_media_url['url']);
            $sio_find_path = str_replace(content_url() . '/', '', $sio_media_url['url']);
            $sio_find_path = str_replace('uploads', $this->sio_contentdir_target . $this->sio_contentdir_name, $sio_find_path);
            $sio_path = pathinfo($sio_find_path);
            if (!is_dir($this->sio_rootdir . $sio_path['dirname'])) {
                $this->smartimageoverlay_make_dir($this->sio_rootdir . $sio_path['dirname']);
            }

            if (!file_exists($this->sio_rootdir . $sio_find_path)) {
                if ($wp_filesystem->put_contents($this->sio_rootdir . $sio_find_path, $sio_media_content, FS_CHMOD_FILE)) {
                    if ($this->debug) {
                        echo $this->sio_rootdir . $sio_find_path . ' Copied.';
                        echo '<br>';
                    }
                    $this->total_file++;
                }
            } else {
                if ($this->debug) {
                    echo $this->sio_rootdir . $sio_find_path . 'This Item Already Exist.';
                    echo '<br><br>--------------------------------';
                }
            }
            if ($sio_media_url['width'] > $this->sio_overlay_min_width_range && $sio_media_url['height'] > $this->sio_overlay_min_height_range) {
                $imagemodule = '';
                if ($this->image_filter == 'gray') {
                    $imagemodule = new imagegray();
                }
                if ($this->image_filter == 'blur') {
                    $imagemodule = new imageblur();
                }
                if ($this->image_filter == 'custom') {
                    $imagemodule = new imagecustom();
                }
                if ($this->image_filter == 'watermark') {
                    $imagemodule = new imagewatermark();
                }
                if ($this->image_filter == 'textwatermark') {
                    $imagemodule = new imagetextwatermark();
                }
                if ($imagemodule != '') {
                    $this->smartimageoverlay_run_image_module($imagemodule, $sio_path['extension'], $this->sio_rootdir . $sio_find_path);
                }
            }else{
                
            }

            if (($this->loop_count) && $this->loop_count == $i) {
                exit();
            }
            $i++;
        }
        if ($this->debug) {
            echo $this->total_file . ' Item Copied.';
            echo '<br>';
        }
    }

    public function smartimageoverlay_make_dir($sio_path)
    {
        global $wp_filesystem;
        if ($this->debug) {
            echo $sio_path . ' Not Exist. Need To Create This. Job Start.';
            echo '<br>';
        }
        if (!mkdir($sio_path, 0777, true)) {
            add_action('admin_notices', array($this, 'smartimageoverlay_mkdir_admin_notice_function'));
        } else {
            $wp_filesystem->mkdir($sio_path, 0777, true);
        }
        if ($this->debug) {
            echo $sio_path . ' Created. Finish job';
            echo '<br>';
        }
    }

    public function smartimageoverlay_mkdir_admin_notice_function()
    {
        $class = 'notice notice-error';
        $message = __('An error has occurred.', 'sample-text-domain');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }

    public function smartimageoverlay_run_image_module(imageModule $module, $type, $path)
    {
        if ($type == 'png') {
            $module->smartimageoverlay_png_image_filter($path);
            if ($this->debug) {
                echo $path . 'Png Blur Created.';
                echo '<br><br>--------------------------------';
            }
        } else if ($type == 'jpg') {
            $module->smartimageoverlay_jpeg_image_filter($path);
            if ($this->debug) {
                echo $path . 'Jpeg Blur Created.';
                echo '<br><br>--------------------------------';
            }
        } else if ($type == 'gif') {
            $module->smartimageoverlay_gif_image_filter($path);
            if ($this->debug) {
                echo $path . 'Jpeg Blur Created.';
                echo '<br><br>--------------------------------';
            }
        }
    }

}
