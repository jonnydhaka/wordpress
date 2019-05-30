<?php
/*
Plugin Name: Custom Iconmoon Icon Add
Plugin URI: http://smartdatasoft.com
Description: Add custom Iconmoon Icon.
Author: smartdatasoft
Version: 1.0
Author URI: http://smartdatasoft.com
text-domain: custom-icon-upload
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class iconmoonFontAdd
{
    public $dirpaths = array();
    public $json_file_name = '';
    public $svg_file_name = '';
    public $new_json = array();
    public $custom_icon_upload_font_name = '';
    public $text_file_name = 'fontarray.txt';
    public $folder_name = '';
    public function __construct()
    {
        add_action('admin_menu', array($this, 'custom_icon_upload_menu_add'),12);
        add_action('admin_enqueue_scripts', array($this, 'custom_icon_upload_enqueue'));
        add_action('wp_ajax_custom_icon_upload_add_font', array($this, 'custom_icon_upload_zipped_font'));
        add_action('wp_ajax_custom_icon_upload_delete_font', array($this, 'custom_icon_upload_delete_icon'));
        add_action('init', array($this, 'custom_icon_upload_add_icon'));
        add_action( 'elementor/controls/controls_registered', array( $this, 'custom_icon_upload_return' ), 10, 1);
        add_action( 'wp_print_footer_scripts', array( $this, 'custom_icon_elementor_editor_css' ) );
        $this->dirpaths = wp_upload_dir();
        $this->dirpaths['fonts'] = 'car_custom_font';
        $this->dirpaths['fontdir'] = trailingslashit($this->dirpaths['basedir']) . $this->dirpaths['fonts'];
        $this->dirpaths['fonturl'] = set_url_scheme(trailingslashit($this->dirpaths['baseurl']) . $this->dirpaths['fonts']);
        $this->dirpaths['unzippeddir'] = '';
        $this->dirpaths['unzippedurl'] = '';
        $this->dirpaths['fontsvgdir'] = '';
    }
    public function custom_icon_upload_menu_add()
    {
        if (class_exists('DashboardEssential')) {
            $token=get_option('envato_theme_license_token');
            if($token!='')
            add_submenu_page('envato-theme-license-dashboard', "Icon Add", "Icon Add", 'switch_themes', 'custom-icon-upload', array($this, 'custom_icon_upload_uploader_page'));
        } else {
            $target = 'Custom Iconmoon Icon Add';
            add_menu_page('Custom Iconmoon Icon Add', 'Custom Iconmoon Icon Add', 'manage_options', 'custom-icon-upload', array($this, 'custom_icon_upload_uploader_page'));
        }
    }
    public function custom_icon_upload_enqueue()
    {
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox'); 
        wp_enqueue_media();
        wp_enqueue_style('custom-icon-upload-css', plugin_dir_url(__FILE__) . '/icon-moon-font-add.css', '', null);
        wp_enqueue_script('custom-icon-upload-js', plugin_dir_url(__FILE__) . '/icon-moon-font-add.js', array('jquery'), '', true);
        wp_localize_script('jquery', 'ajax_custom_icon_add', array(
            'custom_icon_upload_add_font' => wp_create_nonce('custom-icon-upload-nonce'),
            'custom_icon_upload_delete_font' => wp_create_nonce('custom-icon-upload-delete-font'),
            'custom_icon_upload_zip_upload_text' =>  apply_filters( 'custom_icon_upload_zip_upload_text', esc_html__('Insert Font IconMoon Zip File','custom-icon-upload') ),
            'custom_icon_upload_thickbox_title' =>  apply_filters( 'custom_icon_upload_thickbox_title', esc_html__('Something Wrong','custom-icon-upload') ),
            'custom_icon_upload_remove_confirm_text' => apply_filters( 'custom_icon_upload_remove_confirm_text', esc_html__('Are You Sure?','custom-icon-upload') ),
            'ajax_url' => admin_url('admin-ajax.php'),
            'copied_text' =>apply_filters( 'custom_icon_upload_zip_after_font_copy_text', esc_html__('Copied!','custom-icon-upload') ),
        ));
    }
    public function custom_icon_upload_uploader_page()
    {
        do_action('add_icon_tab_menu_for_dashboard','icon');
        ?>
        <div style="display:none" id="custom-icon-uploder-modal-content">
        <div class="custom-icon-uploder-modal-content-child"></div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                <a class="btn btn-info custom-icon-uploder-btn"  data-type="application/zip" > <?php esc_html_e('Upload New Icons', 'custom-icon-upload')?> </a>
                </div>
            </div>
            <div class="row">
                <?php
                $fontarray = $this->custom_icon_upload_return_with_title();
                if(!empty($fontarray )){
                    foreach ($fontarray as $key => $font) {
                        echo "<div class='col-6'><div class='card'>
                            <div class='card-header'>
                            <div class='custom-icon-group-main'>{$key}<span class='remove_custom_icon_group' data-target='{$key}'>X</span></span></div></div>
                            <div class='card-body'><ul class='custom-icon-group'>";
                        if (is_array($font)) {
                            foreach ($font as $key => $class) {
                                echo "<li class='border border-secondary rounded' title='{$class}'><i class='{$class}'></i></li>";
                            }
                        }
                        echo '</ul></div></div></div>';
                    }
                }
                
                ?>
                
            </div>
        </div>
        <?php
}
    public function custom_icon_upload_zipped_font()
    {
        check_ajax_referer('custom-icon-upload-nonce', 'security');
        if (!current_user_can('update_plugins')) {
            die(__("You Have No Permission", "custom-icon-upload"));
        }
        $attachment = $_POST['values'];
        $path = realpath(get_attached_file($attachment['id']));
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $this->folder_name = $filename;
        $builedir = $this->dirpaths['fontdir'];
        $zip = new ZipArchive;
        $res = $zip->open($path);
        if ($res === true) {
            $zip->extractTo($builedir. '/' . $filename);
            $zip->close();
            $this->dirpaths['unzippeddir'] = $this->dirpaths['fontdir'] . '/' . $filename;
            $this->dirpaths['unzippedurl'] = $this->dirpaths['fonturl'] . '/' . $filename;
            $this->dirpaths['fontsvgdir'] = $this->dirpaths['unzippeddir'] . '/fonts';
        } else {
            echo "Doh! I couldn't open $path";
            die();
        }
        $this->custom_icon_upload_create();
        die();
    }
    public function custom_icon_upload_create()
    {
        $this->json_file_name = $this->custom_icon_upload_find_file($this->dirpaths['unzippeddir'], 'json');
        $this->svg_file_name = $this->custom_icon_upload_find_file($this->dirpaths['fontsvgdir'], 'svg');
        if (empty($this->json_file_name) || empty($this->svg_file_name)) {
            $this->custom_icon_upload_remove_dir($this->dirpaths['unzippeddir']);
            die(__('Json or SVG file not found', 'custom-icon-upload'));
        }
        $jsonresponse = '';
        $svgresponse = '';
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH . '/wp-admin/includes/file.php';
            WP_Filesystem();
        }
        if (file_exists(trailingslashit($this->dirpaths['fontsvgdir']) . $this->svg_file_name)) {
            $svgresponse = $wp_filesystem->get_contents(trailingslashit($this->dirpaths['fontsvgdir']) . $this->svg_file_name);
            if($svgresponse==''){
                $svgresponse=file_get_contents( trailingslashit($this->dirpaths['fontsvgdir']) . $this->svg_file_name);
            }
        }
        if (file_exists(trailingslashit($this->dirpaths['unzippeddir']) . $this->json_file_name)) {
            $jsonresponse = $wp_filesystem->get_contents(trailingslashit($this->dirpaths['unzippeddir']) . $this->json_file_name);
            if($jsonresponse==''){
                $jsonresponse=file_get_contents( trailingslashit($this->dirpaths['unzippeddir']) . $this->json_file_name);
            }
        }
        if ('' !== $jsonresponse && '' !== $svgresponse) {
            $xml = simplexml_load_string($svgresponse);
            $font_attr = $xml->defs->font->attributes();
            $this->custom_icon_upload_font_name = (string) $font_attr['id'];
            $file_contents = json_decode($jsonresponse);
            if (!isset($file_contents->IcoMoonType)) {
                $this->custom_icon_upload_remove_dir($this->dirpaths['unzippeddir']);
                die(esc_html__('Only Support IcoMoon App Font.', 'custom-icon-upload'));
            }
            $icons = $file_contents->icons;
            foreach ($icons as $icon) {
                $icon_name = $icon->properties->name;
                $icon_class = str_replace(' ', '', $icon_name);
                $icon_class = str_replace(',', ' ', $icon_class);
                $this->new_json[$icon_name] = array(
                    "class" => $icon_class,
                );
            }
            if (!empty($this->new_json) && $this->custom_icon_upload_font_name != '') {
                $this->custom_icon_upload_create_text_file();
                $this->custom_icon_upload_create_css_again();
                $this->custom_icon_upload_create_option();
                echo 'success';
            }
        }
        return false;
    }
    public function custom_icon_upload_create_text_file()
    {
        $arr = array();
        foreach ($this->new_json as $key => $value) {
            $font_array[$this->custom_icon_upload_font_name . '-' . $key] = $this->custom_icon_upload_font_name . '-' . $value['class'];
        }
        $fp = fopen($this->dirpaths['unzippeddir'] . '/' . $this->text_file_name, 'w');
        fwrite($fp, json_encode($font_array));
        fclose($fp);
        chmod($this->dirpaths['unzippeddir'] . '/' . $this->text_file_name, 0777);
    }
    public function custom_icon_upload_create_css_again()
    {
        $stylefile = $this->dirpaths['unzippeddir'] . '/style.css';
        $getcssfile = @file_get_contents($stylefile);
        if ($getcssfile) {
            $str = str_replace('icon-', $this->custom_icon_upload_font_name . '-', $getcssfile);
            $str = str_replace('.icon {', '[class^="' . $this->custom_icon_upload_font_name . '-"], [class*=" ' . $this->custom_icon_upload_font_name . '-"] {', $str);
            $str = str_replace('i {', '[class^="' . $this->custom_icon_upload_font_name . '-"], [class*=" ' . $this->custom_icon_upload_font_name . '-"] {', $str);
            $str = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $str);
            unlink($stylefile);
            $fp = fopen($stylefile, 'w');
            fwrite($fp, $str);
            fclose($fp);
            chmod($stylefile, 0777);
        } else {
            die(__('Unable to write css. Upload icons downloaded only from icomoon', 'custom-icon-upload'));
        }
    }
    public function custom_icon_upload_create_option()
    {
        $fontsoption = get_option('custom_icon_upload_fonts');
        if (empty($fontsoption)) {
            $fontsoption = array();
        }
        if(isset($fontsoption[$this->custom_icon_upload_font_name])){
            //$this->custom_icon_upload_remove_dir($this->dirpaths['unzippeddir']);
            die( esc_html__('Same Name Font Already Install','custom-icon-upload'));
        }
        $fontsoption[$this->custom_icon_upload_font_name] = array(
            'maindir' => trailingslashit($this->dirpaths['unzippeddir']),
            'mainurl' => trailingslashit($this->dirpaths['unzippedurl']),
            'filename' => $this->text_file_name,
            'icondir' => $this->folder_name,
        );
        update_option('custom_icon_upload_fonts', $fontsoption);
    }
    //finds the json file we need to create the config
    public function custom_icon_upload_find_file($path, $type)
    {
        if (!is_dir($path)) {    
            die( esc_html__('Same Name Font Already Install','custom-icon-upload'));
        }
        $files = scandir($path);
        foreach ($files as $file) {
            if (strpos(strtolower($file), $type) !== false && $file[0] != '.') {
                return $file;
            }
        }
    }
    public function custom_icon_upload_delete_icon()
    {
        check_ajax_referer('custom-icon-upload-nonce', 'security');
        $dirname = $_POST['values'];
        $this->custom_icon_upload_remove_dir($this->dirpaths['fontdir'] . '/' . $dirname);
        $fontsoption = get_option('custom_icon_upload_fonts');
        unset( $fontsoption[$dirname]);
        update_option('custom_icon_upload_fonts', $fontsoption);
        echo 'success';
        die();
    }
    public function custom_icon_upload_remove_dir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->custom_icon_upload_remove_dir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
    public function custom_icon_upload_add_icon()
    {
        $fontsoption = get_option('custom_icon_upload_fonts');
        //echo '<pre>',print_r($fonts);
        if (empty($fontsoption)) {
            return false;
        }
        foreach ($fontsoption as $font) {
            if (file_exists($font['maindir'] . 'style.css')) {
                wp_enqueue_style($font['icondir'] . '-style', $font['mainurl'] . 'style.css', '', null);
            }
        }
        //add_filter('car_repair_services_theme_icons', array($this, 'custom_icon_upload_return'));
    }
    public function custom_icon_elementor_editor_css() {
        if( current_user_can( 'manage_options' ) ){
            $fonts = get_option('custom_icon_upload_fonts');
            if (empty($fonts)) {
                return false;
            }
            foreach ($fonts as $font) {
                if (file_exists($font['maindir'] . 'style.css')) {
                    $modtime = mt_rand();
                    echo '<link rel="stylesheet" type="text/css" href="' . $font['mainurl'] . 'style.css?ver=' . $modtime . '">';
                }
            }
        }
    }
    public function custom_icon_upload_return($controls_registry)
    {
        $iconlist = $controls_registry->get_control( 'icon' )->get_settings( 'options' );
        $fonts = get_option('custom_icon_upload_fonts');
        if (empty($fonts)) {
            return false;
        }
        foreach ($fonts as $key=>$font) {
            if (file_exists($font['maindir'] . $font['filename'])) {
                if (file_exists($font['maindir'] . 'style.css')) {
                    wp_enqueue_style($font['icondir'] . '-style', $font['mainurl'] . 'style.css', '', null);
                }
                $handle = fopen($font['maindir'] . $font['filename'], "r");
                $contents = fread($handle, filesize($font['maindir'] . $font['filename']));
                $textfiledecode = json_decode($contents);
                foreach ($textfiledecode as $key1 => $con) {
                    $iconlist[$con] =  str_replace($key1.'-', '', $con);
                }
                fclose($handle);
            }
        }
        $controls_registry->get_control( 'icon' )->set_settings( 'options', $iconlist );
    }
    public function custom_icon_upload_return_with_title()
    {
        $fonts = get_option('custom_icon_upload_fonts');
        if (empty($fonts)) {
            return false;
        }
        $icon = array();
        foreach ($fonts as $font) {
            if (file_exists($font['maindir'] . $font['filename'])) {
                $handle = fopen($font['maindir'] . $font['filename'], "r");
                $contents = fread($handle, filesize($font['maindir'] . $font['filename']));
                $textfiledecode = json_decode($contents);
                foreach ($textfiledecode as $key => $con) {
                    $icon[$font['icondir']][$key] = $con;
                }
                fclose($handle);
            }
        }
        return $icon;
    }
}
$iconmoonFontAdd = new iconmoonFontAdd();
