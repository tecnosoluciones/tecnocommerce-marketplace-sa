<?php

/////////////////////////////////////////

add_filter('plugin_action_links', 'disable_plugin_deactivation', 10, 4);
function disable_plugin_deactivation($actions, $plugin_file, $plugin_data, $context)
{
    global $tb_plugin_name, $tb_main_folder;
    // Remove edit link for all
    if (array_key_exists('edit', $actions)) {
        unset($actions['edit']);
    }
    // Remove deactivate link for crucial plugins
    if (array_key_exists('deactivate', $actions) && in_array($plugin_file, array(
            $tb_plugin_name . '/' . $tb_plugin_name . '.php'
        ))
    ) {
        unset($actions['deactivate']);
    }
    return $actions;
}

///////////////////////////////////////////

function remove_plugin_checked_box($hook)
{
    global $tb_plugin_name, $tb_main_folder;
    if ('plugins.php' != $hook) {
        return;
    } else {
        echo '<style type="text/css">
		#' . $tb_plugin_name . ' th input {
			display:none;
			visibility: hidden;
			}
		
		</style>';
    }
}

add_action('admin_enqueue_scripts', 'remove_plugin_checked_box');


function example_remove_dashboard_widgets()
{
    remove_meta_box('dashboard_plugins', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'core');
    remove_meta_box('dashboard_secondary', 'dashboard', 'core');
}

// Hoook into the 'wp_dashboard_setup' action to register our function

add_action('admin_menu', 'example_remove_dashboard_widgets');
//------------------------------
add_action( 'editable_roles' , 'hide_adminstrator_editable_roles' );
function hide_adminstrator_editable_roles( $roles ){
    
    if (isset( $roles['administrator'] ) && !current_user_can('administrator')) {
       unset( $roles['administrator'] );
    }
    return $roles;
}
