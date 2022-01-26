<?php

function tb_panel_style()
{
    global $tb_main_folder;

    echo '<link rel="stylesheet" type="text/css" href="' . $tb_main_folder . '/css/admin-bar.css">';
}

function custom_colors() {
    echo "<style type='text/css'>
            th#woosidebars_enable, td.woosidebars_enable.column-woosidebars_enable {
                display: none;
            }
            
            .edit-post-layout__content {
                top: 123px;
            }
            
            .edit-post-header {
                top: 68px;
                z-index: 39393939;
            }
            
            .edit-post-sidebar {
                top: 123px;
            }
            
            .elementor.elementor-40.elementor-location-header {
                 margin-top: 60px;
                 display: inline-flex;
            }
        </style>
        <script>
    
    jQuery(window).scroll(function () {
    var d = jQuery('#tb-logo'); 
    console.log(d.offset());
    if (d.offset().top < 100) {
        jQuery('#tb-logo-container').css('top','0');  
       // $('#wpadminbar').css('top','35px');
    } else {
        jQuery('#tb-logo-container').css('top','-38px');
       //  $('#wpadminbar').css('top','-69px');
        
    }
});
    </script>";
}

add_action('admin_head', 'custom_colors');

function margintop_head()
{
    echo "";
}

add_action('admin_head', 'tb_panel_style');
add_action('wp_head', 'tb_panel_style');

add_action('get_header', 'remove_header_top_space');

function remove_header_top_space()
{
    remove_action('wp_head', '_admin_bar_bump_cb');
}



add_action('wp_head', 'margintop_head');

/////////////////////////////////////

function tb_login_style()
{
    global $tb_main_folder;
    tb_panel_style();
    echo '<link rel="stylesheet" type="text/css" href="' . $tb_main_folder . '/css/login.css">';
}

add_action('login_enqueue_scripts', 'tb_login_style');
/////////////////////////////////////

function add_tecnoglog_logo()
{
    global $plugin_label;
    echo '<div id="tb-logo-container">
<div id="tb-logo">
<a href="http://www.tecnosoluciones.com" target="_blank" title="TecnoCMS">
<h3>'.$plugin_label.'</h3>
</a>
</div>';
}

add_action('wp_before_admin_bar_render', 'add_tecnoglog_logo');

function add_tecnoglog_border()
{
    echo '<div id="tb-adminbar-border">
</div>
</div>';
}

add_action('wp_after_admin_bar_render', 'add_tecnoglog_border');
///////////////////////////////////////

function remove_footer_admin()
{
    echo 'TecnoCMS por TecnoSoluciones.com / Powered by <a href="https://wordpress.org">WordPress</a>';
}

add_filter('admin_footer_text', 'remove_footer_admin');

/////////////////////////////////////////

add_action('login_head', 'tb_login_head');

add_action('login_form', 'tb_login_form');
add_action('lostpassword_form', 'tb_lostpassword_form');
add_action('register_form', 'tb_register_form');

add_action('login_form', 'wpse17709_login_form');
add_action('lostpassword_form', 'wpse17709_login_form');
add_action('register_form', 'wpse17709_login_form');

function tb_login_head()
{
    add_tecnoglog_logo();
    echo "<div id='wpadminbar'></div>";

    if($_SERVER['REQUEST_URI'] == "/login"){
        echo "
            <style type=\"text/css\">
                .elementor.elementor-40.elementor-location-header {
                     margin-top: 60px;
                     display: inline-flex;
                }
                #login-left {
                    top: 251px;
                } 
                #login-top, #login-title {
                    display: none;
                }
            </style>";
    }
    add_tecnoglog_border();

}

function tb_login_form()
{
    global $plugin_label;
    echo "<div id='login-top'>
    <div id='tb-logo'>
<a href='https://tecnosoluciones.com' target='_blank' title='TecnoCMS'>
<h3>$plugin_label</h3>
</a>
</div></div>
<div id='login-title'><span>Ingresar</span></div>
<div id='login-left'></div>";
    ?>
    <div id='login-links'><p id="nav">
            <?php if (!isset($_GET['checkemail']) || !in_array($_GET['checkemail'], array('confirm', 'newpass'))) : ?>
                <?php if (get_option('users_can_register')) : ?>
                    <a href="<?php echo esc_url(site_url('wp-login.php?action=register',
                        'login')); ?>"><?php _e('Register'); ?></a> |
                <?php endif; ?>
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>"
                   title="<?php esc_attr_e('Password Lost and Found'); ?>"><?php _e('Lost your password?'); ?></a>
            <?php endif; ?>
            <?php
            if (!$interim_login): ?>
        <p id="backtoblog"><a href="<?php echo esc_url(home_url('/')); ?>"
                              title="<?php esc_attr_e('Are you lost?'); ?>"><?php printf(__('&larr; Back to %s'),
                    get_bloginfo('title', 'display')); ?></a></p>
        <?php endif; ?>

        </p></div>
        <script>
viewport = document.querySelector('meta[name=viewport]');
viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');
</script>
    <?php
}

function tb_lostpassword_form()
{
    echo "<div id='login-top'><a href=''></a></div>
<div id='login-title'><span>" . __('Lost Password') . "</span></div>
<div id='login-left'></div>";
    ?>
    <div id='login-links'>
        <p id="nav">
            <a href="<?php echo esc_url(wp_login_url()); ?>"><?php _e('Log in') ?></a>
            <?php if (get_option('users_can_register')) : ?>
                | <a href="<?php echo esc_url(site_url('wp-login.php?action=register',
                    'login')); ?>"><?php _e('Register'); ?></a>
            <?php endif; ?>
        </p>
        <?php
        if (!$interim_login): ?>
            <p id="backtoblog"><a href="<?php echo esc_url(home_url('/')); ?>"
                                  title="<?php esc_attr_e('Are you lost?'); ?>"><?php printf(__('&larr; Back to %s'),
                        get_bloginfo('title', 'display')); ?></a></p>
        <?php endif; ?>

        </p></div>
    <?php
}

function tb_register_form()
{
    echo "<div id='login-top'><a href=''></a></div>
<div id='login-title'><span>" . __('Register For This Site') . "</span></div>
<div id='login-left'></div>";
    ?>
    <div id='login-links'>
        <p id="nav">
            <a href="<?php echo esc_url(wp_login_url()); ?>"><?php _e('Log in'); ?></a> |
            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>"
               title="<?php esc_attr_e('Password Lost and Found') ?>"><?php _e('Lost your password?'); ?></a>
        </p>
        <?php
        if (!$interim_login): ?>
            <p id="backtoblog"><a href="<?php echo esc_url(home_url('/')); ?>"
                                  title="<?php esc_attr_e('Are you lost?'); ?>"><?php printf(__('&larr; Back to %s'),
                        get_bloginfo('title', 'display')); ?></a></p>
        <?php endif; ?>

        </p></div>
    <?php
}


function wpse17709_login_form()
{
    add_filter('gettext', 'wpse17709_gettext', 10, 2);
}

function wpse17709_gettext($translation, $text)
{
    if ($text == 'Log in' || $text == 'Lost your password?' || $text == '&larr; Back to %s' || $text == ' | ') {
        return '';
    } elseif ($text == 'Log In') {
        return 'Ingresar';
    }


    return $translation;
}


