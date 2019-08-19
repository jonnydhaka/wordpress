<?php
/*
Plugin Name: Envato Licence Check
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: This plugin help you to check your purchase code
Author: Muhammod Arifur Rahman
Version: 1.0
Author URI: http://example.com/
 */

define('ENVATOSL_STORE_URL', 'http://localhost/wp-themes/car-repair-services');

class SmartEnvatoLicenceCheck
{

    private $item_id = null;

    public function __construct()
    {
        $this->item_id = '20283498';
        add_action('admin_menu', array($this, 'add_menu'));
        // creates our settings in the options table
        register_setting('envato_theme_license', 'envato_theme_license_key', array($this, 'envato_theme_license_sanitize'));

        //
        add_action('admin_init', array($this, 'theme_activate_license'));
        add_action('admin_notices', array($this, 'conditional_plugin_admin_notice'));
        add_filter('pre_set_site_transient_update_plugins', array($this, 'wp_transient_update_plugins'));
        $hook = "in_plugin_update_message-cleaning_services-core/cleaning-services-core.php";
        add_action( $hook, array($this,'your_update_message_cb'), 10, 2 ); 
        // add_action('plugins_loaded', array($this,'wppstp1_runUpdatedPlugin'));
        // add_action('upgrader_process_complete', array($this,'wppstp1_upgrade'), 10, 2);
        add_filter( 'plugins_api', array($this, 'check_info',), 10, 3 );
    }


    public function check_info( $false, $action, $arg ) {
        if ( isset( $arg->slug )){

        }
        $url = ENVATOSL_STORE_URL . '/ck-ensl-api?licence_action=jsonread&ck-ensl-purchase-key=NA&item_id=' . $this->item_id . '&site_url=' . get_site_url();
        $response = wp_remote_get($url);
        if (!isset($response->errors)) {
            $response = json_decode($response['body']);
            foreach ($response as $key => $item) {
                if ( $arg->slug == $item->slug ) {
                    $information=new stdClass();
                    $information->name = $item->pname;
                    $information->slug = $item->slug;
                    $information->new_version = $item->new_version;
                    $information->last_updated = '';
                    $information->sections = array('details'=>'Details','changelog'=>'Changelog');
                    $information->sections['details'] = '<div>'.$item->details.'</div>';
                    $information->sections['changelog'] ='<div>'.$item->changelog.'</div>';
                    
                    return $information;
                }
            }
        }
		return $false;
	}
    

//     // will working only this plugin activated.
// function wppstp1_upgrade(\WP_Upgrader $upgrader_object, $hook_extra)
// {
//     echo 'test1';
// }// wppstp1_upgrade



// function wppstp1_runUpdatedPlugin()
// {
//    //echo 'test';
// }// wppstp1_run
    


    function your_update_message_cb( $plugin_data, $r )
    {
        $purchase_key = trim(get_option('envato_theme_license_key'));
        $status = get_option('envato_theme_license_key_status');
        if ($status != 'valid' ) {
            echo __('To receive automatic updates license activation is required. Please visit <a href="'.admin_url().'themes.php?page=envato-theme-license">Setting</a> page.','');
        }
        
        
    }
    /**
     * Register a custom menu page.
     */

    public function add_menu()
    {
        add_submenu_page('themes.php', "Theme License", "Theme License", 'switch_themes', 'envato-theme-license', array($this, 'product_license'));
    }

    /**
     * Activate the license.
     *
     * @since 1.0.0
     */
    public function theme_activate_license()
    {

        if (isset($_POST['envato_theme_theme_license_activate'])) {
            if (!check_admin_referer('envato_theme_nonce', 'envato_theme_nonce')) {
                return; // get out if we didn't click the Activate button
            }
            $purchase_key = trim(get_option('envato_theme_license_key'));
            // print "You pressed Button activate";
            $this->activated($purchase_key);
        } else if (isset($_POST['envato_theme_theme_license_deactivate'])) {
            if (!check_admin_referer('envato_theme_nonce', 'envato_theme_nonce')) {
                return; // get out if we didn't click the Activate button
            }
            $purchase_key = trim(get_option('envato_theme_license_key'));
            // print "You pressed Button de activate";
            $this->deactivated($purchase_key);
        }
        //exit;
        return;
    }
    public function activated($license)
    {

        $site_url = get_site_url();
        $url = ENVATOSL_STORE_URL . '/ck-ensl-api?licence_action=activate&ck-ensl-purchase-key=' . $license . '&item_id=' . $this->item_id . '&site_url=' . $site_url;
        $args = array('timeout' => 15, 'sslverify' => false);
        $response = wp_remote_get($url, $args);
        // print_r($response);
        if (is_wp_error($response)) {
            return false;
        }
        $license_data = json_decode(wp_remote_retrieve_body($response));
        if ($license_data->status != alreadyactive) {
            update_option('envato_theme_license_key_status', $license_data->status);
        }

    }
    public function deactivated($license)
    {

        $site_url = get_site_url();
        $url = ENVATOSL_STORE_URL . '/ck-ensl-api?licence_action=deactivate&ck-ensl-purchase-key=' . $license . '&item_id=' . $this->item_id . '&site_url=' . $site_url;
        $args = array('timeout' => 15, 'sslverify' => false);
        $response = wp_remote_get($url, $args);
        //print_r($$url);
        //die();
        if (is_wp_error($response)) {
            return false;
        }

        $license_data = json_decode(wp_remote_retrieve_body($response));

        //update_option( 'envato_theme_license_key_status', 'deactivated' );
        update_option('envato_theme_license_key_status', $license_data->status);
        update_option('envato_theme_license_key', '');

    }

    public function envato_theme_license_sanitize($new)
    {
        $old = get_option('envato_theme_license_key');

        if ($old && $old != $new) {
            delete_option('envato_theme_license_key_status'); // New license has been entered, so must reactivate.
        }

        return esc_attr($new);
    }

    public function conditional_plugin_admin_notice()
    {

        if (isset($_GET['settings-updated'])) {
            $status = get_option('envato_theme_license_key_status');
            if ($status == 'valid') {?>
            <div class="notice notice-success">
                <p><strong><?php esc_html_e('License Activated', 'Envato Licence Check')?> </strong></p>
            </div>
        <?php } else if ($status == 'deactivated') {?>
            <div class="notice notice-success">
                <p><strong><?php esc_html_e('License Deactiveted', 'Envato Licence Check')?><strong></p>
            </div>
        <?php } else {?>
            <div class="notice notice-error">
                <p><strong><?php esc_html_e('Some Eroor', 'Envato Licence Check')?> </strong></p>
            </div>
        <?php }
        }
    }

    public function product_license()
    {
        $license = get_option('envato_theme_license_key');

// $license = substr_replace($license, str_repeat("X", 8), 10, 8);

        $status = get_option('envato_theme_license_key_status');
        ?>
    <div class="wrap">
        <h2><?php _e('Theme License Options', 'envato-theme-license');?></h2>
        <form method="post" action="options.php">
            <?php settings_fields('envato_theme_license');?>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e('License Key', 'envato-theme-license');?>
						</th>
						<td>
							<input id="envato_theme_license_key" name="envato_theme_license_key" type="text" class="regular-text" value="<?php echo esc_attr($license); ?>" />
							<label class="description" for="envato_theme_license_key"><?php _e('Enter your license key for receiving automatic upgrades', 'envato-theme-license');?></label>
						</td>
					</tr>
                    <tr valign="top">
							<th scope="row" valign="top">
								<?php _e('Activate License', 'envato-theme-license');?>
							</th>
							<td>
								<?php if ($status !== false && $status == 'valid') {?>
									<?php wp_nonce_field('envato_theme_nonce', 'envato_theme_nonce');?>
									<input type="submit" class="button-secondary" name="envato_theme_theme_license_deactivate" value="<?php esc_attr_e('Deactivate License', 'mina-olen');?>"/>
								<?php } else {
            wp_nonce_field('envato_theme_nonce', 'envato_theme_nonce');?>
									<input type="submit" class="button-secondary" name="envato_theme_theme_license_activate" value="<?php esc_attr_e('Activate License', 'mina-olen');?>"/>
								<?php }?>
							</td>
                    </tr>
                	</tbody>
			</table>
			<?php submit_button();?>
        </form>
    </div>
    <?php
}

public function wp_transient_update_plugins($transient)
    {
        $url = ENVATOSL_STORE_URL . '/ck-ensl-api?licence_action=jsonread&ck-ensl-purchase-key=NA&item_id=' . $this->item_id . '&site_url=' . get_site_url();
        $response = wp_remote_get($url);
        if (!isset($response->errors)) {
            $response = json_decode($response['body']);
            $purchase_key = trim(get_option('envato_theme_license_key'));
            $status = get_option('envato_theme_license_key_status');
            if ($status == 'valid' &&  $purchase_key!='') {
                foreach ($response as $key => $item) {
                    if(isset($item->details)){
                        unset($item->details);
                    }
                    if(isset($item->changelog))
                        unset($item->changelog);
                    $data = get_plugin_data(WP_PLUGIN_DIR . '/' . $key, true, true);
                    if (version_compare($data['Version'], $item->new_version, '<')) {
                        $item->url = ENVATOSL_STORE_URL . '/ck-ensl-api?licence_action=downloadzip&ck-ensl-purchase-key=' . $purchase_key . '&item_id=' . $this->item_id . '&site_url=' . get_site_url()."&filename={$item->slug}";
                        $item->package = ENVATOSL_STORE_URL . '/ck-ensl-api?licence_action=downloadzip&ck-ensl-purchase-key=' . $purchase_key . '&item_id=' . $this->item_id . '&site_url=' . get_site_url()."&filename={$item->slug}";
                        $transient->response[$key] = $item;
                    }
                }
            }else{
                foreach ($response as $key => $item) {
                    if(isset($item->details)){
                        unset($item->details);
                    }
                    if(isset($item->changelog))
                        unset($item->changelog);
                    $data = get_plugin_data(WP_PLUGIN_DIR . '/' . $key, true, true);
                    if (version_compare($data['Version'], $item->new_version, '<')) {
                        $item->url = ENVATOSL_STORE_URL . '/ck-ensl-api?licence_action=downloadzip&ck-ensl-purchase-key=' . $purchase_key . '&item_id=' . $this->item_id . '&site_url=' . get_site_url()."&filename={$item->slug}";
                        $transient->response[$key] = $item;
                    }
                }
            }
            
        }
        return $transient;
        
}
}

$smartEnvatoLicenceCheck = new SmartEnvatoLicenceCheck();

//http://192.168.0.110/wp-licence/ck-ensl-api?licence_action=activate&ck-ensl-purchase-key=b5175d00-a464-41ad-ba39-936a4f8e73e3&item_id=11336599&site_url=http://192.168.0.110/wp-themes/car-repair-services