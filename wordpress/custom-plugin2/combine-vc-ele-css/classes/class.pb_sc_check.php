<?php
namespace CVEC\classes\vc;

defined('ABSPATH') || die();

class pb_sc_check
{
    public static function Check_sc_exist_in_post($array_list, $post_id)
    {
        $content_post = get_post($post_id);
        $content = $content_post->post_content;
        $return_array = [];
        foreach ($array_list as $key => $array) {
            if (stripos($content, $key)) {
                $return_array[] = $array;
            }
        }
        if ($return_array && !empty($return_array)) {
            return $return_array;
        } else {
            return false;
        }
    }

    public static function Check_ele_sc_exist_in_post($array_list, $post_id)
    {
        $elementor_array = '';
        $css_editor = '';
        //if (version_compare(ELEMENTOR_VERSION, '2.6.10', '>=')) {
        //$elementor_array = get_post_meta($post_id, '_elementor_elements_usage', true);
        //}
        //if (!is_array($elementor_array)) {
        $return_array = [];
        $elementor_array = [];
        $content_post = \Elementor\Plugin::$instance->documents->get($post_id);
        $content_post = $content_post ? $content_post->get_elements_data() : [];

        \Elementor\Plugin::$instance->db->iterate_data($content_post, function ($element) use (&$elementor_array, &$css_editor) {
            if (empty($element['widgetType'])) {
                $type = $element['elType'];
            } else {
                $type = $element['widgetType'];
            }
            if (!empty($element['settings'][CSS_EDITOR_NAME])) {
                $css_editor .= $element['settings'][CSS_EDITOR_NAME];
            }
            if (!isset($elementor_array[$type])) {
                $elementor_array[$type] = 0;
            }
            $elementor_array[$type]++;
            return $element;
        });
        // }

        if ($css_editor != '') {
            $filename = pb_build_css::$targetdircss . "css_editor_{$post_id}.css";
            if (file_exists($filename)) {
                unlink($filename);
            }
            if (!is_dir(pb_build_css::$targetdircss)) {
                @mkdir(pb_build_css::$targetdircss, 0777, true);
            }
            file_put_contents($filename, $css_editor);
        } else {
            $filename = pb_build_css::$targetdircss . "css_editor_{$post_id}.css";
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
        foreach ($array_list as $key => $array) {
            if (array_key_exists($key, $elementor_array)) {
                $return_array[] = $array;
            }
        }

        if ($return_array && !empty($return_array)) {
            return $return_array;
        } else {
            return false;
        }
    }

}
