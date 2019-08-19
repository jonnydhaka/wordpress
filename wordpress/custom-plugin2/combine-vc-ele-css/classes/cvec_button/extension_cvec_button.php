<?php

/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @author      Kevin Provance (kprovance)
 * @version     4.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Don't duplicate me!
if (!class_exists('ReduxFramework_Extension_cvec_button')) {

    /**
     * Main ReduxFramework options_object extension class
     *
     * @since       3.1.6
     */
    class ReduxFramework_Extension_cvec_button
    {

        // Protected vars
        protected $parent;
        public $extension_url;
        public $extension_dir;
        public static $theInstance;
        public static $version = "4.0";
        public $is_field = false;

        /**
         * Class Constructor. Defines the args for the extions class
         *
         * @since       1.0.0
         * @access      public
         *
         * @param       array $sections Panel sections.
         * @param       array $args Class constructor arguments.
         * @param       array $extra_tabs Extra panel tabs.
         *
         * @return      void
         */
        public function __construct($parent)
        {

            $this->parent = $parent;
            if (empty($this->extension_dir)) {
                //$this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
            }
            $this->field_name = 'cvec_button';

            self::$theInstance = $this;

            $this->is_field = Redux_Helpers::isFieldInUse($parent, 'cvec_button');

            if (!$this->is_field) {
                $this->add_section();
            }

            add_filter('redux/' . $this->parent->args['opt_name'] . '/field/class/' . $this->field_name, array(
                &$this,
                'overload_field_path',
            )); // Adds the local field
        }

        public function add_section()
        {
            $this->parent->sections[] = array(
                'id' => 'cvec_button_section',
                'title' => __('Combine VC Elementor CSS Option', 'redux-framework'),
                'heading' => '',
                'icon' => 'el el-info-circle',
                'customizer' => false,
                'fields' => array(
                    array(
                        'id' => 'cvec_button',
                        'type' => 'cvec_button',
                        'title' => '',
                    ),
                ),
            );
        }

        // Forces the use of the embeded field path vs what the core typically would use
        public function overload_field_path($field)
        {
            return dirname(__FILE__) . '/' . $this->field_name . '/field_' . $this->field_name . '.php';
        }
    } // class
} // if
