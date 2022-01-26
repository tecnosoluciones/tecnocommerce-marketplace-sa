<?php

function remove_core_updates()
{
    if (!current_user_can('update_core')) {
        return;
    }
    add_action('init', create_function('$a', "remove_action( 'init', 'wp_version_check' );"), 2);
    add_filter('pre_option_update_core', '__return_null');
    add_filter('pre_site_transient_update_core', '__return_null');
}

add_action('after_setup_theme', 'remove_core_updates');
add_filter('pre_site_transient_update_core','remove_core_updates');


//Ocultar mensajes de administración

function pr_disable_admin_notices() { 
		global $wp_filter; 
			if ( is_user_admin() ) { 
				if ( isset( $wp_filter['user_admin_notices'] ) ) { 
								unset( $wp_filter['user_admin_notices'] ); 
				} 
			} elseif ( isset( $wp_filter['admin_notices'] ) ) { 
						unset( $wp_filter['admin_notices'] ); 
			} 
			if ( isset( $wp_filter['all_admin_notices'] ) ) { 
						unset( $wp_filter['all_admin_notices'] ); 
			} 
	} 
add_action( 'admin_print_scripts', 'pr_disable_admin_notices' ); 