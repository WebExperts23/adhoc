<?php
namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM;

defined( 'ABSPATH' ) || exit;

/**
 * Include dependencies.
 */
if ( ! class_exists( 'WC_Email', false ) ) {
	include_once \WC_ABSPATH . 'includes/emails/class-wc-email.php';
}

if ( ! class_exists( __NAMESPACE__ . '\GPLS_WCSAMM_WC_Email_Coming_Soon_Mode', false ) ) :

	/**
	 * Coming Soon Mode Done Email.
	 *
	 * An email sent to the customer when a new order is paid for.
	 *
	 * @class       GPLS_WCSAMM_WC_Email_Coming_Soon_Mode
	 * @version     3.5.0
	 * @package     WooCommerce/Classes/Emails
	 * @extends     WC_Email
	 */
	class GPLS_WCSAMM_WC_Email_Coming_Soon_Mode extends \WC_Email {

		/**
		 * More Custom Placeholders to the email.
		 *
		 * @var array
		 */
		protected $custom_placeholders = array();

		/**
		 * Plugin Info array.
		 *
		 * @var array
		 */
		private static $plugin_info;

		/**
		 * Constructor.
		 *
		 * @param array $plugin_info Plugin Info Array.
		 */
		public function __construct( $plugin_info ) {
			$this->id             = $plugin_info['name'] . '-coming-soon-email-class';
			$this->title          = esc_html__( 'Coming Soon Product Available', 'woocommerce' );
			$this->customer_email = true;
			$this->manual         = true;
			$this->description    = esc_html__( 'This email is sent to coming-soon subscribers after the product is available. ', 'woocommerce' );
			$this->template_base  = apply_filters( $plugin_info['name'] . '-coming-soon-product-email-templates-base', $plugin_info['path'] . 'templates/' );
			$this->template_html  = 'emails/gpls-coming-soon-product-email-html.php';
			$this->template_plain = 'emails/gpls-coming-soon-product-email-plain.php';
			self::$plugin_info    = $plugin_info;

			// Triggers for this email.
			add_action( self::$plugin_info['classes_prefix'] . '-product-coming-soon-ended-email_notification', array( $this, 'trigger' ), 10, 2 );
			add_action( self::$plugin_info['classes_prefix'] . '-product-coming-soon-ended-email_notification_single', array( $this, 'trigger_single' ), 10, 2 );

			$this->init_before_parent();

			// Call parent constructor.
			parent::__construct();

			$this->init_after_parent();

			add_filter( 'woocommerce_settings_api_form_fields_' . $this->id, array( $this, 'email_settings_page' ), 10, 1 );

			add_filter( 'woocommerce_email_styles', array( $this, 'overwrite_default_email_styles' ), 100, 2 );

			add_filter( 'woocommerce_email_get_option', array( $this, 'filter_default_additional_content' ), 1000, 5 );
		}

		/**
		 * Init Before WC_Email init.
		 *
		 * @return void
		 */
		public function init_before_parent() {
			$this->placeholders = array(
				'{product_title}'      => '',
				'{product_image_link}' => '',
				'{product_stock}'      => '',
				'{product_link}'       => '',
			);
			$this->placeholders = apply_filters( self::$plugin_info['name'] . '-coming-soon-product-email-placeholders', $this->placeholders, $this->object );
		}

		/**
		 * Init After WC_Email init.
		 *
		 * @return void
		 */
		public function init_after_parent() {
			/* translators: %s: list of placeholders */
			$placeholder_text                            = sprintf( esc_html__( 'Available placeholders: %s', 'woocommerce' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
			$this->form_fields['additional_content']     = array(
				'title'             => esc_html__( 'Default Body', 'woocommerce' ),
				'description'       => esc_html__( 'The default email body unless a custom Email body is added in the product page.', 'woocommerce' ) . '<br/><br/>' . $placeholder_text,
				'css'               => 'max-width:1200px; min-height: 400px;',
				'placeholder'       => esc_html__( 'N/A', 'woocommerce' ),
				'type'              => 'textarea',
				'custom_attributes' => array(
					'spellcheck' => false,
				),
				'default'           => $this->get_default_additional_content(),
				'desc_tip'          => true,
			);
			$this->form_fields['subject']['description'] = esc_html__( 'The default email subject unless a custom subject is added in the product page. ', 'gpls-wcsamm-coming-soon-for-woocommerce' ) . '<br/><br/>' . $this->form_fields['subject']['description'];
			$this->form_fields['heading']['description'] = esc_html__( 'The default email heading unless a custom subject is added in the product page. ', 'gpls-wcsamm-coming-soon-for-woocommerce' ) . '<br/><br/>' . $this->form_fields['heading']['description'];
		}

		/**
		 * Default Styles.
		 *
		 * @return string
		 */
		public function get_default_styles() {
			ob_start();
			wc_get_template( 'emails/email-styles.php' );
			$css  = ob_get_clean();
			$css .= $this->get_inline_styles();
			return $css;
		}

		/**
		 * Overwrite the email default Styles to be editable.
		 *
		 * @param string $default_css
		 * @param object $email_obj
		 * @return string
		 */
		public function overwrite_default_email_styles( $default_css, $email_obj ) {
			if ( self::$plugin_info['name'] . '-coming-soon-email-class' === $email_obj->id ) {
				return $this->get_email_styles();
			}
			return $default_css;
		}

		/**
		 * Emails Settings Page
		 *
		 * @param array $fields Email Settings Fields.
		 * @return array
		 */
		public function email_settings_page( $fields ) {
			unset( $fields['enabled'] );

			$fields['email_styles'] = array(
				'title'       => esc_html__( 'Styles', 'woocommerce' ),
				'description' => esc_html__( 'The Email full css styles', 'woocommerce' ),
				'css'         => 'max-width:1200px;',
				'placeholder' => esc_html__( 'N/A', 'woocommerce' ),
				'type'        => 'textarea',
				'default'     => $this->get_default_styles(),
				'desc_tip'    => true,
			);
			return $fields;
		}

		/**
		 * Set Default Additional Content as email body HTML.
		 *
		 * @param string $key
		 * @param string $value
		 * @param object $email_obj
		 * @param string $empty_value
		 * @return string
		 */
		public function filter_default_additional_content( $value, $email_obj, $value_again, $key, $empty_value ) {
			if ( ( self::$plugin_info['name'] . '-coming-soon-email-class' === $email_obj->id ) && ( 'additional_content' === $key ) && empty( $value ) ) {
				$value = $this->get_default_additional_content();
			}
			return $value;
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return esc_html__( '[{product_title}] product is available now!', 'gpls-wcsamm-coming-soon-for-woocommerce' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return esc_html__( 'The wait is over', 'gpls-wcsamm-coming-soon-for-woocommerce' );
		}

		/**
		 * Update Dynamic custom  placeholder.
		 *
		 * @param int $product_id Product ID.
		 * @return void
		 */
		private function update_placeholders( $product_id ) {
			$product                                    = wc_get_product( $product_id );
			$is_variation                               = is_a( $product, \WC_Product_Variation::class );
			$this->object                               = $product;
			$this->placeholders['{product_title}']      = esc_html( $is_variation ? $product->get_name() : $product->get_title() );
			$this->placeholders['{product_image_link}'] = esc_url( wp_get_attachment_image_url( $product->get_image_id(), array( 600, 600 ) ) );
			$this->placeholders['{product_stock}']      = ( $product->get_stock_quantity() ? absint( $product->get_stock_quantity() ) : '' );
			$this->placeholders['{product_link}']       = esc_url( $product->get_permalink() );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param array $emails Emails Array.
		 * @param int   $product_id The Product ID.
		 * @param void
		 */
		public function trigger( $emails, $product_id ) {
			$this->setup_locale();
			$this->update_placeholders( $product_id );
			$GLOBALS[ self::$plugin_info['name'] . '-before-sending-email-product-id' ] = $product_id;
			foreach ( $emails as $email ) {
				do_action( 'wpml_switch_language_for_email', $email );
				$this->init_settings();
				$this->reset_default_content();
				$this->send( $email, $this->get_subject(), $this->format_string( $this->get_content() ), $this->get_headers(), $this->get_attachments() );
				do_action( 'wpml_restore_language_from_email' );
			}
			unset( $GLOBALS[ self::$plugin_info['name'] . '-before-sending-email-product-id' ] );
			$this->restore_locale();
		}

		/**
		 * Trigger Single Email Send.
		 *
		 * @param string $email
		 * @param int    $product_id
		 * @return void
		 */
		public function trigger_single( $email, $product_id ) {
			$this->setup_locale();
			$this->update_placeholders( $product_id );
			$GLOBALS[ self::$plugin_info['name'] . '-before-sending-email-product-id' ] = $product_id;
			do_action( 'wpml_switch_language_for_email', $email );
			$this->init_settings();
			$this->reset_default_content();
			$this->send( $email, $this->get_subject(), $this->format_string( $this->get_content() ), $this->get_headers(), $this->get_attachments() );
			do_action( 'wpml_restore_language_from_email' );
			unset( $GLOBALS[ self::$plugin_info['name'] . '-before-sending-email-product-id' ] );
			$this->restore_locale();
		}

		/**
		 * Reset Default Content.
		 *
		 * @return void
		 */
		private function reset_default_content() {
			$this->form_fields['additional_content']['default'] = $this->get_default_additional_content();
			// $this->settings['additional_content']               = '';
		}

		/**
		 * Add more inline styles.
		 *
		 * @return string
		 */
		public function get_inline_styles() {
			ob_start();
			include self::$plugin_info['path'] . 'templates/emails/default-styles.php';
			$styles = ob_get_clean();
			return $styles;
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				apply_filters( self::$plugin_info['name'] . '-coming-soon-product-email-template', $this->template_html, $this, 'html' ),
				array(
					'product'       => $this->object,
					'plugin_info'   => self::$plugin_info,
					'email_heading' => $this->get_heading(),
					'email_body'    => $this->get_email_body(),
					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $this,
					'placeholders'  => $this->placeholders,
				),
				'',
				$this->template_base
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			wc_get_template_html(
				apply_filters( self::$plugin_info['name'] . '-coming-soon-product-email-template', $this->template_html, $this, 'plain' ),
				array(
					'product'       => $this->object,
					'plugin_info'   => self::$plugin_info,
					'email_heading' => $this->get_heading(),
					'email_body'    => $this->get_email_body(),
					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $this,
					'placeholders'  => $this->placeholders,
				),
				'',
				$this->template_base
			);
		}

		/**
		 * Get Email Body.
		 *
		 * @return string
		 */
		public function get_email_body() {
			$email_body = $this->get_option( 'additional_content', $this->get_default_additional_content() );
			return apply_filters( self::$plugin_info['classes_prefix'] . '-woocommerce-email-coming-soon-product-email-body-html', $this->format_string( $email_body ), $this->object, $this );
		}

		/**
		 * Get Email Styles.
		 *
		 * @return string
		 */
		public function get_email_styles() {
			$css = wp_strip_all_tags( $this->get_option( 'email_styles', '' ) );
			return apply_filters( self::$plugin_info['classes_prefix'] . '-woocommerce-email-coming-soon-product-email-body-styles', $css, $this->object, $this );
		}

		/**
		 * Get Default Email Body.
		 *
		 * @return string
		 */
		public function get_default_additional_content() {
			ob_start();
			include self::$plugin_info['path'] . 'templates/emails/default-body.php';
			return ob_get_clean();
		}

	}

endif;
