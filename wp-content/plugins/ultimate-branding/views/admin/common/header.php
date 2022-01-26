<?php
$is_admin = current_user_can( 'manage_options' );
$hide_branding = apply_filters( 'wpmudev_branding_hide_branding', false );
$is_pro = Branda_Helper::is_pro();
$is_wpmu_dev_user = class_exists( 'WPMUDEV_Dashboard' ) && method_exists( 'WPMUDEV_Dashboard_Site', 'allowed_user' )
	? WPMUDEV_Dashboard::$site->allowed_user( get_current_user_id() )
	: false;
$show_bf_notice = $is_admin
                  && ! $hide_branding
                  && ( ! $is_pro || $is_wpmu_dev_user )
                  && ! get_site_option( 'branda_black_friday_2021_dismissed', 0 );
if ( $show_bf_notice ) {
	wp_enqueue_script( 'branda-black-friday-2021', branda_url( 'assets/js/branda-black-friday.js' ), array(
		'wp-i18n',
		'wp-element',
		'wp-dom-ready',
		'jquery',
	) );
	wp_localize_script( 'branda-black-friday-2021', '_black_friday', array(
		'build_type' => $is_pro
			? 'full'
			: 'free',
	) );
	?>
	<div id="branda-black-friday-2021"></div>
<?php } ?>

<div class="sui-header">
	<h1 class="sui-header-title"><?php echo esc_html( $title ); ?></h1>
	<div class="sui-actions-right">
<?php if ( $show_manage_all_modules_button ) { ?>
		<button class="sui-button" type="button" data-modal-open="branda-manage-all-modules" data-modal-mask="true"><?php echo esc_html_x( 'Manage All Modules', 'button', 'ub' ); ?></button>
<?php } ?>
<?php if ( $documentation_chapter && ! empty( $helps ) ) : ?>
		<a target="_blank" class="sui-button sui-button-ghost"
		   href="https://wpmudev.com/docs/wpmu-dev-plugins/branda/#<?php echo esc_attr( $documentation_chapter ); ?>">
			<i class="sui-icon-academy"></i>
			<?php esc_html_e( 'View Documentation', 'ub' ); ?>
		</a>
<?php endif; ?>
	</div>
</div>