<?php

vc_map(array(
    "name" => "Banner",
    "base" => "themename_banner",
    "icon" => THEME_URI . '/images/best-hotel-icon.png',
    "category" => esc_html__('themename', 'text-domain'),
    "content_element" => true,
    "show_settings_on_create" => true,
    "params" => array(
	array(
		"type" => "textfield",
		"holder" => "div",
		"admin_label" => true,
		"heading" => __("Heading 1",""),
		"param_name" => "heading_1",
	),
	array(
		 'type' => 'param_group',
		  'value' => '',
		  'heading' =>  __( 'List Items', 'text-domain' ),
		  'param_name' => 'heading1_list',
		  'params' => array(
			  array(
				 'type' => 'textfield',
				 'value' => '',
				 'heading' => __( 'Title', 'text-domain' ),
				 'param_name' => 'heading1_title',
			 ),
			 array(
				'type' => 'iconpicker',
				'heading' => __( 'Icon', 'text-domain' ),
				'param_name' => 'heading1_bestelicons',
					'settings' => array(
					'emptyIcon' => false, // default true, display an "EMPTY" icon?
					'type' => 'bestelicons',
					'iconsPerPage' => 200, // default 100, how many icons per/page to display
					),
				'description' => __( 'Select icon from library.', 'text-domain' ),
				),
			array(
				 'type' => 'textfield',
				 'value' => '',
				 'heading' => __( 'Icon Class', 'text-domain' ),
				 'param_name' => 'heading1_icon',
			 ),
			 array(
                "type" => "vc_link",
                "holder" => "div",
                "heading" => __( 'Link', 'text-domain' ),
                "param_name" => "heading1_call_action",
             ), 
		)
	),
	array(
		"type" => "textfield",
		"holder" => "div",
		"admin_label" => true,
		"heading" => __("Heading 2",""),
		"param_name" => "heading_2",
	),
	array(
		 'type' => 'param_group',
		  'value' => '',
		  'heading' =>  __( 'List Items', 'text-domain' ),
		  'param_name' => 'heading2_list',
		  'params' => array(
			  array(
				 'type' => 'textfield',
				 'value' => '',
				 'heading' => __( 'Title', 'text-domain' ),
				 'param_name' => 'heading2_title',
			 ),
			 array(
				 'type' => 'textfield',
				 'value' => '',
				 'heading' => __( 'Sub Title', 'text-domain' ),
				 'param_name' => 'heading2_title1',
			 ),
			array(
				 'type' => 'attach_image',
				 'value' => '',
				 'heading' => __( 'Image', 'text-domain' ),
				 'param_name' => 'heading2_img',
			 ),
			 array(
                "type" => "vc_link",
                "holder" => "div",
                "heading" => __( 'Link', 'text-domain' ),
                "param_name" => "heading2_call_action",
             ),
		  )
	),
	array(
		"type" => "textfield",
		"holder" => "div",
		"admin_label" => true,
		"heading" => __("Heading 3",""),
		"param_name" => "heading_3",
	),
	array(
		 'type' => 'param_group',
		  'value' => '',
		  'heading' =>  __( 'List Items', 'pt-vc' ),
		  'param_name' => 'heading3_list',
		  'params' => array(
			  array(
				 'type' => 'textfield',
				 'value' => '',
				 'heading' => __( 'Title', 'pt-vc' ),
				 'param_name' => 'heading3_title',
			 ),
			 array(
				 'type' => 'textfield',
				 'value' => '',
				 'heading' => __( 'Sub Title', 'pt-vc' ),
				 'param_name' => 'heading3_title1',
			 ),
			 array(
				'type' => 'iconpicker',
				'heading' => __( 'Icon', 'js_composer' ),
				'param_name' => 'heading3_bestelicons',
					'settings' => array(
					'emptyIcon' => false, 
					'type' => 'bestelicons',
					'iconsPerPage' => 200, 
					),
				'description' => __( 'Select icon from library.', 'js_composer' ),
				),
			array(
				 'type' => 'textfield',
				 'value' => '',
				 'heading' => __( 'Icon Class', 'pt-vc' ),
				 'param_name' => 'heading3_icon',
			 ),
			 array(
                "type" => "vc_link",
                "holder" => "div",
                "heading" => __( 'Link', 'pt-vc' ),
                "param_name" => "heading3_call_action",
             ),
		  )
	   ),
	)
));


if (class_exists('WPBakeryShortCode')) {

    class WPBakeryShortCode_themename_Banner extends WPBakeryShortCode {

    }

}