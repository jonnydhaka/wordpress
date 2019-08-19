<?php
namespace CVEC\classes\vc;

defined('ABSPATH') || die();

class ele_sc_list extends PBModule
{
    private static $pb_sc_array_list;

    public static function init()
    {
        self::pb_sc_list_array();
    }

    public static function pb_sc_list_array()
    {
        self::$pb_sc_array_list = [
            'services_icon' => [
                'css' => ['services_icon'],
                'js' => [],
                'external' => [
                    'css' => [],
                    'js' => [],
                ],
            ],
        ];
        self::$pb_sc_array_list = apply_filters('combine_vc_ele_css_pb_sc_list_array', self::$pb_sc_array_list);
        return self::$pb_sc_array_list;
    }
    public static function get_pb_sc_array_list()
    {
        return self::$pb_sc_array_list;
    }

}
