<?php
add_action("admin_init", "theme_name_ta_setting_fields");
 
 function theme_name_ta_setting_fields()
{
    add_settings_section("activation-section", "Active theme Repair Theme", null, "activation-setting");
    add_settings_field("theme_username", "User Name", "theme_name_theme_username", "activation-setting", "activation-section");
    add_settings_field("theme_purchase_code", "Purchase Code", "theme_name_theme_purchase_code", "activation-setting", "activation-section");

    register_setting("activation-section", "theme_username");
    register_setting("activation-section", "theme_purchase_code");
}

function theme_name_theme_username()
{
    echo '<input value="'.get_option('theme_username').'" name="theme_username">';
}
function theme_name_theme_purchase_code()
{
    echo '<input value="'.get_option('theme_purchase_code').'" name="theme_purchase_code">';
}
