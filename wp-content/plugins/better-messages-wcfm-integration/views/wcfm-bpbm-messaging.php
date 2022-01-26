<?php
global $WCFM, $wp_query;
?>
<div class="collapse wcfm-collapse" id="wcfm_bpbm_messages">
    <div class="wcfm-page-headig">
        <span class="wcfmfa fa-comments"></span>
        <span class="wcfm-page-heading-text"><?php _e('Messages', 'bp-better-messages' ); ?></span>
        <?php do_action( 'wcfm_page_heading' ); ?>
    </div>
    <div class="wcfm-collapse-content" style="padding: 0">
        <div id="wcfm_page_load"></div>
        <?php do_action( 'before_wcfm_bpbm_messages' ); ?>

        <div class="wcfm-clearfix"></div>

        <div class="wcfm-container" style="padding: 0;margin: 0">
            <div id="wcfm_bpbm_messages_expander" class="wcfm-content" style="margin: 0;padding: 0;">
                <?php echo BP_Better_Messages()->functions->get_page( true ); ?>
                <div class="wcfm-clearfix"></div>
            </div>
            <div class="wcfm-clearfix"></div>
        </div>

        <div class="wcfm-clearfix"></div>
        <?php
        do_action( 'after_wcfm_bpbm_messages' );
        ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        var container = jQuery('#wcfm-main-content .wcfm-content-container');
        var totalHeight = container.outerHeight();

        var headerHeight = container.find('.wcfm-page-headig').outerHeight();
        var resultHeight = totalHeight - headerHeight;

        container.find('.bp-messages-threads-wrapper').css({
            'height': resultHeight,
            'max-height' : resultHeight
        });

        BP_Messages['max_height'] = resultHeight;
    });
</script>