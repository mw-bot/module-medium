<?php
if (!defined("MODULE_DB_MEDIUM")) {
    define('MODULE_DB_MEDIUM', MW_TABLE_PREFIX . 'medium');
}
//autoload_append(dirname(__FILE__));
include_once(__DIR__ . DS . 'Medium.php');
event_bind('mw_admin_edit_page_advanced_settings', 'mw_medium_print_edit_content_menu');

function mw_medium_print_edit_content_menu($params = false)
{


    $medium = new Microweber\Medium();


    //d(__DIR__.DS.'Medium.php');
    if ($params != false) {
        $api = $medium->print_ui_field($params);
    }
}


api_hook('save_content', 'mw_medium_assign_to_page');

function mw_medium_assign_to_page($content_id)
{

    $medium = new Microweber\Medium();
    $api = $medium->add_medium_to_content($content_id);


}

event_bind('on_load', 'mw_medium_pull_content');

function mw_medium_pull_content($content_id = false)
{


    $medium = new Microweber\Medium();
    if ($content_id != false) {

        $api = $medium->pull_content($content_id);
    }


}

function mw_medium_get_settings_content($content_id = false)
{
    $medium = new Microweber\Medium();
    if ($content_id != false) {

        return $medium->get_settings($content_id);
    }


}  
           
