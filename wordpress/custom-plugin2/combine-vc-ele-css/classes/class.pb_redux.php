<?php
namespace CVEC\classes\vc;



class CVEC_Redux
{

    public static function init()
    {
        if (!class_exists('Redux')) {
            return;
        }
        $redux_framework = \ReduxFrameworkInstances::get_instance($redux_item['option_name']);
        Redux::setSection($redux_framework, array(
            'title' => esc_html__('Combine VC Elementor CSS Options','combine-vc-ele-css'),
            'id' => 'cvec_redux_settings',
            'desc' => esc_html__('Allows you to customize some settings for Combine VC Elementor CSS.','combine-vc-ele-css'),
            'customizer_width' => '400px',
            'fields' => array(
            array(
                    'id' => 'search_shortcode_header',
                    'type' => 'button',
                    'title' => esc_html__('Clear Cache','combine-vc-ele-css'),
                    'indent' => true,
                ),
            )
            );
    }

}
