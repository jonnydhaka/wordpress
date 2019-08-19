<?php
// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// error_reporting(-1);
/*

 **************************************************************************

Plugin Name:  Smart Image Overlay
Description:  Regenerate the Image with demo sticker.
Plugin URI:   https://example.com/
Version:      1.0
Author:       example
Author URI:   https://example.com/
Text Domain: smartimageoverlay
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html

 **************************************************************************
 */
if (!defined('WP_CONTENT_DIR')) {
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
}
if (!defined('SIO_WXR_VERSION')) {
    define('SIO_WXR_VERSION', '1.2');
}

require 'include/class.imageprocess.php';
require 'include/class.zipprocess.php';
require 'include/class.copytodir.php';
require 'include/class.export.php';
require_once 'include/siooptions.php';

use smartimageoverlay\process\exportprocess as exportprocess;
use smartimageoverlay\process\imagecopy as imagecopy;
use smartimageoverlay\process\imageprocess as imageprocess;
use smartimageoverlay\process\zipprocess as zipprocess;

class smartimageOverlay
{
    use smartimageoverlay\process\siooptionlist {
        smartimageoverlay\process\siooptionlist::__construct as private __tConstruct;
    }

    public $sio_version = '1.0';

    public $sio_capability = 'manage_options';

    public $sio_media_array = array();

    public $show_array = false;

    public $show_image = false;

    function __construct()
    {
        $this->__tConstruct();
        add_action('admin_menu', array($this, 'smartimageoverlay_add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'smartimageoverlay_admin_enqueue_scripts_func'));
        add_action('wp_ajax_smart_image_overlay_generate', array($this, 'smartimageoverlay_smart_image_overlay_generate'));
        add_action('wp_ajax_nopriv_smart_image_overlay_generate', array($this, 'smartimageoverlay_smart_image_overlay_generate'));
        add_action('wp_ajax_smart_image_overlay_download', array($this, 'smartimageoverlay_smart_image_overlay_download'));
        add_action('wp_ajax_nopriv_smart_image_overlay_download', array($this, 'smartimageoverlay_smart_image_overlay_download'));

    }

    function smartimageoverlay_admin_enqueue_scripts_func()
    {
        if (!wp_script_is('bootstrap', 'enqueued')) {
            wp_enqueue_script('bootstrap', plugins_url('bootstarp/js/bootstrap.min.js', __FILE__));
        }
        if (!wp_style_is('bootstrap', 'enqueued')) {
            wp_enqueue_style('bootstrap', plugins_url('bootstarp/css/bootstrap.min.css', __FILE__));
        }

        wp_enqueue_style('smartimageoverlay_custom_style', plugins_url('css/smartimageoverlay.css', __FILE__));
        wp_enqueue_script('smartimageoverlay_custom_script', plugins_url('js/smartimageoverlay_custom.js', __FILE__));
        wp_localize_script('smartimageoverlay_custom_script', 'smartimageoverlay_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    }

    function smartimageoverlay_add_admin_menu()
    {
        add_management_page("Smart Image Overlay", "Smart Image Overlay", $this->sio_capability, "smart-image-overlay", array($this, "smartimageoverlay_admin_menu_func"));
    }

    function smartimageoverlay_admin_menu_func()
    {
        $imageprocess = new imageprocess();
        $this->sio_media_array = $imageprocess->smartimageoverlay_get_media();
        $sio_count = count($this->sio_media_array);
        include 'views/index.php';
    }

    function smartimageoverlay_smart_image_overlay_generate()
    {
        $imageprocess = new imageprocess();
        $this->sio_media_array = $imageprocess->smartimageoverlay_get_media();
        if ($this->show_array) {
            echo '<pre>', print_r($this->sio_media_array);
        }
        if ($this->show_image) {
            foreach ($this->sio_media_array as $image) {
                echo '<div class="col-md-2"><img src="' . $image['url'] . '"></div>';
            }
        }
        $new_image = array();
        $new_image[] = $this->sio_media_array[$_POST['key']];
        $filepathinfo = pathinfo($new_image[0]['url']);
        $imagecopy = new imagecopy();
        $imagecopy->smartimageoverlay_copy_image_to_dir($new_image);
        $resultarray['massage'] = '"' . $filepathinfo['basename'] . '" Demo Image Generated.';
        $resultarray['key'] = $_POST['key'];
        echo json_encode($resultarray);
        exit();
    }

    function smartimageoverlay_smart_image_overlay_download()
    {
        $filename = $this->sio_contentdir_name . ".zip";
        $zipprocess = new zipprocess($this->sio_rootdir . $this->sio_contentdir_target, $filename, '/' . $this->sio_contentdir_name);
        $getResult = $zipprocess->smartimageoverlay_image_zip_build();
        echo json_encode($getResult);
        exit();
    }

    function smartimageoverlay_download()
    {
        $exportprocess = new exportprocess($_POST['sio-xml-downloader-input']);
        exit();
    }

}
function smartimageoverlaycall()
{
    $smartimageOverlay = new smartimageOverlay;
    if (isset($_POST['sio-xml-downloader'])) {
        $smartimageOverlay->smartimageoverlay_download();
    }
    return $smartimageOverlay;
}

/**
 * Initialize this plugin once all other plugins have finished loading.
 */
add_action('init', 'smartimageoverlaycall');
