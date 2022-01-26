<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_WCFM_Integration_Hooks' ) ):

    class BP_Better_Messages_WCFM_Integration_Hooks
    {

        public $shown_in_admin = false;

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new BP_Better_Messages_WCFM_Integration_Hooks();
            }

            return $instance;
        }


        public function __construct()
        {
            add_filter( 'wcfm_menus', array( $this, 'wcfm_menus' ), 10, 1 );

            add_filter( 'wcfm_query_vars', array( $this, 'wcfm_query_vars' ), 50 );

            add_action( 'wcfm_load_views', array( $this, 'wcfm_csm_load_views'), 50 );
            add_action( 'before_wcfm_load_views', array( $this, 'wcfm_csm_load_views'), 50 );

            add_filter( 'bp_better_messages_page', array( $this, 'messages_page_for_vendors'), 10, 2 );

            add_action( 'woocommerce_single_product_summary',	array( &$this, 'product_page_contact_button' ), 35 );

            if( ! class_exists('BuddyPress') ) {
                add_filter('bp_core_get_user_domain', array($this, 'bp_core_get_user_domain'), 10, 4);
            }

            add_action('wp_enqueue_scripts', array( $this, 'inbox_counter_javascript' ) );
            add_action('before_wcfmmp_store_header_actions', array( $this, 'profile_button' ) );
        }

        public function bp_core_get_user_domain( $domain, $user_id, $user_nicename, $user_login ){
            if( ! class_exists('WCFM') || ! function_exists('wcfmmp_get_store_url') ) return $domain;
            if( ! wcfm_is_vendor( $user_id ) ) return $domain;

            return wcfmmp_get_store_url( $user_id );
        }

        public function profile_button( $vendor_id ){

            $bpbm_vendors = BP_Better_Messages_WCFM_Integration()->functions->is_messages_enabled( $vendor_id );
            if( $bpbm_vendors === 'enabled' ){

                if( $vendor_id !== 0 ) {
                    $user = get_userdata($vendor_id);
                    $nice_name = $user->user_nicename;
                    $text    = _x('Have question', 'WCFM Profile page', 'bp-better-messages-wcfm');
                    $subject = urlencode($text);
                    $message = urlencode($text);
                    $label = _x('Private Message', 'WCFM Profile page', 'bp-better-messages-wcfm');

                    $link = add_query_arg([
                        'new-message' => '',
                        'to' => $nice_name,
                        'subject' => $subject,
                        'message' => $message
                    ], BP_Better_Messages()->functions->get_link( get_current_user_id() ) );

                    echo '<div class="lft bd_icon_box"><a class="wcfm_store_bpbm_pm" href="' . esc_url($link) . '"><i class="wcfmfa fa-envelope" aria-hidden="true"></i><span>' . esc_attr($label) . '</span></a></div>';
                }

            }
        }

        public function messages_page_for_vendors( $url, $user_id ){
            if( ! is_user_logged_in() ) return $url;
            if( ! class_exists('WCFM') ) return $url;
            if( ! wcfm_is_vendor( $user_id ) && ! current_user_can('manage_options')) return $url;

            #$enabled = BP_Better_Messages_WCFM_Integration()->functions->is_messages_enabled( $user_id );

            #if( $enabled === 'enabled' ) {
            #    $pro_dashboard_pages   = (array) get_option( 'wcvendors_dashboard_page_id', array() );
            #    if( ! empty( $pro_dashboard_pages ) ) {
            #        $dashboard_page_id = $pro_dashboard_pages[0];
            #        $permalink         = get_permalink($dashboard_page_id);
            #        $url = trailingslashit($permalink) . 'messages/';
            #    }
            #}
            $wcfm_page = get_wcfm_page();
            $wcfm_messages_url = wcfm_get_endpoint_url( 'messaging', '', $wcfm_page );

            return $wcfm_messages_url;
        }

        public function wcfm_csm_load_views( $end_point ){
            global $WCFM, $WCFMu;
            $plugin_path = trailingslashit( dirname( __FILE__  ) );

            switch( $end_point ) {
                case 'bpbm-messages':
                    require_once(BP_Better_Messages_WCFM_Integration()->path . 'views/wcfm-bpbm-messaging.php' );
                    break;
            }
        }

        function wcfm_query_vars( $query_vars ) {
            $wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );

            $query_custom_menus_vars = array(
                'bpbm-messages' => ! empty( $wcfm_modified_endpoints['bpbm-messages'] ) ? $wcfm_modified_endpoints['bpbm-messages'] : 'messaging',
            );

            $query_vars = array_merge( $query_vars, $query_custom_menus_vars );

            return $query_vars;
        }

        public function wcfm_menus( $wcfm_menus  ){
            $wcfm_page = get_wcfm_page();
            $wcfm_messages_url = wcfm_get_endpoint_url( 'messaging', '', $wcfm_page );
            $wcfm_menus['bpbm-messages'] = array(
                'label'      => __( 'Messages', 'bp-better-messages'),
                'url'        => $wcfm_messages_url,
                'icon'       => 'comments',
                'priority'   => 50
            );

            return $wcfm_menus;
        }

        public function inbox_counter_javascript(){
            wp_enqueue_style('bp-messages');
            ob_start(); ?>
            .wcfm_store_bpbm_pm{
            min-width: 50px;
            width: auto;
            padding: 0 15px;
            height: 30px;
            background: #fff;
            color: #17A2BB !important;
            border-radius: 5px;
            display: inline-block;
            cursor: pointer;
            }
            .wcfm_store_bpbm_pm span{
            color: #17A2BB !important;
            font-size: 13px !important;
            }

            .wcfm-pm{
            background: #1c2b36;
            padding: 5px 10px;
            -moz-border-radius: 3px;
            -webkit-border-radius: 3px;
            border-radius: 3px;
            border: #f0f0f0 1px solid;
            border-bottom: 1px solid #17a2b8;
            color: #b0bec1;
            float: left;
            text-align: center;
            text-decoration: none;
            margin-top: 10px;
            -webkit-box-shadow: 0 1px 0 #ccc;
            box-shadow: 0 1px 0 #ccc;
            display: block;
            cursor: pointer;
            }

            .wcfm-pm:hover{
            background: #17a2b8 !important;
            background-color: #17a2b8 !important;
            border-bottom-color: #17a2b8 !important;
            color: #ffffff !important;
            }
            <?php
            $css = ob_get_clean();

            if( trim( $css ) !== '' ){
                echo '<style type="text/css">' . BP_Better_Messages()->functions->minify_css( $css ) . '</style>';
            }

            if( is_user_logged_in() ) {
                $enabled = BP_Better_Messages_WCFM_Integration()->functions->is_messages_enabled( get_current_user_id() );
                if( $enabled === 'enabled' ) {
                    ob_start(); ?>
                    jQuery(document).ready(function( event ) {
                    var unread = BP_Messages['total_unread'];
                    var element = jQuery('.wcfm_menu_bpbm-messages .wcfm_menu_item .text .bp-better-messages-unread');
                    if( element.length === 0 ){
                    jQuery('<span class="bp-better-messages-unread bpbmuc bpbmuc-hide-when-null" data-count="' + unread + '">' + unread + '</span>').appendTo(jQuery('.wcfm_menu_bpbm-messages .wcfm_menu_item .text'));
                    }
                    });
                    <?php
                    $js = ob_get_clean();

                    if( trim( $js ) !== '' ){
                        wp_add_inline_script( 'bp_messages_js', BP_Better_Messages()->functions->minify_js( $js ), 'before' );
                    }

                    ob_start(); ?>
                    .wcfm_menu_bpbm-messages .wcfm_menu_item .text .bp-better-messages-unread{
                    margin-left: 10px;
                    }

                    .wcfm_store_bpbm_pm{
                    min-width: 50px;
                    width: auto;
                    padding: 0 15px;
                    height: 30px;
                    background: #fff;
                    color: #17a2b8;
                    border-radius: 5px;
                    display: inline-block;
                    cursor: pointer;
                    }
                    <?php
                    $css = ob_get_clean();

                    if( trim( $css ) !== '' ){
                        wp_add_inline_style( 'bp-messages', BP_Better_Messages()->functions->minify_css( $css ) );
                    }
                }
            }
        }

        public function product_page_contact_button(){
            global $WCFM, $post;
            $vendor_id = 0;

            if( is_product() && $post && is_object( $post ) ) {
                $product_id = $post->ID;
                $vendor_id = wcfm_get_vendor_id_by_post( $product_id );
            }

            $bpbm_vendors = BP_Better_Messages_WCFM_Integration()->functions->is_messages_enabled( $vendor_id );
            if( $bpbm_vendors === 'enabled' ){

                if( $vendor_id !== 0 ) {
                    $product_title = get_the_title();
                    $user = get_userdata($vendor_id);
                    $nice_name = $user->user_nicename;
                    $text = sprintf(_x('Have question regarding your product %s', 'WCFM Product page', 'bp-better-messages-wcfm'), $product_title);

                    $subject = urlencode($text);
                    $message = urlencode($text);
                    $label = _x('Private Message', 'WCFM Product page', 'bp-better-messages-wcfm');

                    $link = add_query_arg([
                        'new-message' => '',
                        'to' => $nice_name,
                        'subject' => $subject,
                        'message' => $message
                    ], BP_Better_Messages()->functions->get_link( get_current_user_id() ) );

                    echo '<a href="' . esc_url($link) . '" class="wcfm-pm"><span class="wcfmfa fa-envelope"></span>&nbsp;&nbsp;' . esc_attr($label) . '</a>';
                }
            }
        }

    }


    function BP_Better_Messages_WCFM_Integration_Hooks(){
        return BP_Better_Messages_WCFM_Integration_Hooks::instance();
    }

endif;