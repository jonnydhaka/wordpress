add_action( 'wp_loaded', array($this,'ck_edd_remove_personal_info'),99 );
    }
    function ck_edd_remove_personal_info() {
        global $wp_filter;
        foreach ( $wp_filter['in_plugin_update_message-js_composer/js_composer.php']->callbacks[10] as $key=>$value ) {
            if ( strpos( $key, 'your_update_message_cb') === false) {
                remove_action( 'in_plugin_update_message-js_composer/js_composer.php', $key, 10 );
                break;
            }
        }
    }