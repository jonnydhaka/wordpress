<?php

add_action( 'admin_menu', 'template_color_main_plugin_menu' );
add_action("admin_init", "template_color_setting");
add_action( 'wp_enqueue_scripts',  'template_color_enqueue_scripts' );


function template_color_setting(){
    add_settings_section("template-color-section", "Template RTL Status", null, "template-color-setting");
    add_settings_field("template_color_active_intive", "RTL Setting", "template_color_display_oil_title", "template-color-setting", "template-color-section");
    register_setting("template-color-section", "template_color_active_intive");
}
function template_color_main_plugin_menu(){
    add_menu_page( 'Rtl Setting', "Rtl Setting",'manage_options' , 'templatecolor', 'template_color_settings_page', '', 10);
}
function template_color_display_oil_title()
{
    ?>
     <input name="template_color_active_intive" type="checkbox" value="1" <?php checked( '1', get_option( 'template_color_active_intive' ) ); ?> /> 
     <?php
}

function template_color_settings_page()
{
    ?><div class="wrap">
        <h1>Theme Panel</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields("template-color-section");
                do_settings_sections("template-color-setting");      
                submit_button();
            ?>          
        </form>
        </div>
    <?php
}

function template_color_enqueue_scripts() {
    if(get_option( 'template_color_active_intive' )==1){
        wp_enqueue_style( 'changetemplatecolormain', plugin_dir_url( __FILE__ ) . 'css/change_color_template_main.css' );
        wp_enqueue_style( 'changetemplatecolorblue', plugin_dir_url( __FILE__ ) . 'css/change_color_template_blue.css' );
        wp_enqueue_style( 'changetemplatecolorgreen', plugin_dir_url( __FILE__ ) . 'css/change_color_template_green.css' );
        wp_enqueue_style( 'changetemplatecolorviolet', plugin_dir_url( __FILE__ ) . 'css/change_color_template_violet.css' );
        wp_enqueue_style( 'changetemplatecoloryellow', plugin_dir_url( __FILE__ ) . 'css/change_color_template_yellow.css' );
        wp_enqueue_script( 'changetemplatecolor', plugin_dir_url( __FILE__ ) . 'js/change_color_template.js', array('jquery') );
        wp_localize_script( 'changetemplatecolor', 'changetemplatecolor_object',array( 'changetemplatecolor_ajax_url' => plugins_url( 'img/color-icon.png', __FILE__ )) );
    }
}

