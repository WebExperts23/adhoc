<?php
/**
 * Handles the Coming Soon State of WooCommerce Products Backend Side.
 *
 * @category class
 * @package  ComingSoon
 */

namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM;

use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Settings;
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\ComingSoon;

/**
 * Coming Soon Products Shortcode.
 */
class ComingSoonShortCode extends ComingSoon {

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

		// Coming Soon Products Shortcode.
		add_action( 'init', array( $this, 'coming_soon_shortcodes' ) );
		add_filter( 'woocommerce_shortcode_products_query', array( $this, 'coming_soon_products_query' ), 10000, 3 );
	}

	/**
	 * Coming Soon Products Shortcodes.
	 *
	 * @return void
	 */
	public function coming_soon_shortcodes() {
		add_shortcode( 'wcsamm-soon-products', array( $this, 'coming_soon_products_loop' ) );
	}

	/**
	 * Coming Soon Products Loop.
	 *
	 * @param array $atts
	 *
	 * @return void
	 */
	public function coming_soon_products_loop( $atts ) {
		$coming_soon_products_ids = self::get_coming_soon_list();
		if ( ! empty( $coming_soon_products_ids ) ) {
			$shortcode = new \WC_Shortcode_Products( $atts, 'wcsamm-soon-products' );
			return $shortcode->get_content();
		}
		return;
	}

	/**
	 * Coming Soon Query Args.
	 *
	 * @param array  $query_args
	 * @param array  $attributes
	 * @param string $type
	 * @return array
	 */
	public function coming_soon_products_query( $query_args, $attributes, $type ) {
		if ( 'wcsamm-soon-products' === $type ) {
			$coming_soon_products_ids = self::get_coming_soon_list();
			$query_args['post__in']   = $coming_soon_products_ids;
			$query_args['post_type']  = array( 'product' );
		}
		return $query_args;
	}
}
