<?php
add_filter( 'woocommerce_product_data_tabs', 'add_my_custom_product_data_tab' , 99 , 1 );
function add_my_custom_product_data_tab( $product_data_tabs ) {
    $product_data_tabs['my-custom-tab'] = array(
        'label' => __( 'My Custom Tab', 'my_text_domain' ),
        'target' => 'my_custom_product_data',
    );
    return $product_data_tabs;
}

add_action( 'woocommerce_product_data_panels', 'add_my_custom_product_data_fields' );
function add_my_custom_product_data_fields() {
    global $woocommerce, $post;
    ?>
    <!-- id below must match target registered in above add_my_custom_product_data_tab function -->
    <div id="my_custom_product_data" class="panel woocommerce_options_panel">
        <?php
        woocommerce_wp_checkbox( array( 
            'id'            => '_engrave_text_option1', 
            'wrapper_class' => 'show_if_simple', 
            'label'         => __( 'My Custom Field Label', 'my_text_domain' ),
            'description'   => __( 'My Custom Field Description', 'my_text_domain' ),
            'default'       => '1',
            'desc_tip'      => false,
        ) );
		 woocommerce_wp_checkbox( array(
			'id'        => '_engrave_text_option',
			'description'      => __('set custom Engrave text field', 'woocommerce'),
			'label'     => __('Display custom Engrave', 'woocommerce'),
			'desc_tip'  => 'true'
		));
        ?>
    </div>
    <?php
}


add_action( 'woocommerce_process_product_meta', 'woocommerce_process_product_meta_fields_save' );
function woocommerce_process_product_meta_fields_save( $post_id ){
    // This is the case to save custom field data of checkbox. You have to do it as per your custom fields
    $woo_checkbox = isset( $_POST['_engrave_text_option1'] ) ? 'yes' : 'no';
	$woo_checkbox = isset( $_POST['_engrave_text_option'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_engrave_text_option1', $woo_checkbox );
	update_post_meta( $post_id, '_engrave_text_option', $woo_checkbox );
}
?>