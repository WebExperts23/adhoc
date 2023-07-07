<?php
/**
 * Handles the Coming Soon State of WooCommerce Variations Products Frontend Side.
 *
 * @category class
 * @package  ComingSoon
 */

namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM;

use DateTime;
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Settings;
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\ComingSoonFrontend;

/**
 * Coming Soon Class
 */
class ComingSoonVariationFrontend extends ComingSoonFrontend {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Hooks Function.
	 *
	 * @return void
	 */
	public function hooks() {
		// Show - Hide Price.
		add_filter( 'woocommerce_show_variation_price', array( $this, 'show_variation_price' ), PHP_INT_MAX, 3 );

		// Set coming_soon key in variation data.
		add_filter( 'woocommerce_available_variation', array( $this, 'woocommerce_avialable_variation_coming_soon_key' ), 1000, 3 );

		// Mark the variation as unpurchasable if it is coming soon.
		add_filter( 'woocommerce_variation_is_purchasable', array( $this, 'make_coming_soon_variation_unpurchasable' ), PHP_INT_MAX, 2 );

		// Variation Coming Soon Section Ajax.
		add_action( 'wp_ajax_' . self::$plugin_info['name'] . '-variation-coming-soon-section-action', array( $this, 'ajax_variation_coming_soon_section' ) );
		add_action( 'wp_ajax_nopriv_' . self::$plugin_info['name'] . '-variation-coming-soon-section-action', array( $this, 'ajax_variation_coming_soon_section' ) );
	}

	/**
	 * Set coming soon key in the variation array data.
	 *
	 * @param array  $variation_data
	 * @param object $variable_parent_product
	 * @param object $variaion_product
	 * @return array
	 */
	public function woocommerce_avialable_variation_coming_soon_key( $variation_data, $variable_parent_product, $variation_product ) {
		if ( self::is_product_coming_soon( $variation_product->get_id() ) ) {
			$variation_data[ self::$plugin_info['name'] . '-coming-soon-variation' ] = true;
		}
		return $variation_data;
	}

	/**
	 * Disable add to cart function of coming soon variation by making it unpurchasable.
	 *
	 * @param boolean $is_purchasable
	 * @param object  $product_obj
	 * @return boolean
	 */
	public function make_coming_soon_variation_unpurchasable( $is_purchasable, $product_obj ) {
		if ( is_null( $product_obj ) || empty( $product_obj ) || is_wp_error( $product_obj ) ) {
			return $is_purchasable;
		}
		// 1) Check the parent product first.
		if ( self::is_product_unpurchasable( $product_obj->get_parent_id() ) ) {
			return false;
		}
		// 2) Check the variation.
		if ( $this->is_product_unpurchasable( $product_obj->get_id() ) ) {
			return false;
		}
		return $is_purchasable;
	}

	/**
	 * show Variation Price.
	 *
	 * @param boolean $is_hidden
	 * @param object  $variable_product
	 * @param object  $variation_product
	 * @return boolean
	 */
	public function show_variation_price( $is_hidden, $variable_product, $variation_product ) {
		$show_variation_price = self::get_settings( $variation_product->get_id(), 'hide_price' );
		if ( 'yes' === $show_variation_price ) {
			return false;
		}
		return $is_hidden;
	}

	/**
	 * Ajax Variation Coming Soon Section.
	 *
	 * @return void
	 */
	public function ajax_variation_coming_soon_section() {
		if ( ! empty( $_POST['variation'] ) && ! empty( $_POST['nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['nonce'] ), self::$plugin_info['name'] . '-nonce' ) ) {
			$variation = wp_unslash( $_POST['variation'] );
			ob_start();
			$coming_soon_section = $this->variation_coming_soon_section( absint( sanitize_text_field( $variation['variation_id'] ) ) );
			wp_send_json_success(
				array(
					'coming_soon_section' => $coming_soon_section,
					'variation'           => $variation,
				)
			);
		}
		wp_send_json_error( '', 400 );
	}

	/**
	 * Get Variation Coming Soon Seciton.
	 *
	 * @param int $variation_id
	 * @return string
	 */
	private function variation_coming_soon_section( $variation_id ) {
		$product = wc_get_product( $variation_id );
		if ( ! $product ) {
			return '';
		}
		$settings = self::get_settings( $variation_id );
		if ( ! self::is_product_coming_soon( $variation_id ) ) {
			return;
		}
		ob_start();
		// 1) Coming Soon Text.
		self::coming_soon_text( $variation_id, $settings );

		// 2) Arrival Time Countdown.
		self::countdown_section( $variation_id, $settings );

		// 3) Subscription Form.
		$this->subscription_form( $variation_id );
		return ob_get_clean();
	}
}
