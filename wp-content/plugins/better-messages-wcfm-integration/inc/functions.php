<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_WCFM_Integration_Functions' ) ):

    class BP_Better_Messages_WCFM_Integration_Functions
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new BP_Better_Messages_WCFM_Integration_Functions();
            }

            return $instance;
        }


        public function __construct()
        {
        }

        public function is_messages_enabled( $vendor_id ){
            return 'enabled';
        }

    }


    function BP_Better_Messages_WCFM_Integration_Functions(){
        return BP_Better_Messages_WCFM_Integration_Functions::instance();
    }

endif;