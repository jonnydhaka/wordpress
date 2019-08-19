<?php
namespace CVEC\classes\vc;

class CVEC_Customize
{

    public static function init()
    {
        add_action('customize_register', array(__CLASS__, 'register'));
    }

    public static function register($wp_customizer)
    {
        $wp_customizer->add_section('combine_cache_assets',
            array(
                'title' => __('Combine VC Elementor CSS Options', 'combine-vc-ele-css'),
                'priority' => 100,
                'capability' => 'edit_theme_options',
                'description' => __('Allows you to customize some settings for Combine VC Elementor CSS.', 'combine-vc-ele-css'),
            )
        );

        $wp_customizer->add_setting('combine_cache_assets_setting', array(
            'default' => 'Clear Cache',
            'type' => 'theme_mod',
        ));

        $wp_customizer->add_control('combine_cache_assets_button', array(
            'type' => 'button',
            'priority' => 10, // Within the section.
            'section' => 'combine_cache_assets', // Required, core or custom.
            'label' => __('Clear Cache'),
            'description' => __('Clear Cache.'),
            'settings' => 'combine_cache_assets_setting',
            'input_attrs' => array(
                'value' => __('Edit Pages', 'textdomain'), // 👈
                'class' => 'button button-primary combine_vc_ele_css', // 👈
            ),
        ));

    }

}
