<?php
/**
 * Main class start.
 *
 * @package : frontclass
 */
 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'AF_C_S_P_Main' ) ) {

	/**
	 * Main class start.
	 */
	class AF_C_S_P_Main {

		private $af_price;

		/**
		 * Main __construct start.
		 */
		public function __construct() {

			$this->af_price = new AF_C_S_P_Price();

			// Change Price HTML.
			add_filter( 'woocommerce_get_price_html', array( $this, 'af_csp_custom_price_html' ), 100, 2 );

			add_filter( 'woocommerce_product_get_price', array( $this, 'af_csp_price_adjustment' ), 99, 2 );

			add_filter( 'woocommerce_product_variation_get_price', array( $this, 'af_csp_price_adjustment' ), 99, 2 );
			
			// Hide add to cart shop page.
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'csp_replace_loop_add_to_cart_link' ), 10, 2 );

			// Hide add to cart on product page.
			add_action( 'woocommerce_single_product_summary', array( $this, 'csp_hide_add_cart_product_page' ), 1, 0 );
		}

		/**
		 * Replace/hide add to cart button in product loop.
		 *
		 * @param string $html HTML of add to cart button.
		 *
		 * @param Object $product WC_Product class object.
		 */
		public function csp_replace_loop_add_to_cart_link( $html, $product ) {
			
			$_button_text = get_option( 'csp_cart_button_text' );
			$_button_link = get_option( 'csp_cart_button_link' );
		
			if ( $this->af_price->is_product_price_hidden( $product ) || $this->af_price->is_product_add_to_cart_button_hidden( $product ) ) {

				if ( empty( $_button_text ) ) {
					return;

				} else {

					ob_start();
					?>
					<a href="<?php echo esc_url( $_button_link ); ?>" class="button add_to_cart_button">
						<?php echo esc_html( $_button_text ); ?>
					</a>
					<?php
					return ob_get_clean();
				}	
			}
			
			return $html;
		}

		/**
		 * Replace custom price HTML of product.
		 *
		 * @param string $price HTML of price.
		 *
		 * @param Object $product WC_Product class Object.
		 */
		public function af_csp_custom_price_html( $price, $product ) {
			return $this->af_price->get_price_html_of_product($price, $product);
		}
		
		/**
		 * Apply role based pricing.
		 *
		 * @param float $price Price of product.
		 *
		 * @param Object $product WC_Product class object.
		 */
		public function af_csp_price_adjustment( $price, $product ) {

			$role_based_price = $this->af_price->get_price_of_product( $product );

			if ( $role_based_price ) {
				return $role_based_price;
			}

			return $price;
		}
		
		/**
		 * Hide add to cart on product page.
		 */
		public function csp_hide_add_cart_product_page() {

			global $product;

			if ( $this->af_price->is_product_price_hidden( $product ) || $this->af_price->is_product_add_to_cart_button_hidden( $product ) ) {

				if ( 'variable' == $product->get_type() ) {

					remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
					add_action( 'woocommerce_single_variation', array( $this, 'csp_custom_button_replacement' ), 30 );

				} else {

					remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
					add_action( 'woocommerce_simple_add_to_cart', array( $this, 'csp_custom_button_replacement' ), 40 );
				}
			}
		}

		/**
		 * Replace custom button with add to cart button.
		 */
		public function csp_custom_button_replacement() {

			$csp_cart_button_text = get_option( 'csp_cart_button_text' );
			$csp_cart_button_link = get_option( 'csp_cart_button_link' );

			if ( ! empty( $csp_cart_button_text ) ) {

				echo '<a href="' . esc_url( $csp_cart_button_link ) . '" rel="nofollow" class="button add_to_cart_button">' . esc_attr( $csp_cart_button_text ) . '</a>';

			} else {
				echo '';
			}

		}
	}

	new AF_C_S_P_Main();
}
