<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_test' ) ) {
    class ReduxFramework_test {

        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since ReduxFramework 1.0.0
         */
        function __construct( $field = array(), $value = '', $parent ) {
            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;
        }

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        function render() {
			
			 echo '<input  type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] .'" class="regular-text " /><br />';
			
			
			}

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 3.0.0
         */
        /*function enqueue() {
            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'redux-field-text-css',
                    ReduxFramework::$_url . 'inc/fields/text/field_text.css',
                    array(),
                    time(),
                    'all'
                );
            }
        }*/
    }
}