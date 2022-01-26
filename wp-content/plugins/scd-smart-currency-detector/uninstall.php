<?php
/**
 * Uninstall plugin - Single and Multisite
 */
 
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

    //php mailer variables
  $to = 'gajelabs@gmail.com, support@gajelabs.com';
  $subject = "SCD Free desactivated";
  $headers = 'From customer site ';
  $message = 'Administrators Email </br>';
  
      $blogusers = get_users('role=Administrator');
    foreach ($blogusers as $user) {
        $message.=$user->user_email.'<span>&nbsp;</span>' ;
      }  

//Here put your Validation and send mail
 wp_mail($to, $subject, strip_tags($message), $headers);

if ( !is_multisite() ) 
{
    // If the option to delete data on uninstall is not checked, exit
    if(scd_get_delete_data_option() == true)
    {
        delete_option( 'scd_general_options' );
	    delete_option( 'scd_currency_options' );
	    delete_option( 'scd_role_options' );
	    delete_option( 'widget_scd_widget' );
	    delete_post_meta_by_key( 'scd_other_options' );
    }
} 
else 
{
    global $wpdb;
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();

    foreach ( $blog_ids as $blog_id ) 
    {
        switch_to_blog( $blog_id );

        // If the option to delete data on uninstall is not checked, continue
        if(scd_get_delete_data_option() == true)
        {
            delete_option( 'scd_general_options' );
            delete_option( 'scd_currency_options' );
            delete_option( 'scd_role_options' );
            delete_option( 'widget_scd_widget' );
            delete_post_meta_by_key( 'scd_other_options' );
        }
    }
    switch_to_blog( $original_blog_id );
}

function scd_get_delete_data_option() 
{
    try {   
        $options = get_option('scd_general_options');
        if (is_array($options) && array_key_exists('deleteDataOnUninstall', $options)) {
            if ($options['deleteDataOnUninstall'] == "0")
                return false;
            else
                return true;
        }
        else {
            return false;
        }
    }
    catch(exception $e){
        return true;
    }
 }

?>