<?php


//add_action( 'admin_menu', 'elv_add_admin_menu' );
add_action( 'admin_init', 'envato_licence_settings_init' );


function elv_add_admin_menu(  ) { 

	//add_submenu_page( 'tools.php', 'envato-licence-validate', 'envato-licence-validate', 'manage_options', 'envato-licence-validate', 'elv_options_page' );

}


function envato_licence_settings_init(  ) { 

	register_setting( 'pluginPage', 'envato_licence_settings' );

	add_settings_section(
		'elv_pluginPage_section', 
		__( '', 'envato-licence-validate' ), 
		'envato_licence_settings_section_callback', 
		'pluginPage'
	);

    
	add_settings_field( 
		'elv_envato_user_name', 
		__( 'Envato User Name', 'envato-licence-validate' ), 
		'elv_envato_user_name_render', 
		'pluginPage', 
		'elv_pluginPage_section' 
    );
    
	add_settings_field( 
		'elv_envato_api_key', 
		__( 'Envato Token', 'envato-licence-validate' ), 
		'elv_envato_api_key_render', 
		'pluginPage', 
		'elv_pluginPage_section' 
	);


	add_settings_field( 
		'elv_remove_load_envato_product', 
		__( 'Load Envato Product', 'envato-licence-validate' ), 
		'elv_load_envato_product_render', 
		'pluginPage', 
		'elv_pluginPage_section' 
	);

    add_settings_field( 
		'elv_remove_deactivate_site', 
		__( 'Remove Activated Site', 'envato-licence-validate' ), 
		'elv_envato_remove_deactivate_render', 
		'pluginPage', 
		'elv_pluginPage_section' 
	);
	
	add_settings_field( 
		'elv_envato_sub_domain_activation', 
		__( 'Activated Site In Sub Domain', 'envato-licence-validate' ), 
		'elv_envato_sub_domain_activation_render', 
		'pluginPage', 
		'elv_pluginPage_section' 
	);
	

	add_settings_field( 
		'elv_envato_fraud_detection', 
		__( 'Activated Fraud Detection', 'envato-licence-validate' ), 
		'elv_envato_fraud_detection_render', 
		'pluginPage', 
		'elv_pluginPage_section' 
    );
    
}


function elv_envato_user_name_render(  ) { 

	$options = get_option( 'envato_licence_settings' );
	?>
	<input type='text' name='envato_licence_settings[elv_envato_envato_user_name]' value='<?php echo $options['elv_envato_envato_user_name']; ?>'>
	<?php

}

function elv_load_envato_product_render(  ) { 
	$diasable='';
	$options = get_option( 'envato_licence_settings' );
	if(!isset($options['elv_envato_api_key'])){
		$diasable='disabled="disabled"';
	}
	?>
    <button class="button" id="envato_licence_btn_load_product" type="submit" <?php echo esc_attr($diasable)  ?>name="load_product">Click to load all your Envato products</button>
	<?php

}


function elv_envato_api_key_render(  ) { 

	$options = get_option( 'envato_licence_settings' );
	?>
	<input type='text' name='envato_licence_settings[elv_envato_api_key]' value='<?php echo $options['elv_envato_api_key']; ?>'>
	<?php

}


function elv_envato_remove_deactivate_render(  ) { 

	$options = get_option( 'envato_licence_settings' );
 
	?>
	<input type='checkbox' name='envato_licence_settings[elv_remove_deactivate_site]' <?php if(isset( $options['elv_remove_deactivate_site'])) {checked( $options['elv_remove_deactivate_site'], 1 );} ?> value='1'>
	<?php

}

function elv_envato_sub_domain_activation_render(  ) { 

	$options = get_option( 'envato_licence_settings' );
	?>
	<input type='checkbox' name='envato_licence_settings[elv_envato_sub_domain_activation]' <?php isset($options['elv_envato_sub_domain_activation']) ? checked( $options['elv_envato_sub_domain_activation'], 1 ):''; ?> value='1'>
	<?php

}


function elv_envato_fraud_detection_render(  ) { 

	$options = get_option( 'envato_licence_settings' );
	?>
	<input type='checkbox' name='envato_licence_settings[elv_envato_fraud_detection]' <?php isset($options['elv_envato_fraud_detection']) ? checked( $options['elv_envato_fraud_detection'], 1 ):''; ?> value='1'>
	<?php

}


function envato_licence_settings_section_callback(  ) { 

	echo __( '', 'envato-licence-validate' );

}


function elv_options_page_callback(  ) { 

   // print_r($_REQUEST);
    if(isset($_REQUEST['load_product'])){
		echo "i am fire for fetch envato product";
	}
	?>
	<form action='options.php' method='post'>

		<h2>Envato Licence Validataion Settings</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}