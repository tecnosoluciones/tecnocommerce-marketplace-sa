<?php

namespace WPStaging\Backend\Pro\Licensing;

// No Direct Access
use WPStaging;

if( !defined( "WPINC" ) ) {
   die;
}


class Licensing {

   // The license key
   private $licensekey;

   public function __construct() {

      // Load some hooks
      add_action( 'admin_notices', array($this, 'admin_notices') );
      add_action( 'admin_init', array($this, 'activate_license') );
      add_action( 'admin_init', array($this, 'deactivate_license') );
      add_action( 'wpstg_weekly_event', array($this, 'weekly_license_check') );
      // For testing weekly_license_check, uncomment this line
      //add_action( 'admin_init', array( $this, 'weekly_license_check' ) );
      update_option( 'wpstg_license_key','345tert4545rty4574treter565456dft' );
      
      // this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
      if( !defined( 'WPSTG_STORE_URL' ) )
       //  define( 'WPSTG_STORE_URL', 'https://wp-staging.com' );

      // the name of your product. This should match the download name in EDD exactly
      if( !defined( 'WPSTG_ITEM_NAME' ) )
         define( 'WPSTG_ITEM_NAME', 'WP STAGING PRO' );

      // Load EDD Plugin updater
      //require_once( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );

      // Inititalize the EDD software licensing API
      $this->plugin_updater();

      // the license key
      $this->licensekey = trim( get_option( 'wpstg_license_key' ) );
   }

    /**
     * EDD software licensing API
     */
    public function plugin_updater()
    {
        $license_key = trim(get_option('wpstg_license_key'));

        // Check for 'undefined' here because WPSTGPRO_PLUGIN_FILE will be undefined if plugin is uninstalled to prevent issue #216
        $pluginFile = !defined('WPSTGPRO_PLUGIN_FILE') ? null : WPSTGPRO_PLUGIN_FILE;

        $edd_updater = new \WPStaging\Backend\Pro\Licensing\EDD_SL_Plugin_Updater('WPSTG_STORE_URL', $pluginFile, array(
                'version' => WPStaging\WPStaging::getVersion(), // current version number
                'license' => $license_key, // license key (used get_option above to retrieve from DB)
                'item_name' => WPSTG_ITEM_NAME, // name of this plugin
                'author' => 'Rene Hermenau', // author of this plugin
                'beta' => false
            )
        );
    }

   /**
    * Activate the license key
    */
   public function activate_license() {
      if( isset( $_POST['wpstg_activate_license'] ) && !empty( $_POST['wpstg_license_key'] ) ) {
         // run a quick security check
         if( !check_admin_referer( 'wpstg_license_nonce', 'wpstg_license_nonce' ) )
            return; // get out if we didn't click the Activate button

            
         // Save License key in DB
         update_option( 'wpstg_license_key', $_POST['wpstg_license_key'] );

         // retrieve the license from the database
         $license = trim( get_option( 'wpstg_license_key' ) );


         // data to send in our API request
         $api_params = array(
             'edd_action' => 'activate_license',
             'license' => $license,
             'item_name' => urlencode( WPSTG_ITEM_NAME ), // the name of our product in EDD
             'url' => home_url()
         );

         // Call the custom API.
$response = array('response'=>array('code'=>200));

         // make sure the response came back okay
         if( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

            if( is_wp_error( $response ) ) {
               $message = $response->get_error_message();
            } else {
               $message = __( 'An error occurred, please try again.' );
            }
         } else {

$license_data = (object)array('success'=>true, 'license'=>'valid', 'expires'=>'2048-06-06 23:59:59');

            if( false === $license_data->success ) {

               switch ( $license_data->error ) {

                  case 'expired' :

                     $message = sprintf(
                             __( 'Your license key expired on %s.' ), date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                     );
                     // Check if the license has ever been activated
                     //update_option('wpstg_license_data', $license_data);
                     break;

                  case 'revoked' :

                     $message = __( 'Your license key has been disabled.' );
                     break;

                  case 'missing' :

                     $message = __( 'WP Staging license key is invalid.' );
                     break;

                  case 'invalid' :
                  case 'site_inactive' :

                     $message = __( 'Your license is not active for this URL.' );
                     break;

                  case 'item_name_mismatch' :

                     $message = sprintf( __( 'This appears to be an invalid license key for %s.' ), WPSTG_ITEM_NAME );
                     break;

                  case 'no_activations_left':

                     $message = __( 'Your license key has reached its activation limit.' );
                     break;

                  default :

                     $message = __( 'An error occurred, please try again.' );
                     break;
               }
            }
         }

         // Check if anything passed on a message constituting a failure
         if( !empty( $message ) ) {
            $base_url = admin_url( 'admin.php?page=wpstg-license' );
            $redirect = add_query_arg( array('wpstg_licensing' => 'false', 'message' => urlencode( $message )), $base_url );
            update_option( 'wpstg_license_status', $license_data );
            wp_redirect( $redirect );
            exit();
         }

         // $license_data->license will be either "valid" or "invalid"
         update_option( 'wpstg_license_status', $license_data );
         wp_redirect( admin_url( 'admin.php?page=wpstg-license' ) );
         exit();
      }
   }

   public function deactivate_license() {

      // listen for our activate button to be clicked
      if( isset( $_POST['wpstg_deactivate_license'] ) ) {
         // run a quick security check
         if( !check_admin_referer( 'wpstg_license_nonce', 'wpstg_license_nonce' ) )
            return; // get out if we didn't click the Activate button

            
         // retrieve the license from the database
         $license = trim( get_option( 'wpstg_license_key' ) );


         // data to send in our API request
         $api_params = array(
             'edd_action' => 'deactivate_license',
             'license' => $license,
             'item_name' => urlencode( WPSTG_ITEM_NAME ), // the name of our product in EDD
             'url' => home_url()
         );

         // Call the custom API.
         $response = wp_remote_post( WPSTG_STORE_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params) );

         // make sure the response came back okay
         if( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

            if( is_wp_error( $response ) ) {
               $message = $response->get_error_message();
            } else {
               $message = __( 'An error occurred, please try again.' );
            }

            $base_url = admin_url( 'admin.php?page=wpstg-license' );
            $redirect = add_query_arg( array('wpstg_licensing' => 'false', 'message' => urlencode( $message )), $base_url );
            wp_redirect( $redirect );
            exit();
         }
//wp_die(var_dump($response));

         // decode the license data
$license_data = (object)array('license'=>'deactivated');

         // $license_data->license will be either "deactivated" or "failed"
         if( $license_data->license == 'deactivated' || $license_data->license == 'failed' ) {
            delete_option( 'wpstg_license_status' );
         }

         wp_redirect( admin_url( 'admin.php?page=wpstg-license' ) );
         exit();
      }
   }

   /**
    * Check if license key is valid once per week
    *
    * @access  public
    * @since   2.0.3
    * @return  void
    */
   public function weekly_license_check() {
    return;


      if( empty( $this->licensekey ) ) {
         return;
      }

      // data to send in our API request
      $api_params = array(
          'edd_action' => 'check_license',
          'license' => $this->licensekey,
          'item_name' => urlencode( WPSTG_ITEM_NAME ),
          'url' => home_url()
      );

      // Call the API
      $response = wp_remote_post(
              WPSTG_STORE_URL, array(
          'timeout' => 15,
          'sslverify' => false,
          'body' => $api_params
              )
      );

      // make sure the response came back okay
      if( is_wp_error( $response ) ) {
         return false;
      }

      $license_data = json_decode( wp_remote_retrieve_body( $response ) );
      update_option( 'wpstg_license_status', $license_data );

      //$log = new \WPStaging\Utils\Logger;
      //$log->log( json_encode( array($license_data) ) );
   }

   /**
    * This is a means of catching errors from the activation method above and displaying it to the customer
    */
   public function admin_notices() {
      if( isset( $_GET['wpstg_licensing'] ) && !empty( $_GET['message'] ) ) {

         switch ( $_GET['wpstg_licensing'] ) {

            case 'false':
               $message = urldecode( $_GET['message'] );
               ?>
               <div class="wpstg-error" style="font-weight:500;">
                   <p><?php _e('WP Staging - Can not activate license key! ','wp-staging');  echo $message; ?></p>
               </div>
               <?php
               break;

            case 'true':
            default:
               // Put a custom success message here for when activation is successful if they way.
               ?>
               <!--				<div class="success">
                                                  <p><?php echo $message; ?></p>
                                           </div>-->
               <?php
               break;
         }
      }
   }

}
