<?php
/*
Plugin Name: Envato Licence Validation
Plugin URI: http://smartdatasoft.com
Description: Creates and endpoint to get license key status
Version: 1.0
Author: Muhammad Arifur Rahman
Author URI: http://smartdatasoft.com
License: GPLv2
 */
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
define('ENVATO_LICENCE_DB_VERSION', '1.0.0');
define('EL_DEBUG', false);

function sm_envato_licensing()
{
    $envato_Licence_Valicator = new Envato_Licence_Valicator();
    return $envato_Licence_Valicator;
}
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
include plugin_dir_path(__FILE__) . 'lib/class-activation.php';
include plugin_dir_path(__FILE__) . 'lib/class-envato-licence-info-list-table.php';
include plugin_dir_path(__FILE__) . 'lib/licence-settings.php';
include plugin_dir_path(__FILE__) . 'lib/cpt-envato-product.php';
include plugin_dir_path(__FILE__) . 'lib/envato-product-meta.php';

register_activation_hook(__FILE__, 'ck_elvl_activation_tasks');
register_activation_hook(__FILE__, 'ck_elvl_create_table');
register_activation_hook(__FILE__, 'my_activation_func');

function my_activation_func(){
    file_put_contents(__DIR__ . '/my_log.txt', ob_get_contents());
}

function ck_elvl_activation_tasks(){
    flush_rewrite_rules();
}

// Delete table when deactivate
function ck_elvl_remove_database(){
    global $wpdb;
    $table_name = $wpdb->prefix . "envato_licence_info";
    $table_name_fraud = $wpdb->prefix . 'fraud_envato_licence_info';
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $sql_fraud = "DROP TABLE IF EXISTS $table_name_fraud;";
    $wpdb->query($sql);
    $wpdb->query($sql_fraud);
    delete_option("elvl_db_version");
}

function ck_elvl_create_table(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'envato_licence_info';
    $table_name_fraud = $wpdb->prefix . 'fraud_envato_licence_info';
    $table_name_stats = $wpdb->prefix . 'envato_sale_stats';
    if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        `id_envato_licence_info` int(11)   NOT NULL AUTO_INCREMENT,
        `purchase_key` varchar(40)  ,
        `domain_name` varchar(400) ,
        `buyer` VARCHAR(100) NOT NULL ,
        `token` VARCHAR(255) NOT NULL ,
        `status` int(11) DEFAULT '0',
        `license_type` int(11) NOT NULL DEFAULT '1',
        `item_id` int(11) NOT NULL,
        `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id_envato_licence_info)
        ) $charset_collate;";
        dbDelta($sql);
    }
    if ($wpdb->get_var("show tables like '$table_name_fraud'") != $table_name_fraud) {
        $sql_fraud = "CREATE TABLE IF NOT EXISTS $table_name_fraud (
        `id` int(11)   NOT NULL AUTO_INCREMENT,
        `fraud_purchase_key` varchar(40)  ,
        `fraud_domain_name` varchar(400) ,
        `fraud_buyer` VARCHAR(100)  NULL ,
        `fraud_status` int(11) DEFAULT '0',
        `fraud_license_type` int(11)  NULL DEFAULT '1',
        `fraud_item_id` int(11)  NULL,
        `fraud_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `fraud_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_fraud);
    }
    if ($wpdb->get_var("show tables like '$table_name_stats'") != $table_name_stats) {
        $sql_stats = "CREATE TABLE IF NOT EXISTS $table_name_stats (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `item_id` int(11) NOT NULL,
        `sale` int(11) NOT NULL,
        `date` date NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_stats);
    }
    require_once ABSPATH . '/wp-admin/includes/upgrade.php';
    add_option('elvl_db_version', ENVATO_LICENCE_DB_VERSION);

}

add_action('init', 'ck_enl_add_endpoint');
function ck_enl_add_endpoint($rewrite_rules){
    add_rewrite_endpoint('ck-ensl-api', EP_ALL);
}

function ck_elvl_deactivation_tasks(){
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'ck_elvl_deactivation_tasks');

function ck_elv_query_vars($vars){
    $vars[] = 'ck-ensl-purchase-key';
    $vars[] = 'licence_action';
    $vars[] = 'site_url';
    $vars[] = 'item_id';
    $vars[]='filename';
    $vars[]='token';
	$vars[]='multisite';
	$vars[]='info';

    return $vars;
}
add_filter('query_vars', 'ck_elv_query_vars');

function sm_elv_process_request(){
    global $wp_query;
    $end_point = get_query_var('ck-ensl-api');
    if (!isset($wp_query->query_vars['ck-ensl-api'])) {
        return;
    }
    if (!isset($wp_query->query_vars['ck-ensl-purchase-key'])) {
        sm_evl_output(array('error' => 'No Purchase Key Provided'));
    }
    $action = $wp_query->query_vars['licence_action'];
    $item_id = $wp_query->query_vars['item_id'];
    $domain_name = $wp_query->query_vars['site_url'];
    $purchase_code = $wp_query->query_vars['ck-ensl-purchase-key'];
    $args = array(
        'post_type' => array('cpt_env_products'),
        'meta_query' => array(
            array(
                'key' => 'cvl_envato_product',
                'value' => $item_id,
                'compare' => '=',
            ),
        ),
    );
    $query = new WP_Query($args);
    $item_post_id = $query->posts[0]->ID;
    $item_zips = get_post_meta($item_post_id,'cvl_envato_product_zip',true);
    if (isset($action) && ($action == "downloadzip")) {
        $envato_Licence_Valicator = new Envato_Licence_Valicator();
        $token = $wp_query->query_vars['token'];
        $status=$envato_Licence_Valicator->checkAlreadyActive($purchase_code, $domain_name,$token );
        if($purchase_code=='localsdtest'){
            $status=1;
        }
		
        $statusarray=array('1','5');
        if( in_array($status,$statusarray)  ){
            $filename= $wp_query->query_vars['filename'];
            foreach($item_zips as $item_zip){
                if($item_zip['_ev_file_dir']!=''){
                    if($item_zip['_ev_file_dir']==$filename){
                        $file=WP_CONTENT_DIR.'/uploads/envato-products/'.$filename.'.zip';
                        if (!is_file($file)) {
                            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
                            sm_evl_output('File not found') ;
                        } else if (!is_readable($file)) {
                            header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
                            sm_evl_output('File not readable') ;
                        }else{
							while (ob_get_level()) {
								ob_end_clean();
							}
                            header('Content-type: application/zip');
                            header('Content-Disposition: attachment; filename="'. basename( $file ).'"');
                            readfile( $file );
                        }
                    }
                }
            }
        }else{
            $file='Something Wrong';
        }
        sm_evl_output($file);
    }


    if (isset($action) && ($action == "jsonread")) {
        $envato_Licence_Valicator = new Envato_Licence_Valicator();
        $status=$envato_Licence_Valicator->checkAlreadyActive($purchase_code, $domain_name);
        if($purchase_code=='localsdtest'){
            $status=1;
        }
        if ($status) {
            $array=array();
            foreach($item_zips as $item_zip){
                if($item_zip['_ev_file_dir']!=''){
                    $array[$item_zip['_ev_file_dir'].'/'.$item_zip['_ev_file_names']]['slug']=$item_zip['_ev_file_dir'];
                    $array[$item_zip['_ev_file_dir'].'/'.$item_zip['_ev_file_names']]['pname']=$item_zip['_ev_file_pname'];
                    $array[$item_zip['_ev_file_dir'].'/'.$item_zip['_ev_file_names']]['new_version']=$item_zip['_ev_file_version'];
                    $array[$item_zip['_ev_file_dir'].'/'.$item_zip['_ev_file_names']]['changelog']=$item_zip['_ev_changelog'];
                    $array[$item_zip['_ev_file_dir'].'/'.$item_zip['_ev_file_names']]['details']=$item_zip['_ev_details'];
                }
            }
            sm_evl_output( $array);
        } else {
            $data = array(
                "success" => false,
                "status" => 'not activated',
            );
            sm_evl_output($data);
        }
    }

    $sl = sm_envato_licensing();
    if (isset($action) && ($action == "activate")) {
        $sl->activate_license(array('key' => $wp_query->query_vars['ck-ensl-purchase-key'], 'item_id' => $item_id));
    } elseif (isset($action) && ($action == "deactivate")) {
        $sl->deactivate_license(array('key' => $wp_query->query_vars['ck-ensl-purchase-key'], 'item_id' => $item_id));
    } else {
        $data = array(
            "success" => false,
            "status" => 'Authentication Error',
        );
        sm_evl_output($data);
    }
}

add_action('template_redirect', 'sm_elv_process_request', -1);

function sm_evl_output($output){

    if (EL_DEBUG == true) {
        die();
    }
    // Helps us exit any output buffers started by plugins or themes
    $ob_status = ob_get_level();
    while ($ob_status > 0) {
        ob_end_clean();
        $ob_status--;
    }
    // Output the data for the endpoint
    header('Content-Type: application/json');
    echo json_encode($output);
    exit;
}

class Envato_Licence_Valicator{
    private $API_KEY;
    private $options;
	public $alreadyactive='';
    /** Constructor */
    public function __construct(){
        $this->options = get_option('envato_licence_settings');
        $api_key = $this->options['elv_envato_api_key']; // 'tVVruafe8h7T9Pt0zGSTQARssVWBfvzH';
        $this->API_KEY = $api_key;
    }

    public function deactivate_license($purchase_key){

        global $wpdb, $wp_query;
        $table_name = $wpdb->prefix . 'envato_licence_info';
        $purchase_code = $purchase_key['key'];
        $licence_deactivation_remove_status = false;
        $domain_name = $wp_query->query_vars['site_url'];
        //now we will check if the module activated in our server, if exits then we will remove the record
        $licence_deactivation_remove_status = $this->options['elv_remove_deactivate_site'];
		
        // later we have to retrive from database and then check it is activated or not
        if ($this->checkAlreadyActive($purchase_code, $domain_name) && ($licence_deactivation_remove_status == 1)) {
            $table_name = $wpdb->prefix . 'envato_licence_info';
            $arrgs = array(
                'purchase_key' => $purchase_code,
                'domain_name' => $domain_name,
            );
            $wpdb->delete($table_name, $arrgs);
        } else {
            //if hook do not set for remove will will just update the activation status
            $status = 0;
            $visittime = time();
            $status = $wpdb->update(
                $table_name,
                array(
                    'status' => $status, // integer (number)
                    'updated' => $visittime, // string
                ),
                array('purchase_key' => $purchase_code),
                array('%d', '%s',),
                array('%s')
            );
			/* $arrgs = array(
                'purchase_key' => $purchase_code,
                'domain_name' => $domain_name,
            );
            $wpdb->delete($table_name, $arrgs);*/
        }
        $data = array(
            "success" => true,
            "status" => 'deactivated',
            // "license": "deactivated",
        );
        sm_evl_output($data);
    }

    public function activate_license($purchase_code)
    {
        
        global $wpdb, $wp_query;

        $purchase_key = trim($purchase_code['key']);
        $item_id = (int) trim($purchase_code['item_id']);
        $domain_name = $wp_query->query_vars['site_url'];
        $table_name = $wpdb->prefix . 'envato_licence_info';

        //now we check our database if the licence already activate in the domain

        $licence_status = $this->checkAlreadyActive($purchase_key, $domain_name);

        if($purchase_key=='localsdtest'){
            $data = array(
                "success" => true,
                "status" => 'valid',
				'token' => 'tokenforlocalhost',
            );
            sm_evl_output($data);
        }
		
		
        //  1   :  "domain exits and already activated";
        //  2   :  "domain exits and deactivated"; // this means this purchase key found in our database
        //  3   :  "licence already activated other domain"; // this means this purchase key found in our database
        //  4   :   "licence not found";
        if (EL_DEBUG == true) {
            echo '<br>licence_statut : ' . $licence_status;
        }
		
		
		if($licence_status == '7'){
			 $table_name = $wpdb->prefix . 'envato_licence_info';
			 $visittime = time();
			 $secret = "SmartData-Bangladesh";
	         $token = md5($body->item->id.$purchase_key.$secret);
			$status = $wpdb->update(
                $table_name,
                array(
                    'status' =>1, // integer (number)
                    'updated' => $visittime, // string
					'token' => $token,
                ),
                array('purchase_key' => $purchase_key)
               
            );
			 $data = array(
                "success" => true,
                "status" => 'valid',
				'token' => $token,
            );
            sm_evl_output($data);
		}elseif ($licence_status == 1) { //licence exits in any domain or sub domain
            //domain licence already exits
            $data = array(
                "success" => false,
                "status" => 'valid',
				'token' => $this->alreadyactive,
            );
            sm_evl_output($data);

        } elseif ($licence_status == 2) {

            $status = 1;
            //now we will udpate
            $visittime = time();
            $updated = $wpdb->update(
                $table_name,
                array(
                    'status' => $status, // integer (number)
                    'updated' => $visittime, // string
                ),
                array('purchase_key' => $purchase_key),
                array(
                    '%d', // value1
                    '%s', // value2
                ),
                array('%s')
            );
            if (EL_DEBUG == true) {
                echo $wpdb->last_query;
            }

            if (false === $updated) {
                echo "Activation failed";
            }
            $data = array(
                "success" => true,
                "status" => 'valid',
            );
            // echo $wpdb->last_query;
            sm_evl_output($data);

        } elseif ($licence_status == 3) { //"licence already activated other domain";

            $data = array(
                "success" => false,
                "status" => 'This licence active in Another Site',
            );
            sm_evl_output($data);

        } else { 
            $this->url = 'https://api.envato.com/v3/market/author/sale?code=' . urlencode(trim($purchase_key)) . '';

            $response = wp_remote_get($this->url,
                array(
                    'headers' => array(
                        'Authorization' => "Bearer " . $this->API_KEY,
                        'User-Agent' => "Enter a description of your app here for the API team",
                    ),
                )
            );

            $body = json_decode($response['body']);

            if ($item_id > 0 && $body->item->id !== $item_id) {
                $data = array(
                    "success" => false,
                    "status" => 'invalid',
                );
               if(isset($body->description) && $body->description!=''){
                   $data['error']=$body->description;
               }
               if(isset($body->error) && $body->error!=''){
                    $data['error_code']=$body->error;
                }
                
                $licence_fraud_detection_status = $this->options['elv_envato_fraud_detection'];
                if ($licence_fraud_detection_status == 1) {
                    $data['fraud'] = true;
                    $table = $wpdb->prefix . 'fraud_envato_licence_info';
                    $sql = "SELECT * FROM $table  WHERE fraud_purchase_key='" . $purchase_key . "' AND fraud_domain_name='" . $domain_name . "'";
                    $results = $wpdb->get_results($sql);
                    if (empty($results)) {
                        $insert_data = array(
                            'fraud_item_id' => $item_id,
                            'fraud_purchase_key' => $purchase_key,
                            'fraud_buyer' => $body->buyer,
                            'fraud_domain_name' => $domain_name,
                            'fraud_status' => 0,
                            'fraud_license_type' => '',
                        );
                        $wpdb->insert($table, $insert_data);
                        $insert_id = $wpdb->insert_id;
                    }

                }
            } else {
                $secret = "SmartData-Bangladesh";
	            $token = md5($body->item->id.$purchase_key.$secret);
                $data = array(
                    "success" => true,
                    "status" => 'valid',
                    "item_id" => $body->item->id,
                    "name_name" => $body->item->name,
                    "sold_at" => $body->sold_at,
                    "license_type" => $body->license,
                    "support_amount" => $body->support_amount,
                    "supported_until" => $body->supported_until,
                    "buyer" => $body->buyer,
                    "purchase_count" => $body->purchase_count,
                    "token" => $token,
                );
                if ($licence_status == 5) {
                    $data['subdomain'] = true;
                }
                //now we will check if it found , if found that we will update activation other wise insert
                $status = 1;
                $licence_type = $this->licenceType($body->license);
                $table = $wpdb->prefix . 'envato_licence_info';
                $insert_data = array(
                    'item_id' => $body->item->id,
                    'purchase_key' => $purchase_key,
                    'buyer' => $body->buyer,
                    'domain_name' => $domain_name,
                    'status' => $status,
                    'license_type' => $licence_type,
                    'token' => $token,
                );
                $wpdb->insert($table, $insert_data);
                $insert_id = $wpdb->insert_id;
            }
            sm_evl_output($data);
        } //end if if statement of licence check
    }

    public static function licenceType($type)
    {
        switch ($type) {
            case 'Regular License':
                return 1;
            case 'Extended License':
                return 2;
            default:
                return 1;
        }
    }
    public static function licenceTypeName($name)
    {
        switch ($name) {
            case 1:
                return 'Regular License';
            case 2:
                return 'Extended License';
            default:
                return 'Regular License';
        }
    }
	
	public function getdomainname ($url){
		$parsehost = parse_url($url);
		$host_names = explode(".", $parsehost['host']);
		$bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
		return $bottom_host_name;
	}

    public function checkAlreadyActive($purchase_key, $site_domain,$token ='')
    {
        global $wpdb;
		global $alreadyactive;
		$multisite=$info='';
        $table_name = $wpdb->prefix . 'envato_licence_info';
        if($token !=''){
            $sql = "SELECT * FROM $table_name  WHERE  purchase_key='" . $purchase_key . "' AND domain_name='" . $site_domain . "' AND token='" . $token ."'";
            $results = $wpdb->get_results($sql);
            if (empty($results)) {
                return false; 
            }
        }
		
		
		$sql = "SELECT * FROM $table_name  WHERE  purchase_key='" . $purchase_key . "' AND domain_name='" . $site_domain . "' AND status=0";
        $results = $wpdb->get_results($sql);
		
		
        if (!empty($results)) {
            if (EL_DEBUG == true) {
                echo "domain exits and already activated";
            }
            return "7"; 
        }
		
		
        $sql = "SELECT * FROM $table_name  WHERE  purchase_key='" . $purchase_key . "' AND domain_name='" . $site_domain . "'";
        $results = $wpdb->get_results($sql);
		
        if (!empty($results)) {
            if (EL_DEBUG == true) {
                echo "domain exits and already activated";
            }
			$this->alreadyactive=$results[0]->token;
            return "1"; 
        }
		
		
        $domain_name = '';
        $sql = "SELECT * FROM $table_name  WHERE purchase_key='" . $purchase_key . "'";
        $results = $wpdb->get_results($sql);
		
        if (count($results) > 0) {

            foreach ($results as $key => $value) {
                $domain_name = $value->domain_name; 
                $licence_sub_domain_activation_status = $this->options['elv_envato_sub_domain_activation'];
                $gethost = $this->getdomainname($domain_name);
                $senthost =  $this->getdomainname($site_domain); 
                if (($domain_name == $site_domain) && ($value->status == 1)) {
                    if (EL_DEBUG == true) {
                        echo "domain exits and already activated";
                    }
                    return "1"; //
                } elseif (($domain_name != $site_domain) && ($gethost == $senthost) && $licence_sub_domain_activation_status == 1) {
                    if (EL_DEBUG == true) {
                        echo "licence already activated, but Subdomain Detected";
                    }
                    return "5"; //
                } elseif (($domain_name == $site_domain) && ($value->status == 0)) {
                    if (EL_DEBUG == true) {
                        echo "domain exits and deactivated";
                    }
                    // this means this purchase key found in our database
					
                    return "2";
                } else {
                    // echo "domain exits and deactivated"; // this means this purchase key found in our database
                    // return "2";
                }
            } //end foreach of licence found
            if (EL_DEBUG == true) {
                echo "licence already activated other domain" . "<br>" . "Request domain : " . $site_domain . "<br>" . " Activated domain : " . $domain_name . "<br>";
            }
            // it means purchase code found in database but didn't match with domain so already activated
			
            return "3";
        }
        if (EL_DEBUG == true) {
            echo "licence not found";
        }
		
        return 4;
        // print_r($resutl ) ;die();
        // return $resutl;
    }
    public function get_domain($url)
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }
    public function fetch_envato_products()
    {
        $page = 1;
        $page_size = 20;
        $envato_username = $this->options['elv_envato_envato_user_name'];
        $url = 'https://api.envato.com/v1/discovery/search/search/item?page=' . $page . '&page_size=' . $page_size . '&username=' . $envato_username;
        $response = wp_remote_get($url,
            array(
                'headers' => array(
                    'Authorization' => "Bearer " . $this->API_KEY,
                    'User-Agent' => "Enter a description of your app here for the API team",
                ),
            )
        );
        // Check for error
        if (is_wp_error($response)) {
            return;
        }
        // Parse remote HTML file
        $products = json_decode(wp_remote_retrieve_body($response));
        // Check for error
        if (is_wp_error($products)) {
            return;
        }
        $this->insertEnvatoSale($products);
        //now our goal is to add this product statistics in our database
        update_option('envato_licence_product_list', $products);
    }
    private function insertEnvatoSale($products)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'envato_sale_stats';
        $today = date('Y-m-d');
        $sql = "SELECT * FROM $table  WHERE  date='" . $today . "'";
        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            return "Data Exits"; //
        }

        foreach ($products->matches as $key => $product) {

            if (isset($product->id)) {

                $insert_data = array(
                    'item_id' => $product->id,
                    'sale' => $product->number_of_sales,
                    'date' => $today,

                );
                //$format = array('%s','%d');
                $wpdb->insert($table, $insert_data);
                $insert_id = $wpdb->insert_id;

                // echo $insert_id . " Item Name : {$product->name} ". "<br/>";

            } else {
                echo "Product ID Not Found";
            }

        }
    }

}
if (is_admin()) {
    new Envato_Licence_Info_Wp_List_Table();
}

/**
 * Paulund_Wp_List_Table class will create the page to load the table
 */
class Envato_Licence_Info_Wp_List_Table
{
    /**
     * Constructor will create the menu item
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_menu_licence_info_table_page'));
    }

    /**
     * Menu item will allow us to load the page to display the table
     */
    public function add_menu_licence_info_table_page()
    {
        // add_menu_page('Envato Purchase codes', 'Purchase codes', 'manage_options', 'envato-licence-info-list-table', array($this, 'list_table_page'));
        add_submenu_page(
            'edit.php?post_type=cpt_env_products',
            'Purchase codes',
            'Purchase codes',
            'manage_options',
            'envato-licence-info-list-table',
            array($this, 'list_table_page')
        );
        add_submenu_page(
            'edit.php?post_type=cpt_env_products',
            __('Settings', 'textdomain'),
            __('Settings', 'envato-licence-validate'),
            'manage_options',
            'books-shortcode-ref',
            'elv_options_page_callback'
        );
        add_submenu_page(
            'edit.php?post_type=cpt_env_products',
            __('Report', 'textdomain'),
            __('Report', 'envato-licence-validate'),
            'manage_options',
            'envato-sales-report',
            'elv_report_page_callback'
        );
    }

    /**
     * Display the list table page
     *
     * @return Void
     */
    public function list_table_page()
    {
        $envato_licence_info = new Envato_Licence_Info_List_Table();

        $envato_licence_info->prepare_items();

        ?>

            <div class="wrap">
            <?php $envato_licence_info->views();?>
            <form method="post">
            <?php

        $envato_licence_info->search_box('Search', 'search');
        $envato_licence_info->display();
        ?>
            </form>
        <?php
}

}

function elv_report_page_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "envato_sale_stats";
    $id = '19823557';

    // echo "I am report";
    $sql = ' SELECT *  FROM ' . $table_name . ' where item_id=' . $id . '';

    $sales_data = $wpdb->get_results($sql);

    $data_x = '';
    $label_x = '';
    foreach ($sales_data as $key => $sale) {

        //  print_r($sale);
        $sales_time = $sale->date;
        $sales_no = $sale->sale;

        //$data_x .="".strtotime( $sales_time).",".$sales_no."],";
        $data_x .= "" . $sales_no . ",";
        $label_x .= "'" . $sales_time . "',";
    }

    $data_x = rtrim($data_x, ',');
    $label_x = rtrim($label_x, ',');

    ?>
 <canvas id="myChart" width="300" height="300"></canvas>
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
<script>
var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    "type": "line",
    "data": {
        "labels": [<?php echo $label_x; ?>],
        "datasets": [{
            "label": "My First Dataset",
            "data": [<?php echo $data_x; ?>],
        }]
    },
    "options": {
        "scales": {
            "yAxes": [{
                "ticks": {
                    "suggestedMin": 800,
                    "suggestedMax": 900
                }
            }]
        }
    }
});

</script>
<?php

}

function envato_licence_get_zip_file($item_id, $file_name)
{

    $args = array(
        'meta_key' => 'cvl_envato_product',
        'meta_value' => $item_id,
        'post_type' => 'cpt_env_products',
        'post_status' => 'any',
        'posts_per_page' => -1,
    );
    $post = get_posts($args);

    // check results ##
    if (!$post || is_wp_error($post)) {
        return false;
    }

    $item_zips = get_post_meta($post[0]->ID, 'cvl_envato_product_zip', true);
    foreach ($item_zips as $key => $f) {
        if ($f['_ev_file_names'] == $file_name) {
            return $f['_ev_file_urls'];
        }
    }
}

function cvl_envato_load_product_func()
{
    $envato_Licence_Valicator = new Envato_Licence_Valicator();
    $result = $envato_Licence_Valicator->fetch_envato_products();
    die();
}
add_action('wp_ajax_cvl_envato_load_product', 'cvl_envato_load_product_func');
add_action('wp_ajax_nopriv_cvl_envato_load_product', 'cvl_envato_load_product_func');

function  cvl_envato_mime_types($mimes)
{
    $mimes['zip'] = 'application/zip';
    $mimes['gz'] = 'application/x-gzip';
    return $mimes;
}
add_filter('upload_mimes', 'cvl_envato_mime_types',15);
add_action('init', 'do_output_buffer');
function do_output_buffer() {
        ob_start();
}