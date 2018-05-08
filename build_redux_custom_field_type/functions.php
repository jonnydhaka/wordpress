<?php
add_filter( "redux/mogo_option/field/class/test", "add_test_field_path" ); 

function add_test_field_path($field) {
    return dirname(__FILE__).'/add/test.php';
}