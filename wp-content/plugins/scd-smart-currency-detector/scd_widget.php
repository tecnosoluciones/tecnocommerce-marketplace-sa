<?php

class SCD_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
                'scd_widget', __('SCD Widget', 'ch_scd_woo'), array('description' => __('Lets the visitor choose their preferred currency', 'ch_scd_woo'),)
        );
    }

    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

        echo do_shortcode('[scd_widget]');

        echo $args['after_widget'];
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Set currency', 'ch_scd_woo');
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

}

function scd_getChecked($opgroup, $option) {

    $resul = false;

    if (is_array(get_option($opgroup, $default = false)) && (array_key_exists($option, get_option($opgroup)))) {
        $resul = true;
    }

    return $resul;
}

function scd_init_stat() {
    $pbc = get_option('scd_currency_options');
//    var_dump($pbc);
    $res = isset($_POST['targetSessionName']) ? strtoupper(sanitize_key($_POST['targetSessionName'])) : "";

    $dn = ( isset($pbc['decimalNumber']) ) ? $pbc['decimalNumber'] : "";
    $pc = ( isset($pbc['priceByCurrency']) ) ? $pbc['priceByCurrency'] : "";

    if (scd_getChecked('scd_currency_options', 'decimalNumber') && scd_getChecked('scd_currency_options', 'priceByCurrency')) {

        $out = array(
            "userCurrencyChoice" => $pbc['userCurrencyChoice'],
            "decimalNumber" => $pbc['decimalNumber'],
            "decimalPrecision" => $pbc['decimalPrecision'],
            "priceByCurrency" => $pbc['priceByCurrency'],
            "thousandSeperator" => $pbc['thousandSeperator'],
            "fallbackCurrency" => $pbc['fallbackCurrency'],
            "targetSession" => $res
        );
    } elseif (scd_getChecked('scd_currency_options', 'decimalNumber') && !scd_getChecked('scd_currency_options', 'priceByCurrency')) {

        $out = array(
            "userCurrencyChoice" => $pbc['userCurrencyChoice'],
            "decimalNumber" => $pbc['decimalNumber'],
            "decimalPrecision" => $pbc['decimalPrecision'],
            "thousandSeperator" => $pbc['thousandSeperator'],
            "fallbackCurrency" => $pbc['fallbackCurrency'],
            "targetSession" => $res
        );
    } elseif (!scd_getChecked('scd_currency_options', 'decimalNumber') && scd_getChecked('scd_currency_options', 'priceByCurrency')) {

        $out = array(
            "userCurrencyChoice" => $pbc['userCurrencyChoice'],
            "decimalPrecision" => $pbc['decimalPrecision'],
            "priceByCurrency" => $pbc['priceByCurrency'],
            "thousandSeperator" => $pbc['thousandSeperator'],
            "fallbackCurrency" => $pbc['fallbackCurrency'],
            "targetSession" => $res
        );
    } else {

        $out = array(
            "userCurrencyChoice" => $pbc['userCurrencyChoice'],
            "decimalPrecision" => $pbc['decimalPrecision'],
            "thousandSeperator" => $pbc['thousandSeperator'],
            "fallbackCurrency" => $pbc['fallbackCurrency'],
            "targetSession" => $res
        );
    }

    if (!empty($res)) {
     //   update_option('scd_currency_options', $out);
    }
    add_action('widgets_init', 'scd_load_widget');
}

function scd_load_widget() {
    register_widget('SCD_Widget');
}
