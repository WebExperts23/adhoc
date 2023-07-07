<?php
/**
 * Handles the Coming Soon State of WooCommerce Products Gutenberg BLock.
 *
 * @category class
 * @package  ComingSoon
 */

namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM;

use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Settings;
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\ComingSoon;
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\ComingSoonWidget;
use Automattic\WooCommerce\Blocks\Utils\BlocksWpQuery;

/**
 * Coming Soon Blocks - Shortcodes Class
 */
class ComingSoonGutenberg extends ComingSoon {

	/**
	 * BLock Name.
	 *
	 * @var string
	 */
	public $block_name;

	/**
	 * Block Attributes Schema.
	 *
	 * @var array
	 */
	protected $attributes_schema = array();

	/**
	 * Default Attributes.
	 *
	 * @var array
	 */
	protected $default_attributes = array();

	/**
	 * Attributes.
	 */
	protected $attributes = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->block_name        = self::$plugin_info['name'] . '/coming-soon-products-block';
		$this->attributes_schema = array(
			'columns'     => array(
				'type'    => 'integer',
				'default' => 3,
			),
			'rows'        => array(
				'type'    => 'integer',
				'default' => 4,
			),
			'className'   => array(
				'type'    => 'string',
				'default' => self::$plugin_info['classes_prefix'] . '-coming-soon-products-block',
			),
			'title'       => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'rating'      => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'price'       => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'cart'        => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'coming_soon' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'countdown'   => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'badge'       => array(
				'type'    => 'boolean',
				'default' => true,
			),
		);

		$this->default_attributes = array(
			'columns'     => 3,
			'rows'        => 4,
			'className'   => self::$plugin_info['classes_prefix'] . '-coming-soon-products-block',
			'title'       => true,
			'rating'      => true,
			'price'       => true,
			'cart'        => true,
			'coming_soon' => false,
			'countdown'   => true,
			'badge'       => true,
		);

		$this->hooks();
	}

	/**
	 * Hooks Function.
	 *
	 * @return void
	 */
	public function hooks() {
		// Coming Soon Products Shortcode.
		add_action( 'init', array( $this, 'register_coming_soon_products_block' ), 100 );

		add_action( 'enqueue_block_editor_assets', array( $this, 'block_assets' ) );
	}

	/**
	 * Block Assets.
	 *
	 * @return void
	 */
	public function block_assets() {
		ComingSoonFrontend::front_assets();
	}

	/**
	 * Register Coming Soon Products Gutenberg Block.
	 *
	 * @return void
	 */
	public function register_coming_soon_products_block() {
		wp_register_script( self::$plugin_info['name'] . '-gutenberg-block-js', self::$plugin_info['url'] . 'assets/dist/js/gutenberg-block.min.js', array( 'jquery', 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ), self::$plugin_info['version'], true );
		wp_localize_script(
			self::$plugin_info['name'] . '-gutenberg-block-js',
			str_replace( '-', '_', self::$plugin_info['name'] . '_localize_vars' ),
			array(
				'name'           => self::$plugin_info['name'],
				'classes_prefix' => self::$plugin_info['classes_prefix'],
				'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( self::$plugin_info['name'] . '-ajax-nonce' ),
				'labels'         => array(
					'headers'    => array(
						'blockName' => esc_html__( 'Coming Soon Products', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'layout'    => esc_html__( 'Layout', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'content'   => esc_html__( 'Content', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
					),
					'subHeaders' => array(
						'description' => esc_html__( 'Display a grid of coming soon products', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'keywords'    => esc_html__( 'coming soon', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'media'       => esc_html__( 'Media', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'columns'     => esc_html__( 'Columns', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'rows'        => esc_html__( 'Rows', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'title'       => esc_html__( 'Product Title', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'price'       => esc_html__( 'Product Price', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'rating'      => esc_html__( 'Product Rating', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'cart'        => esc_html__( 'Add to Cart Button', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'coming_soon' => esc_html__( 'Coming Soon Text', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'countdown'   => esc_html__( 'Coming Soon Countdown', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
						'badge'       => esc_html__( 'Coming Soon Badge', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
					),
					'msgs'       => array(
						'countdown_editor_limit' => esc_html__( 'The countdown will not be shown here in editor if enabled due to the gutenberg editor limitations, but it will be shown on frontend', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
					),
				),
				'attrs'          => $this->attributes_schema,
			)
		);

		register_block_type(
			$this->block_name,
			array(
				'api_version'     => 2,
				'attributes'      => $this->attributes_schema,
				'editor_script'   => self::$plugin_info['name'] . '-gutenberg-block-js',
				'render_callback' => array( $this, 'render' ),
			)
		);
	}

	/**
	 * Render The Block.
	 *
	 * @param array  $block_attributes
	 * @param array  $content
	 * @param object $block
	 * @return string
	 */
	public function render( $block_attributes, $content, $block ) {
		$coming_soon_products_ids = self::get_coming_soon_list();
		$this->attributes         = $this->parse_attributes( $block_attributes );
		if ( empty( $coming_soon_products_ids ) ) {
			return '';
		}
		$query_args              = self::$query_args;
		$query_args['post__in']  = $coming_soon_products_ids;
		$query_args['post_type'] = array( 'product' );

		$this->set_categories_query_args( $query_args );
		$this->set_products_limit( $query_args );
		$query    = new \WP_Query( $query_args );
		$products = array_filter( array_map( 'wc_get_product', $query->get_posts() ) );
		if ( ! $products ) {
			return '';
		}
		return sprintf(
			'<div class="%s"><ul class="wc-block-grid__products">%s</ul></div>',
			esc_attr( $this->get_container_classes( $block_attributes ) ),
			implode(
				'',
				array_map(
					function( $product ) use ( $block_attributes ) {
						return $this->render_product( $product, $block_attributes );
					},
					$products
				)
			)
		);
	}

	/**
	 * Parse Attributes.
	 *
	 * @param array $attributes
	 * @return array
	 */
	public function parse_attributes( $attributes ) {
		$attributes = wp_parse_args( $attributes, $this->default_attributes );
		return $attributes;
	}

	/**
	 * Render a single products.
	 *
	 * @param \WC_Product $product Product object.
	 * @return string Rendered product output.
	 */
	protected function render_product( $product, $attributes ) {
		$data = (object) array(
			'permalink'   => esc_url( $product->get_permalink() ),
			'image'       => $this->get_image_html( $product ),
			'title'       => $this->get_title_html( $product ),
			'rating'      => $this->get_rating_html( $product ),
			'price'       => $this->get_price_html( $product ),
			'badge'       => $this->get_sale_badge_html( $product ),
			'coming_soon' => $this->get_coming_soon_html( $product ),
			'countdown'   => $this->get_countdown_html( $product ),
			'button'      => $this->get_button_html( $product ),
		);

		return apply_filters(
			'woocommerce_blocks_product_grid_item_html',
			"<li class=\"wc-block-grid__product\">
				<a href=\"{$data->permalink}\" class=\"wc-block-grid__product-link\">
					{$data->image}
					{$data->title}
				</a>
				{$data->badge}
				{$data->price}
				{$data->rating}
				{$data->coming_soon}
				{$data->countdown}
				{$data->button}
			</li>",
			$data,
			$product
		);
	}

	/**
	 * Get the list of classes to apply to this block.
	 *
	 * @return string space-separated list of classes.
	 */
	protected function get_container_classes() {
		$classes = array(
			'wc-block-grid',
			"wp-block-{$this->block_name}",
			"wc-block-{$this->block_name}",
			"has-{$this->attributes['columns']}-columns",
		);

		if ( $this->attributes['rows'] > 1 ) {
			$classes[] = 'has-multiple-rows';
		}

		if ( isset( $this->attributes['align'] ) ) {
			$classes[] = "align{$this->attributes['align']}";
		}

		if ( ! empty( $this->attributes['alignButtons'] ) ) {
			$classes[] = 'has-aligned-buttons';
		}

		if ( ! empty( $this->attributes['className'] ) ) {
			$classes[] = $this->attributes['className'];
		}

		return implode( ' ', $classes );
	}

	/**
	 * Get Coming SOon Text HTML.
	 *
	 * @param object $product
	 * @return void
	 */
	protected function get_coming_soon_html( $product ) {
		if ( empty( $this->attributes['coming_soon'] ) ) {
			return '';
		}
		ob_start();
		ComingSoonFrontend::coming_soon_text( $product->get_id() );
		$coming_soon_text = ob_get_clean();
		if ( empty( $coming_soon_text ) ) {
			return;
		}
		return '<div class="wc-block-grid__product-coming-soon-text">' . wp_kses_post( $coming_soon_text ) . '</div>';
	}

	/**
	 * Get Countdown HTML.
	 *
	 * @param object $product
	 * @return void
	 */
	protected function get_countdown_html( $product ) {
		if ( empty( $this->attributes['countdown'] ) ) {
			return '';
		}
		ob_start();
		if ( self::is_product_coming_soon( $product->get_id() ) ) {
			ComingSoonFrontend::countdown_section( $product->get_id() );
		}
		$countdown = ob_get_clean();
		if ( empty( $countdown ) ) {
			return;
		}
		return '<div class="wc-block-grid__product-coming-soon-countdown">' . wp_kses_post( $countdown ) . '</div>';
	}

	/**
	 * Get Badge HTML.
	 *
	 * @param object $product
	 * @return void
	 */
	protected function get_soon_badge_html( $product ) {
		if ( empty( $this->attributes['badge'] ) ) {
			return '';
		}
		$badge = ComingSoonFrontend::add_coming_soon_badge_for_blocks( $product );
		if ( empty( $badge ) ) {
			return;
		}
		return wp_kses_post( $badge );
	}
	/**
	 * Get the product image.
	 *
	 * @param \WC_Product $product Product.
	 * @return string
	 */
	protected function get_image_html( $product ) {
		return ComingSoonFrontend::coming_soon_badge_wrapper_start() . '<div class="wc-block-grid__product-image">' . ( ! empty( $this->attributes['badge'] ) ? ComingSoonFrontend::add_coming_soon_badge_for_blocks( $product ) : '' ) . wp_kses_post( $product->get_image( ( $this->attributes['columns'] > 1 ? 'woocommerce_thumbnail' : 'woocommerce_single' ) ) ) . '</div>' . ComingSoonFrontend::coming_soon_badge_wrapper_end(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get the product title.
	 *
	 * @param \WC_Product $product Product.
	 * @return string
	 */
	protected function get_title_html( $product ) {
		if ( empty( $this->attributes['title'] ) ) {
			return '';
		}
		$product_title = is_a( $product, \WC_Product_Variation::class ) ? $product->get_name() : $product->get_title();
		return '<div class="wc-block-grid__product-title">' . wp_kses_post( $product_title ) . '</div>';
	}

	/**
	 * Render the rating icons.
	 *
	 * @param WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_rating_html( $product ) {
		if ( empty( $this->attributes['rating'] ) ) {
			return '';
		}
		$rating_count = $product->get_rating_count();
		$review_count = $product->get_review_count();
		$average      = $product->get_average_rating();

		if ( $rating_count > 0 ) {
			return sprintf(
				'<div class="wc-block-grid__product-rating">%s</div>',
				wc_get_rating_html( $average, $rating_count ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}
		return '';
	}

	/**
	 * Get the price.
	 *
	 * @param \WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_price_html( $product ) {
		if ( empty( $this->attributes['price'] ) ) {
			return '';
		}
		return sprintf(
			'<div class="wc-block-grid__product-price price">%s</div>',
			wp_kses_post( $product->get_price_html() )
		);
	}

	/**
	 * Get the sale badge.
	 *
	 * @param \WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_sale_badge_html( $product ) {
		if ( empty( $this->attributes['price'] ) ) {
			return '';
		}

		if ( ! $product->is_on_sale() ) {
			return;
		}

		return '<div class="wc-block-grid__product-onsale">
			<span aria-hidden="true">' . esc_html__( 'Sale', 'woocommerce' ) . '</span>
			<span class="screen-reader-text">' . esc_html__( 'Product on sale', 'woocommerce' ) . '</span>
		</div>';
	}

	/**
	 * Get the button.
	 *
	 * @param \WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_button_html( $product ) {
		if ( empty( $this->attributes['cart'] ) ) {
			return '';
		}
		return '<div class="wp-block-button wc-block-grid__product-add-to-cart">' . wp_kses_post( $this->get_add_to_cart( $product ) ) . '</div>';
	}

	/**
	 * Get the "add to cart" button.
	 *
	 * @param \WC_Product $product Product.
	 * @return string Rendered product output.
	 */
	protected function get_add_to_cart( $product ) {
		$attributes = array(
			'aria-label'       => $product->add_to_cart_description(),
			'data-quantity'    => '1',
			'data-product_id'  => $product->get_id(),
			'data-product_sku' => $product->get_sku(),
			'rel'              => 'nofollow',
			'class'            => 'wp-block-button__link add_to_cart_button',
		);

		if (
			$product->supports( 'ajax_add_to_cart' ) &&
			$product->is_purchasable() &&
			( $product->is_in_stock() || $product->backorders_allowed() )
		) {
			$attributes['class'] .= ' ajax_add_to_cart';
		}

		return sprintf(
			'<a href="%s" %s>%s</a>',
			esc_url( $product->add_to_cart_url() ),
			wc_implode_html_attributes( $attributes ),
			esc_html( $product->add_to_cart_text() )
		);
	}


	/**
	 * Categories Query args.
	 *
	 * @param array $query_args
	 * @return void
	 */
	protected function set_categories_query_args( &$query_args ) {
		// TODO: will be added in later versions.
		if ( ! empty( $this->attributes['categories'] ) ) {
			$categories = array_map( 'absint', $this->attributes['categories'] );

			$query_args['tax_query'][] = array(
				'taxonomy'         => 'product_cat',
				'terms'            => $categories,
				'field'            => 'term_id',
				'operator'         => 'all' === $this->attributes['catOperator'] ? 'AND' : 'IN',
				'include_children' => 'all' === $this->attributes['catOperator'] ? false : true,
			);
		}
	}

	/**
	 * Set Products Limit.
	 *
	 * @param array $query_args
	 * @return void
	 */
	protected function set_products_limit( &$query_args ) {
		if ( isset( $this->attributes['rows'], $this->attributes['columns'] ) && ! empty( $this->attributes['rows'] ) ) {
			$query_args['posts_per_page'] = intval( $this->attributes['columns'] * $this->attributes['rows'] );
		}
	}
}
