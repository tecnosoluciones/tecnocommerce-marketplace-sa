<?php
/*
Plugin Name: TecnoCommerce | Marketplace Sin Asistencia
Plugin URI: https://tecnosoluciones.com
Description: TecnoCommerce | Marketplace Sin Asistencia
Author: TecnoSoluciones
Version: 1.2
Author URI: https://tecnosoluciones.com
*/
global $tb_plugin_name, $tb_main_folder, $plugin_label;

$tb_plugin_name = basename(__FILE__, ".php");
$tb_main_folder = plugins_url('', __FILE__);
$plugin_label = 'Tecno<strong>Commerce</strong> <span style="    font-size: 28px;color: #000">|</span> <span>  Marketplace Sin Asistencia</span>';


require_once(plugin_dir_path(__FILE__) . 'includes/theme.php');
require_once(plugin_dir_path(__FILE__) . 'includes/disable-updates.php');
require_once(plugin_dir_path(__FILE__) . 'includes/list-mod.php');

add_filter( 'user_row_actions', 'tecnocms_user_row_actions', 10, 2 );
function tecnocms_user_row_actions( $actions, $user_object ) {
    global $wp_roles;

    if($wp_roles->roles[$user_object->roles[0]]['name'] == 'TecnoAdmin'){
        unset($actions['delete']);
    }
    return $actions;
    
}
