<?php
$fonts_array=bestel_add_fonts_family(1);
$fonts_array2=bestel_add_fonts_family(2);
$fonts_array3=bestel_add_fonts_family(3);
$fonts_array4=bestel_add_fonts_family(4);
vc_map(array(
    "name" => "Slider Banner",
    "base" => "bestel_slick_slider_banner",
    "icon" => BESTEL_THEME_URI . '/images/best-hotel-icon.png',
    "category" => esc_html__('Bestel', BESTEL_HOTEL_CORE),
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
	
	$fonts_array[0],
    $fonts_array[1],
    $fonts_array[2],
    $fonts_array[3],
    $fonts_array[4],
    $fonts_array[5],
    $fonts_array[6],
    $fonts_array[7],
	
	array(
		 'type' => 'param_group',
		  'value' => '',
		  'heading' =>  __( 'List Items', 'pt-vc' ),
		  'param_name' => 'heading1_list',
		  'params' => array(
			  array(
				 'type' => 'textfield',
				 'value' => '',
				 'heading' => __( 'Title', 'pt-vc' ),
				 'param_name' => 'heading1_title',
			 ),
			 array(
				'type' => 'iconpicker',
				'heading' => __( 'Icon', 'js_composer' ),
				'param_name' => 'heading1_bestelicons',
					'settings' => array(
					'emptyIcon' => false, // default true, display an "EMPTY" icon?
					'type' => 'bestelicons',
					'iconsPerPage' => 200, // default 100, how many icons per/page to display
					),
				'description' => __( 'Select icon from library.', 'js_composer' ),
				),
			array(
				 'type' => 'textfield',
				 'value' => '',
				 'heading' => __( 'Icon Class', 'pt-vc' ),
				 'param_name' => 'heading1_icon',
			 ),
			 array(
                "type" => "vc_link",
                "holder" => "div",
                "heading" => __( 'Link', 'pt-vc' ),
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
	
	$fonts_array2[0],
    $fonts_array2[1],
    $fonts_array2[2],
    $fonts_array2[3],
    $fonts_array2[4],
    $fonts_array2[5],
    $fonts_array2[6],
    $fonts_array2[7],
	
	
	array(
		 'type' => 'param_group',
		  'value' => '',
		  'heading' =>  __( 'List Items', 'pt-vc' ),
		  'param_name' => 'heading2_list',
		  'params' => array(
			  array(
				 'type' => 'textfield',
				 'value' => '',
				 'heading' => __( 'Title', 'pt-vc' ),
				 'param_name' => 'heading2_title',
			 ),
			 array(
				 'type' => 'textfield',
				 'value' => '',
				 'heading' => __( 'Sub Title', 'pt-vc' ),
				 'param_name' => 'heading2_title1',
			 ),
			array(
				 'type' => 'attach_image',
				 'value' => '',
				 'heading' => __( 'Image', 'pt-vc' ),
				 'param_name' => 'heading2_img',
			 ),
			 array(
                "type" => "vc_link",
                "holder" => "div",
                "heading" => __( 'Link', 'pt-vc' ),
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
	
	$fonts_array3[0],
    $fonts_array3[1],
    $fonts_array3[2],
    $fonts_array3[3],
    $fonts_array3[4],
    $fonts_array3[5],
    $fonts_array3[6],
    $fonts_array3[7],

        
	
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
					'emptyIcon' => false, // default true, display an "EMPTY" icon?
					'type' => 'bestelicons',
					'iconsPerPage' => 200, // default 100, how many icons per/page to display
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

    class WPBakeryShortCode_Bestel_Slick_Slider_Banner extends WPBakeryShortCode {

    }

}