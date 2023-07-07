<?php
/**
 * Handles the Coming Soon State of WooCommerce Variations Products Backend Side.
 *
 * @category class
 * @package  ComingSoon
 */

namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM;

use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Settings;
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\ComingSoonBackend;
use ReflectionObject;

/**
 * Coming Soon Class
 */
class ComingSoonVariationBackend extends ComingSoonBackend {

	/**
	 * Custom Fields that needs Visual Editor in WPML.
	 *
	 * @var array
	 */
	private static $visual_wpml_custom_fields = array();

	/**
	 * Custom Fields for variations labels that need fix.
	 *
	 * @var array
	 */
	private static $wpml_custom_fields_needs_fix = array();
	/**
	 * Constructor
	 */
	public function __construct() {
		self::$visual_wpml_custom_fields = array(
			self::$plugin_info['name'] . '-coming_soon_text',
			self::$plugin_info['name'] . '-custom_email_body',
		);

		self::$wpml_custom_fields_needs_fix = array(
			self::$plugin_info['name'] . '-custom_subscription_title',
			self::$plugin_info['name'] . '-custom_email_subject',
			self::$plugin_info['name'] . '-custom_email_heading',
		);

		$this->hooks();
	}

	/**
	 * Hooks Function.
	 *
	 * @return void
	 */
	public function hooks() {
		// Coming Soon Settings in Products edit page.
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'coming_soon_for_variation' ), 1000, 3 );

		// Save Variation Coming Soon settings.
		add_action( 'woocommerce_ajax_save_product_variations', array( $this, 'save_variations_coming_soon_settings' ), 10, 1 );
	}

	/**
	 * Save Variations Coming Soon Settings.
	 *
	 * @param int $product_id
	 * @return void
	 */
	public function save_variations_coming_soon_settings( $product_id ) {
		if ( ! empty( $_POST[ self::$plugin_info['name'] . '-variation-coming-soon-field' ] ) ) {
			$coming_soon_settings = wp_unslash( $_POST[ self::$plugin_info['name'] . '-variation-coming-soon-field' ] );
			foreach ( $coming_soon_settings as $variation_id => $variation_coming_soon_settings ) {
				$variation_id      = absint( sanitize_text_field( $variation_id ) );
				$variation_product = wc_get_product( $variation_id );
				if ( ! $variation_product || ( $variation_product && 'variation' !== $variation_product->get_type() ) ) {
					continue;
				}
				$settings = self::$default_settings;
				if ( ! empty( $variation_coming_soon_settings['coming-soon-status'] ) ) {
					$settings['status'] = 'yes';
				}

				if ( ! empty( $variation_coming_soon_settings['arrival-time'] ) ) {
					$settings['arrival_time'] = sanitize_text_field( $variation_coming_soon_settings['arrival-time'] );
				}

				if ( ! empty( $variation_coming_soon_settings['coming-soon-text'] ) ) {
					$settings['coming_soon_text'] = wp_kses_post( $variation_coming_soon_settings['coming-soon-text'] );
				} else {
					$settings['coming_soon_text'] = '';
				}

				if ( ! empty( $variation_coming_soon_settings['hide-price'] ) ) {
					$settings['hide_price'] = 'yes';
				}

				if ( ! empty( $variation_coming_soon_settings['disable-backorders'] ) ) {
					$settings['disable_backorders'] = 'yes';
				}

				if ( ! empty( $variation_coming_soon_settings['show-countdown'] ) ) {
					$settings['show_countdown'] = sanitize_text_field( $variation_coming_soon_settings['show-countdown'] );
				}

				if ( ! empty( $variation_coming_soon_settings['auto-enable'] ) ) {
					$settings['auto_enable'] = 'yes';
				}

				// Coming Soon Email Fields.
				if ( ! empty( $variation_coming_soon_settings['auto-email'] ) ) {
					$settings['auto_email'] = 'yes';
				}

				// Subscription Form Fields.
				if ( ! empty( $variation_coming_soon_settings['show-subscription-form'] ) ) {
					$settings['show_subscription_form'] = 'yes';
				}

				if ( ! empty( $variation_coming_soon_settings['custom-subscription-title'] ) ) {
					$settings['custom_subscription_title'] = sanitize_text_field( $variation_coming_soon_settings['custom-subscription-title'] );
				}

				if ( ! empty( $variation_coming_soon_settings['custom-subscription-form'] ) ) {
					$settings['custom_subscription_form'] = sanitize_text_field( wp_unslash( $variation_coming_soon_settings['custom-subscription-form'] ) );
				}

				if ( ! empty( $variation_coming_soon_settings['custom-email-subject'] ) ) {
					$settings['custom_email_subject'] = sanitize_text_field( $variation_coming_soon_settings['custom-email-subject'] );
				}

				if ( ! empty( $variation_coming_soon_settings['custom-email-heading'] ) ) {
					$settings['custom_email_heading'] = sanitize_text_field( $variation_coming_soon_settings['custom-email-heading'] );
				}

				if ( ! empty( $variation_coming_soon_settings['custom-email-body'] ) ) {
					$settings['custom_email_body'] = wp_kses_post( $variation_coming_soon_settings['custom-email-body'] );
				}

				foreach ( $settings as $setting_name => $setting_value ) {
					self::update_setting( $variation_id, $setting_name, $setting_value );
				}

				if ( 'yes' === $settings['status'] ) {
					self::update_coming_soon_list( $variation_id );
				} else {
					self::update_coming_soon_list( $variation_id, 'remove' );
				}
			}
		}
	}

	/**
	 * Coming Soon Settings for Variation Data.
	 *
	 * @param int    $loop
	 * @param array  $variation_data
	 * @param object $variation
	 * @return void
	 */
	public function coming_soon_for_variation( $loop, $variation_data, $variation ) {
		$coming_soon_settings = self::get_settings( $variation->ID );
		?>
		<div class="<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-variation-coming-soon-box wc-metabox woocommerce_attribute postbox closed' ); ?>">
			<h3>
				<div class="handlediv" aria-expanded="true" title="Click to toggle"></div>
				<div class="attribute_name"><?php esc_html_e( 'Coming Soon', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></div>
			</h3>
			<div class="woocommerce_attribute_data wc-metabox-content hidden">
				<div class="woocommerce_options_panel">
				<?php if ( self::$core->is_active( true, true ) ) : ?>
					<div class="options_group">
					<?php
						woocommerce_wp_checkbox(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-coming-soon-status',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][coming-soon-status]',
								'value'       => $coming_soon_settings['status'],
								'label'       => esc_html__( 'Coming Soon Mode', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field',
								'description' => esc_html__( 'Enable coming soon mode', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
							)
						);
						woocommerce_wp_textarea_input(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-coming-soon-text',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][coming-soon-text]',
								'value'       => $coming_soon_settings['coming_soon_text'],
								'label'       => esc_html__( 'Coming Soon Text', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field' . ' ' . self::$plugin_info['name'] . '-variation-texteditor',
								'description' => esc_html__( 'It will be shown in single product page after product short description', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
							)
						);
						woocommerce_wp_text_input(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-arrival-time',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][arrival-time]',
								'type'        => 'datetime-local',
								'value'       => $coming_soon_settings['arrival_time'],
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field',
								'label'       => esc_html__( 'Arrival Time', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'description' => esc_html__( 'Remaining time until arrival is calculated based on the site\'s timezone', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'desc_tip'    => true,
							)
						);
						woocommerce_wp_checkbox(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-show-countdown',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][show-countdown]',
								'value'       => $coming_soon_settings['show_countdown'],
								'label'       => esc_html__( 'Show Countdown', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field',
								'description' => esc_html__( 'Show the arrival time countdown', 'gpls-wcsamm-coming-soon-for-woocommerce' ) . ' <br/> ' . esc_html__( 'Countdown Shortcode ', 'gpls-wcsamm-coming-soon-for-woocommerce' ) . ':   [' . self::$plugin_info['classes_prefix'] . '-coming-soon-countdown id="' . $variation->ID . '"]',
							)
						);

						woocommerce_wp_checkbox(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-hide-price',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][hide-price]',
								'value'       => $coming_soon_settings['hide_price'],
								'label'       => esc_html__( 'Hide Price', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field',
								'description' => esc_html__( 'Hide the product price', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
							)
						);
						woocommerce_wp_checkbox(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-disable-backorders',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][disable-backorders]',
								'value'       => $coming_soon_settings['disable_backorders'],
								'label'       => esc_html__( 'Disable Backorders', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field',
								'description' => esc_html__( 'Disable purchasing the product in backorder', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
							)
						);
						woocommerce_wp_checkbox(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-auto-enable',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][auto-enable]',
								'value'       => $coming_soon_settings['auto_enable'],
								'label'       => esc_html__( 'Auto Enable', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field',
								'description' => esc_html__( 'Auto enable the product for purchase when the arrival time is over [ requires "Arrival Time" ]', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
							)
						);
						woocommerce_wp_checkbox(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-auto-email',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][auto-email]',
								'value'       => $coming_soon_settings['auto_email'],
								'label'       => esc_html__( 'Auto Email', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field',
								'description' => esc_html__( 'Send email automatically when the product arrival time is over [ requires "Arrival Time" and "Auto Enable" ]', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
							)
						);
						?>
					</div>
					<div class="options_group">
						<?php
						woocommerce_wp_checkbox(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-show-subscription-form',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][show-subscription-form]',
								'value'       => $coming_soon_settings['show_subscription_form'],
								'label'       => esc_html__( 'Show Subscription', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field',
								'description' => esc_html__( 'Display the subscription form ', 'gpls-wcsamm-coming-soon-for-woocommerce' ) . ' <br/> ' . esc_html__( 'Subscription form Shortcode ', 'gpls-wcsamm-coming-soon-for-woocommerce' ) . ':   [' . self::$plugin_info['classes_prefix'] . '-subscription-form-shortcode id="' . $variation->ID . '"]',
							)
						);
						woocommerce_wp_text_input(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-custom-subscription-title',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][custom-subscription-title]',
								'value'       => $coming_soon_settings['custom_subscription_title'],
								'label'       => esc_html__( 'Custom Title', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field',
								'description' => esc_html__( 'Custom Title for the Subscription Form', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
							)
						);
						woocommerce_wp_text_input(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-custom-subscription-form',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][custom-subscription-form]',
								'value'       => $coming_soon_settings['custom_subscription_form'],
								'label'       => esc_html__( 'Custom Form Shortcode', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field',
								'description' => esc_html__( 'Add custom subscription form shortcode. leave it blank for using the default form', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'desc_tip'    => true,
							)
						);
						?>
					</div>
					<div class="options_group">
						<h4 class="heading-title"><?php esc_html_e( 'Coming Soon Ended Email', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h4>
						<?php
						woocommerce_wp_text_input(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-custom-email-subject',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][custom-email-subject]',
								'value'       => $coming_soon_settings['custom_email_subject'],
								'label'       => esc_html__( 'Email Subject', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field',
								'description' => esc_html__( 'Custom email subject for this product, available placeholders: {site_title}, {site_url}, {site_address}, {product_title}, {product_image}, {product_stock}, {product_link_start}, {product_link_end}', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'desc_tip'    => true,
							)
						);
						woocommerce_wp_text_input(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-custom-email-heading',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][custom-email-heading]',
								'value'       => $coming_soon_settings['custom_email_heading'],
								'label'       => esc_html__( 'Email Heading', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field',
								'description' => esc_html__( 'Custom email heading for this product, available placeholders: {site_title}, {site_url}, {site_address}, {product_title}, {product_image}, {product_stock}, {product_link_start}, {product_link_end}', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'desc_tip'    => true,
							)
						);
						woocommerce_wp_textarea_input(
							array(
								'id'          => self::$plugin_info['name'] . '-variation-coming-soon-field-' . $variation->ID . '-custom-email-body',
								'name'        => self::$plugin_info['name'] . '-variation-coming-soon-field[' . $variation->ID . '][custom-email-body]',
								'value'       => $coming_soon_settings['custom_email_body'],
								'label'       => esc_html__( 'Email Body', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'class'       => self::$plugin_info['classes_prefix'] . '-variation-coming-soon-field ' . self::$plugin_info['name'] . '-variation-texteditor',
								'description' => esc_html__( 'Custom email Body for this product, available placeholders: {site_title}, {site_url}, {site_address}, {product_title}, {product_image}, {product_stock}, {product_link_start}, {product_link_end}', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
								'desc_tip'    => true,
							)
						);
						?>
					</div>
					<?php
					do_action( self::$plugin_info['name'] . '-coming-soon-variation-product-fields', $variation->ID );
				endif;
				?>
				</div>
			</div>
		</div>
			<?php
	}
}
