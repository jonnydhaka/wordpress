<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
/*

 **************************************************************************

Plugin Name:  Combine VC Elementor CSS
Description:  Combine VC Elementor CSS.
Plugin URI:   https://smartdatasoft.com/
Version:      1.0
Author:       Smartdatasoft
Author URI:   https://smartdatasoft.com/
Text Domain: combine-vc-ele-css
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html

 **************************************************************************
 */
defined('ABSPATH') || die();
define('CVEC_FILE', __FILE__);
define('CVEC_DIR_PATH', plugin_dir_path(CVEC_FILE));
define('CVEC_DIR_URL', plugin_dir_url(CVEC_FILE));
define('CVEC_OPTION_NAME', 'combine_vc_ele_css_post_sc');
define('CSS_EDITOR_NAME', 'custom_css_editor');
define('VERSION', '1.0');

use CVEC\classes\vc\CVEC_Customize;
use CVEC\classes\vc\ele_sc_list;
use CVEC\classes\vc\PBModule;
use CVEC\classes\vc\pb_build_css;
use CVEC\classes\vc\pb_sc_check;
use CVEC\classes\vc\vc_sc_list;

class combine_vc_ele_css
{

    private static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('save_post', [$this, 'save_shortcode_for_combine']);
        add_action('plugins_loaded', [$this, 'init']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_cvec_assets'], 100);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_cvec_assets'], 100);

        add_action('wp_ajax_combine_vc_ele_css_clear_cache', [$this, 'combine_vc_ele_css_clear_cache']);
        add_action('wp_ajax_nopriv_combine_vc_ele_css_clear_cache', [$this, 'combine_vc_ele_css_clear_cache']);
        add_action("redux/extensions/computer_repair_opt/before", [$this, 'redux_register_custom_extension_loader']);

    }

    public function init()
    {
        include __DIR__ . '/classes/class.pb.php';
        include __DIR__ . '/classes/class.vc_sc_list.php';
        include __DIR__ . '/classes/class.ele_sc_list.php';
        include __DIR__ . '/classes/class.pb_sc_check.php';
        include __DIR__ . '/classes/class.pb_build_css.php';
        include __DIR__ . '/classes/class.add_control.php';

        PBModule::init();
        vc_sc_list::init();
        ele_sc_list::init();
        pb_build_css::init();
        CVEC_Customize::init();
    }

    public function save_shortcode_for_combine($post_id)
    {
        $array_list = $array_list_ele = false;
        if (class_exists('Vc_Manager')) {
            $array_list = $this->get_array_list(vc_sc_list::class);
        }
        if (did_action('elementor/loaded')) {
            $array_list_ele = $this->get_array_list(ele_sc_list::class);
        }

        if ($array_list && !empty($array_list)) {
            $get_exist_sc_array = pb_sc_check::Check_sc_exist_in_post($array_list, $post_id);
            if ($get_exist_sc_array) {
                update_post_meta($post_id, CVEC_OPTION_NAME, $get_exist_sc_array);
                pb_build_css::pb_build_css_assets_css($post_id);
            } else {
                pb_build_css::pb_build_css_remove_css($post_id, true);
                update_post_meta($post_id, CVEC_OPTION_NAME, '');
            }
        }
        if ($array_list_ele && !empty($array_list_ele)) {
            $get_exist_sc_array = pb_sc_check::Check_ele_sc_exist_in_post($array_list_ele, $post_id);
            if ($get_exist_sc_array) {
                update_post_meta($post_id, CVEC_OPTION_NAME, $get_exist_sc_array);
                pb_build_css::pb_build_css_assets_css($post_id);
            } else {
                pb_build_css::pb_build_css_remove_css($post_id, true);
                update_post_meta($post_id, CVEC_OPTION_NAME, '');
            }
        }
    }

    public function get_array_list($module)
    {
        $_array = $module::get_pb_sc_array_list();
        return $_array;
    }

    public function enqueue_cvec_assets()
    {
        global $post;
        if (!isset($post) || empty($post)) {
            return;
        }
        $post_meta_array = get_post_meta($post->ID, CVEC_OPTION_NAME, true);
        if ($post_meta_array == '') {
            $this->save_shortcode_for_combine($post->ID);
        }
        $get_pb_build_css_array = pb_build_css::pb_get_css_assets_css();
        if ($get_pb_build_css_array && isset($get_pb_build_css_array['return_url'])) {
            wp_enqueue_style('combine-vc-ele-css', $get_pb_build_css_array['return_url'], null, $get_pb_build_css_array['return_url_version']);
        }
        if ($get_pb_build_css_array && isset($get_pb_build_css_array['return_url_custom'])) {
            wp_enqueue_style('combine-vc-ele-css-custom', $get_pb_build_css_array['return_url_custom'], null, $get_pb_build_css_array['return_url_custom_version']);
        }

    }

    public function admin_enqueue_cvec_assets()
    {
        wp_enqueue_script('combine-admin', plugins_url('assets/js/combine-admin.js', __FILE__), array('jquery'), '', true);
        wp_localize_script('combine-admin', 'combine_vc_ele_object', array('ajax_url' => esc_url(admin_url('admin-ajax.php'))));

    }

    public function combine_vc_ele_css_clear_cache()
    {
        delete_post_meta_by_key(CVEC_OPTION_NAME);
        $path = pb_build_css::$targetdircss . '*';
        foreach (glob($path) as $file_path) {
            unlink($file_path);
        }
        echo json_encode(array('status' => 'success'));
        exit();
    }

    public static function redux_register_custom_extension_loader($ReduxFramework)
    {

        if (!class_exists('Redux')) {
            return;
        }

        $path = plugin_dir_path(__FILE__) . 'classes/cvec_button/';
        $folders = scandir($path, 1);
        foreach ($folders as $folder) {
            if ($folder === '.' or $folder === '..' or !is_dir($path . $folder)) {
            } else {
                $extension_class = 'ReduxFramework_Extension_' . $folder;
                if (!class_exists($extension_class)) {
                    // In case you wanted override your override, hah.
                    $class_file = $path . 'extension_' . $folder . '.php';
                    $class_file = apply_filters('redux/extension/' . $ReduxFramework->args['opt_name'] . '/' . $folder, $class_file);
                    if ($class_file) {
                        require_once $class_file;
                    }
                }

                if (!isset($ReduxFramework->extensions[$folder])) {
                    $ReduxFramework->extensions[$folder] = new $extension_class($ReduxFramework);
                }

            }

        }
    }

}

combine_vc_ele_css::instance();
