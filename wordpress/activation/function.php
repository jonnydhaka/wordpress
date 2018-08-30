<?php
add_action('admin_menu', 'theme_name_theme_activation_menu');
 function theme_name_theme_activation_menu() {
    add_theme_page('Activation', 'Activation', 'manage_options', 'theme_name_activation', 'theme_name_ta_page');
 }

 function theme_name_ta_page() {
    ?><div class="wrap">
        <h1>Theme Panel</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields("activation-section");
                do_settings_sections("activation-setting");      
                submit_button();
            ?>          
        </form>
        </div>
    <?php
 }
 
 
 add_action("admin_init", "theme_name_ta_setting_fields");
 
 function theme_name_ta_setting_fields()
{
    add_settings_section("activation-section", "Active Car Repair Theme", null, "activation-setting");
    add_settings_field("theme_username", "User Name", "theme_name_theme_username", "activation-setting", "activation-section");
    add_settings_field("theme_purchase_code", "Purchase Code", "theme_name_theme_purchase_code", "activation-setting", "activation-section");

    register_setting("activation-section", "theme_username");
    register_setting("activation-section", "theme_purchase_code");
}

function theme_name_theme_username()
{
    echo '<input value="'.get_option('theme_username').'" name="theme_username">';
}
function theme_name_theme_purchase_code()
{
    echo '<input value="'.get_option('theme_purchase_code').'" name="theme_purchase_code">';
}

function theme_name_purchase_authenticate() {
	$username      = get_option( 'theme_username' );
	$purchase_code = get_option( 'theme_purchase_code' );
	$activation = get_option( 'theme_name_activation' );
	$token = get_option( 'theme_name_token' );
	if ( $activation != 1 && $token == '' ) {
		if ( $username != '' && $purchase_code != '' ) {
			$url = "http://url/authenticate.php?username={$username}&purchasecode={$purchase_code}&theme=Theme Name&site=".get_site_url();
			$response = wp_remote_get( $url );
			if(isset($response->errors)){
				update_option( 'theme_name_activation', 0 );
				update_option( 'theme_name_token', '' );
			}else{
				$response_body = json_decode($response['body']);
				if ( 'invalid' != $response_body->massage  ) {
					update_option( 'theme_name_activation', 1 );
					update_option( 'theme_name_token', $response_body->token );
				}else{
					update_option( 'theme_name_activation', 0 );
					update_option( 'theme_name_token', '' );
				}
			}
		}
	}
	
}

add_action( 'after_setup_theme', 'theme_name_purchase_authenticate' );

// plugin update from salf hosted 
add_filter ('pre_set_site_transient_update_plugins', 'display_transient_update_plugins');
function display_transient_update_plugins ($transient)
{
	$username      = get_option( 'theme_username' );
	$purchase_code = get_option( 'theme_purchase_code' );
	$activation = get_option( 'theme_name_activation' );
	$token = get_option( 'theme_name_token' );
	if ( $activation != 1 && $token == '' ) {
		//$data=get_plugin_data( WP_PLUGIN_DIR.'/plugin-folder/plugin-page.php',  true, true );
		$url = "https://url/secure/json.php";
		$response = wp_remote_get( $url );
		if(!isset($response->errors)){
			$response = json_decode( $response['body'] );
			foreach ($response  as $key=>$item) {
				$data=get_plugin_data( WP_PLUGIN_DIR.'/'.$key,  true, true );
				if( version_compare( $data['Version'], $item->new_version, '<' ) ){
					$item->url = "https://url/download.php?username={$username}&purchasecode={$purchase_code}&token={$token}&filename=js_composer&theme=Theme Nam&site=".get_site_url();
					$item->package = "https://url/download.php?username={$username}&purchasecode={$purchase_code}&token={$token}&filename=js_composer&theme=Theme Nam&site=".get_site_url();
					$transient->response[ $key ] = $item;
				}
			}
		}
		/*echo '<pre>';
		print_r($transient);
		echo '</pre>';*/
		return $transient;
	}
}
