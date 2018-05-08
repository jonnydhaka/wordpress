<?php
Redux::setSection( $opt_name, array(
		'title'            => esc_html__( 'Settings', 'text-domain' ),
		'id'               => 'header_settings',
		'desc'             => esc_html__( 'All settings', 'text-domain' ),
		'customizer_width' => '400px',
		'icon'             => 'el el-website',
		'fields'           => array(
			array(
					'id'       => 'test_field',
					'type'     => 'test',
					'title'    => esc_html__('test', 'text-domain'),
					'subtitle' => esc_html__('test', 'text-domain'),
					
			),
		)
	)
);