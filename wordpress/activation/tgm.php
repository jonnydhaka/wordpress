<?php

require_once get_theme_file_path( '/framework/class-tgm-plugin-activation.php' );
$theme_activated = get_option('theme_name_activation');

if($theme_activated==1){
	add_action( 'tgmpa_register', 'theme_name_register_required_plugins' );
}


function theme_name_register_required_plugins() {
	$username      = get_option( 'theme_username' );
	$purchase_code = get_option( 'theme_purchase_code' );
	$token = get_option( 'theme_name_token' );
	
    $plugins = array(

         
        array(
            'name' => esc_html__('WPBakery Visual Composer', 'text-domain'), // The plugin name
            'slug' => 'js_composer', // The plugin slug (typically the folder name)
            'source' => get_template_directory() . '/framework/plugins/js_composer.zip',  // The plugin source
			'source'=>"https://url/download.php?username={$username}&purchasecode={$purchase_code}&token={$token}&filename=js_composer&theme=Theme Nam&site=".get_site_url(),
            'required' => true, // If false, the plugin is only 'recommended' instead of required    
                 
            'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
        )
	);
}