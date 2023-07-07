<?php
/**
 * Handles the Coming Soon State of WooCommerce Products.
 *
 * @category class
 * @package  ComingSoon
 */

namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM;

use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Settings;

/**
 * Coming Soon Compatibility with Avada Theme and Builder Class.
 */
class ComingSoonAvada extends ComingSoon {
	/**
	 * Avada Elements Names Array.
	 */
	private $elements_names = array();
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->setup();
		$this->hooks();
	}

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		$this->elements_names = array(
			'add_to_cart'           => 'fusion_tb_woo_cart',
			'price'                 => 'fusion_tb_woo_price',
			'post_card_add_to_cart' => 'fusion_post_card_cart',
		);
	}

	/**
	 * Hooks Function.
	 *
	 * @return void
	 */
	public function hooks() {
		// Woo Elements.
		add_filter( 'fusion_woo_component_content', array( $this, 'filter_woo_coming_soon_product' ), PHP_INT_MAX, 3 );
		// Post Card Cart Element.
		add_filter( 'fusion_element_post_card_cart_content', array( $this, 'filter_post_card_cart_btn_coming_soon_product' ), PHP_INT_MAX, 2 );
	}

	/**
	 * Filter Product Price - Add To Cart Woo Elements for Coming Soon Product.
	 *
	 * @param string $content
	 * @param string $shortcode_handle
	 * @param array  $args
	 * @return string
	 */
	public function filter_woo_coming_soon_product( $content, $shortcode_handle, $args ) {
		global $product;
		$product_id = $product->get_id();
		$settings   = self::get_settings( $product_id );

		// Handle Add To Cart.
		if ( $shortcode_handle === $this->elements_names['add_to_cart'] && self::is_product_coming_soon( $product_id ) ) {
			$content = preg_replace( '/<form class="variations_form cart"[^>]*>(.*?)<\/form>/is', '', $content );
		}

		// Handle Price.
		if ( $shortcode_handle === $this->elements_names['price'] && self::is_product_coming_soon( $product_id ) && ( 'yes' === $settings['hide_price'] ) ) {
			$content = preg_replace( '/<p class="price[^>]*>(.*?)<\/p>/is', '', $content );
		}

		return $content;
	}

	/**
	 * Filter Add To Cart Of Post Card Element For Coming SOon Product.
	 *
	 * @param string $content
	 * @param array  $args
	 * @return string
	 */
	public function filter_post_card_cart_btn_coming_soon_product( $content, $args ) {
        // To Be used later.
		return $content;
	}
}
