<?php
/**
 * Handles the Coming Soon products Emails.
 *
 * @category class
 * @package  ComingSoon
 */

namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM;

use GPLSCorePro\GPLS_PLUGIN_WCSAMM\ComingSoon;
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\GPLS_WCSAMM_WC_Email_Coming_Soon_Mode;
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\ComingSoonBackgroundEmails;

/**
 * Coming Soon Emails Class
 */
class ComingSoonEmails extends ComingSoon {

	/**
	 * Email Background Process Instance.
	 *
	 * @var object
	 */
	public static $email_background_instance;

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

		// init background process.
		add_action( 'init', array( $this, 'setup_background_process' ) );
		add_action( 'wp_ajax_' . self::$plugin_info['name'] . '-track-background-emails', array( $this, 'ajax_followup_track_background_emails' ) );
		// Coming Soon End Emails Registration.
		add_filter( 'woocommerce_email_classes', array( $this, 'add_coming_soon_mail' ), 100, 1 );
		add_filter( 'woocommerce_email_actions', array( $this, 'add_coming_soon_mail_action' ), 100, 1 );

		// Subscription Backend.
		add_action( 'add_meta_boxes', array( $this, 'subscription_emails_metabox' ) );
		add_action( 'wp_ajax_' . self::$plugin_info['name'] . '-subscription-test-email', array( $this, 'ajax_send_test_email' ) );
		add_action( 'wp_ajax_' . self::$plugin_info['name'] . '-subscription-list-clear', array( $this, 'ajax_clear_subscription_list' ) );
		add_action( 'wp_ajax_' . self::$plugin_info['name'] . '-subscription-list-send', array( $this, 'ajax_send_email_subscription_list' ) );

		// Subscription Frontend.
		add_action( 'wp_ajax_nopriv_' . self::$plugin_info['name'] . '-subscription-submit-action', array( $this, 'ajax_subscription_submit' ) );
		add_action( 'wp_ajax_' . self::$plugin_info['name'] . '-subscription-submit-action', array( $this, 'ajax_subscription_submit' ) );

		// Coming Soon Subscription Email Hooks.
		add_filter( 'woocommerce_email_subject_' . self::$plugin_info['name'] . '-coming-soon-email-class', array( $this, 'custom_email_subject' ), 100, 2 );
		add_filter( 'woocommerce_email_heading_' . self::$plugin_info['name'] . '-coming-soon-email-class', array( $this, 'custom_email_heading' ), 100, 2 );
		add_filter( self::$plugin_info['classes_prefix'] . '-woocommerce-email-coming-soon-product-email-body-html', array( $this, 'custom_email_body' ), 100, 3 );

		// TODO: will be used in later versions.
		// add_filter( self::$plugin_info['name'] . '-coming-soon-product-email-template', array( $this, 'custom_email_template' ), 10, 3 );

		add_action( 'admin_notices', array( $this, 'show_background_emails_notices' ) );

		// WPML comp: Register Email to WPML emails filter.
		add_filter( 'wcml_emails_options_to_translate', array( $this, 'wpml_emails_options_hook' ), 100, 1 );
		add_filter( 'wcml_emails_section_name_to_translate', array( $this, 'filter_emails_section_name_for_wpml' ), 1000, 1 );

		// WPML comp: Populate the switched lang code.
		add_action( 'wpml_language_has_switched', array( $this, 'get_switched_lang_code' ), 1000, 3 );

		// WPML comp: Get the email language code before sending emails.
		add_filter( 'wpml_user_email_language', array( $this, 'get_recipient_lang_code_by_email' ), 1000, 2 );
	}

	/**
	 * Get recipient language code by email address.
	 *
	 * @param null|string $default_return
	 * @param string $email
	 * @return null|string
	 */
	public function get_recipient_lang_code_by_email( $default_return, $email ) {
		// Make sure its our emails call.
		if ( empty( $GLOBALS[ self::$plugin_info['name'] . '-before-sending-email-product-id' ] ) ) {
			return $default_return;
		}
		$product_id   = absint( sanitize_text_field( wp_unslash( $GLOBALS[ self::$plugin_info['name'] . '-before-sending-email-product-id' ] ) ) );
		$product_type = get_post_type( $product_id );

		// Get the product language code.
		global $sitepress;
		$lang_details = $sitepress->get_element_language_details( $product_id, 'post_' . $product_type );
		if ( $lang_details && ! empty( $lang_details->language_code ) ) {
			return $lang_details->language_code;
		}
		return $default_return;
	}

	/**
	 * Switched language action.
	 *
	 * @param string $code
	 * @param string $cookie_lang
	 * @param string $original_language_code
	 * @return string
	 */
	public function get_switched_lang_code( $code, $cookie_lang, $original_language_code ) {
		$GLOBALS[ self::$plugin_info['name'] . '-wpml-switched-lang-code' ] = $code;
		return $code;
	}

	/**
	 * WPML Emails Register Hook
	 *
	 * @param array $email_options Email Options.
	 * @return array
	 */
	public function wpml_emails_options_hook( $email_options ) {
		$email_options[] = 'woocommerce_' . self::$plugin_info['name'] . '-coming-soon-email-class_settings';
		return $email_options;
	}

	/**
	 * Adjust the email section name for WPML translation.
	 *
	 * @param string $section_name
	 * @return string
	 */
	public function filter_emails_section_name_for_wpml( $section_name ) {
		if ( 'wc_email_gpls-wcsamm-coming-soon-for-woocommerce-coming-soon-email-class' === $section_name ) {
			return 'gpls_wcsamm_wc_email_coming_soon_mode';
		}
		return $section_name;
	}

	/**
	 * Get - Update - remove background emails started notices.
	 *
	 * @param int    $product_id
	 * @param string $action
	 * @return array|void
	 */
	public static function background_emails_started_notices( $action = 'get', $new_products_ids = array() ) {
		$restored_ids = get_option( self::$background_emails_started_notices_key, array() );
		if ( 'get' === $action ) {
			return $restored_ids;
		} elseif ( 'add' === $action ) {
			update_option( self::$background_emails_started_notices_key, array_unique( array_merge( $restored_ids, $new_products_ids ) ) );
		} elseif ( 'remove' === $action ) {
			update_option( self::$background_emails_started_notices_key, array_diff( $restored_ids, $new_products_ids ) );
		} elseif ( 'reset' === $action ) {
			update_option( self::$background_emails_started_notices_key, array() );
		}
	}

	/**
	 * Get - Update - remove background emails notices.
	 *
	 * @param int    $product_id
	 * @param string $action
	 * @return array|void
	 */
	public static function background_emails_notices( $action = 'get', $new_products_ids = array() ) {
		if ( 'get' === $action ) {
			return get_option( self::$background_emails_notices_key, array() );
		} elseif ( 'add' === $action ) {
			$products_ids = get_option( self::$background_emails_notices_key, array() );
			$products_ids = array_unique( array_merge( $products_ids, $new_products_ids ) );
			update_option( self::$background_emails_notices_key, $products_ids, true );
		} elseif ( 'reset' === $action ) {
			update_option( self::$background_emails_notices_key, array(), true );
		}
	}

	/**
	 * Background Emails End Notices.
	 * Show the notice for one time and remove it to avoid spamming.
	 *
	 * @return void
	 */
	public function show_background_emails_notices() {
		$products_list                  = '';
		$background_emails_products_ids = self::background_emails_notices( 'get' );
		if ( ! empty( $background_emails_products_ids ) ) :
			foreach ( $background_emails_products_ids as $product_id ) :
				$_product = wc_get_product( $product_id );
				if ( $_product ) :
					$is_variation      = is_a( $_product, \WC_Product_Variation::class );
					$product_edit_link = get_edit_post_link( $is_variation ? $_product->get_parent_id() : $product_id );
					$products_list    .= '<a target="_blank" href="' . esc_url( $product_edit_link ) . '">' . esc_html( $is_variation ? '#' : '' ) . esc_html( $product_id ) . '</a>&nbsp;';
				endif;
			endforeach;
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( '[Coming Soon product available] emails have been sent successfully for ', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?><?php echo wp_kses_post( $products_list ); ?></p>
			</div>
			<?php
			self::background_emails_notices( 'reset' );
			ComingSoonBackend::reset_background_emails_products_track();
		endif;
						// ======= Background Emails Started Notice ========= //
		if ( ! empty( $background_emails_products_ids ) ) {
			// Delete any ended background IDs from the started ones.
			self::background_emails_started_notices( 'remove', $background_emails_products_ids );
		}
		$products_list                          = '';
		$background_emails_started_products_ids = self::background_emails_started_notices( 'get' );
		if ( empty( $background_emails_started_products_ids ) ) {
			return;
		}
		foreach ( $background_emails_started_products_ids as $product_id ) :
			$_product = wc_get_product( $product_id );
			if ( $_product ) :
				$is_variation      = is_a( $_product, \WC_Product_Variation::class );
				$product_edit_link = get_edit_post_link( $is_variation ? $_product->get_parent_id() : $product_id );
				$products_list    .= '<a target="_blank" href="' . esc_url( $product_edit_link ) . '">' . esc_html( $is_variation ? '#' : '' ) . esc_html( $product_id ) . '</a>&nbsp;';
			endif;
		endforeach;
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( '[Coming Soon product available] emails are being sent in background for ', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?><?php echo wp_kses_post( $products_list ); ?></p>
		</div>
		<?php
		self::background_emails_started_notices( 'reset' );
	}

	/**
	 * Initialize The background process instance.
	 *
	 * @return void
	 */
	public function setup_background_process() {
		self::$email_background_instance = new ComingSoonBackgroundEmails( self::$plugin_info );
	}

	/**
	 * Register Coming Soon End Product Email Class.
	 *
	 * @param array $emails
	 * @return array
	 */
	public function add_coming_soon_mail( $emails ) {
		$emails['GPLS_WCSAMM_WC_Email_Coming_Soon_Mode'] = new GPLS_WCSAMM_WC_Email_Coming_Soon_Mode( self::$plugin_info );
		return $emails;
	}

	/**
	 * Register Coming Soon End Product Email Class Action.
	 *
	 * @param array $emails_actions
	 * @return array
	 */
	public function add_coming_soon_mail_action( $emails_actions ) {
		$emails_actions[] = self::$plugin_info['classes_prefix'] . '-product-coming-soon-ended-email';
		return $emails_actions;
	}

	/**
	 * Select Custom Email Template.
	 *
	 * @param string $template_html_relative_path
	 * @param object $email_obj
	 * @return string
	 */
	public function custom_email_template( $template_html_relative_path, $email_obj, $type ) {
		$email_template = Settings::get_settings( 'email', 'template' );
		if ( 'default' !== $email_template && in_array( $email_template, array_keys( Settings::$email_templates ) ) ) {
			return 'emails/' . Settings::$email_templates[ $email_template ]['name'] . '-' . $type . '.php';
		}
		return $template_html_relative_path;
	}

	/**
	 * Coming Soon Subscription Emails metabox registration.
	 *
	 * @return void
	 */
	public function subscription_emails_metabox() {
		add_meta_box(
			self::$plugin_info['name'] . '-subscription-emails-metabox',
			esc_html__( 'Coming Soon Emails', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
			array( $this, 'subscription_emails_metabox_html' ),
			'product',
			'normal',
			'core'
		);
	}

	/**
	 * Coming Soon Subscription Emails Metabox HTML.
	 *
	 * @return void
	 */
	public function subscription_emails_metabox_html( $post ) {
		if (  self::$core->is_active( true, true ) ) :
			$last_sent              = array();
			$subscription_emails    = $this->get_subscription_emails( $post->ID );
			$last_sent[ $post->ID ] = get_post_meta( $post->ID, self::$last_sent_key, true );
			$product_obj            = wc_get_product( $post->ID );
			$is_variable_product    = is_a( $product_obj, \WC_Product_Variable::class );
			if ( $is_variable_product ) {
				$variations_ids = wc_get_products(
					array(
						'status'  => array( 'private', 'publish' ),
						'type'    => 'variation',
						'parent'  => $post->ID,
						'orderby' => array(
							'menu_order' => 'ASC',
							'ID'         => 'DESC',
						),
						'return'  => 'ids',
					)
				);
			}
			if ( ! empty( $subscription_emails['emails'] ) || ! empty( $subscription_emails['variations'] ) ) :
				$this->subscription_list_html( $subscription_emails, $post->ID );
			else :
				?>
				<h4><?php esc_html_e( 'No emails yet!', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h4>
				<?php
			endif;

			if ( $is_variable_product ) :
				?>
				<!-- Variation Emails Selection -->
				<div class="email-selection-wrapper">
					<label><?php esc_html_e( 'Select Product/Variation', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?>
					<select class="email-selection" id="<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-variation-email-selection' ); ?>">
						<option value="0"><?php echo esc_html( '&mdash; Select &mdash;' ); ?></option>
						<option value="<?php echo absint( esc_attr( $post->ID ) ); ?>"><?php esc_html_e( 'Main product', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></option>
					<?php
					foreach ( $variations_ids as $variation_id ) :
						$last_sent[ $variation_id ] = get_post_meta( $variation_id, self::$last_sent_key, true );
						?>
						<option value="<?php echo absint( esc_attr( $variation_id ) ); ?>"><?php echo esc_attr( '#' . $variation_id ); ?></option>
						<?php endforeach; ?>
					</select>
					</label>
				</div>
				<?php
			else :
				?>
				<input type="hidden" class="hidden email-selection" value="<?php echo absint( esc_attr( $post->ID ) ); ?>"></span>
				<?php
			endif;

			do_action( self::$plugin_info['name'] . '-coming-soon-emails-metabox-more-options', $post->ID );
			?>
			<div class="test-email d-flex flex-start align-items-center">
				<?php global $current_user; ?>
				<input type="email" class="test-email-input" value="<?php echo esc_attr( ( ! empty( $current_user ) && ( ! empty( $current_user->user_email ) ) ) ? $current_user->user_email : '' ); ?>" >
				<button class="button send-test-email-btn" <?php echo esc_attr( $is_variable_product ? 'disabled' : '' ); ?> ><?php esc_html_e( 'Send Test Email', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></button>
				<span class="spinner"><img class="loader" src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>" /></span>
				<span class="sent hidden"><img src="<?php echo esc_url( self::$plugin_info['url'] . 'assets/dist/images/ok.png' ); ?>" /></span>
			</div>
			<div class="actions">
				<button data-id="<?php echo esc_attr( $post->ID ); ?>" <?php echo esc_attr( $is_variable_product ? 'disabled' : '' ); ?> class="button <?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-subscriptions-action-send' ); ?>"><?php esc_html_e( 'Send Emails', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></button>
				<a href="#" <?php echo esc_attr( $is_variable_product ? 'disabled' : '' ); ?>  class=" button <?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-subscriptions-action-export' ); ?>" download="coming-soon-subscriptions-<?php echo esc_attr( $post->ID ); ?>.csv" ><?php esc_html_e( 'Export List', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></a>
				<button data-id="<?php echo esc_attr( $post->ID ); ?>" <?php echo esc_attr( $is_variable_product ? 'disabled' : '' ); ?> class="button <?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-subscriptions-action-clear' ); ?>"><?php esc_html_e( 'Clear List', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></button>
			</div>
			<div class="notices">
				<h4 class="hidden clearing notice notice-warning"><?php esc_html_e( 'Deleting selected emails... ', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?> <img class="loader" src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>" /></h4>
				<h4 class="hidden sending notice notice-warning"><?php esc_html_e( 'emails are being sent in background... ', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?> <img class="loader" src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>" /></h4>
				<h4 class="hidden sent notice notice-success"><?php esc_html_e( 'All Emails have been sent successfully!', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h4>
			</div>
			<?php
			if ( $last_sent ) :
				foreach ( $last_sent as $product_id => $sent_date ) :
					if ( ! empty( $sent_date ) ) :
						?>
				<div class="last-sent <?php echo esc_attr( $is_variable_product ? 'hidden' : '' ); ?>" data-product="<?php echo absint( esc_attr( $product_id ) ); ?>">
					<span><?php esc_html_e( 'Last Sent: ', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></span>
					<strong><?php echo esc_html( date_i18n( 'F j, Y g:i A', $sent_date ) ); ?></strong>
				</div>
						<?php
					endif;
				endforeach;
			endif;
		endif;
	}

	/**
	 * Emails List HTMl.
	 *
	 * @param array $sub_emails
	 * @param int   $product_id
	 * @return void
	 */
	private function subscription_list_html( $sub_emails, $product_id ) {
		if ( empty( $sub_emails['emails'] ) && empty( $sub_emails['variations'] ) ) {
			?>
			<h4><?php esc_html_e( 'No emails yet', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h4>
			<?php
			return;
		}
		?>
		<ul class="emails-list">
			<?php foreach ( $sub_emails['emails'] as $email ) : ?>
			<li class="email-item" data-product_id="<?php echo absint( esc_attr( $product_id ) ); ?>">
				<span class="email-name"><?php echo esc_html( $email ); ?></span>
			</li>
			<?php endforeach; ?>
			<?php
			if ( ! empty( $sub_emails['variations'] ) ) :
				foreach ( $sub_emails['variations'] as $variation_id => $emails ) :
					foreach ( $emails as $email ) :
						?>
					<li class="email-item" data-product_id="<?php echo absint( esc_attr( $variation_id ) ); ?>">
						<div class="email-item-header">
							<span class="alignleft email-name"><?php echo esc_html( $email ); ?></span>
							<span class="alignright"><?php echo esc_html( '#' . $variation_id ); ?></span>
							<span class="badge alignright"><?php echo esc_html( 'Variation' ); ?></span>
						</div>
					</li>
						<?php
					endforeach;
				endforeach;
			endif;
			?>
		</ul>
		<?php
	}

	/**
	 * AJAX send Test Email.
	 *
	 * @return void
	 */
	public function ajax_send_test_email() {
		if ( ! empty( $_POST['email'] ) && ! empty( $_POST['nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['nonce'] ), self::$plugin_info['name'] . '-nonce' ) ) {
			$email_product_id = ! empty( $_POST['emailProductID'] ) ? absint( wp_unslash( $_POST['emailProductID'] ) ) : 0;
			$email            = sanitize_email( wp_unslash( $_POST['email'] ) );
			if ( 0 === $email_product_id ) {
				wp_send_json_success(
					array(
						'status'  => 'error',
						'message' => esc_html__( 'Please, Select a product/variation.', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
					)
				);
			}

			$this->send_email_manual( array( $email ), $email_product_id );
			wp_send_json_success(
				array(
					'status'  => 'success',
					'message' => esc_html__( 'The email has been sent successfully!', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
				)
			);
		} else {
			wp_send_json_error(
				array(
					'status'  => 'error',
					'message' => esc_html__( 'Invalid nonce', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
				),
				400
			);
		}
	}

	/**
	 * AJAX Coming Soon Subscription Send Email.
	 *
	 * @return void
	 */
	public function ajax_send_email_subscription_list() {
		if ( ! empty( $_POST['productID'] ) && ! empty( $_POST['nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['nonce'] ), self::$plugin_info['name'] . '-nonce' ) ) {
			$product_id       = absint( sanitize_text_field( wp_unslash( $_POST['productID'] ) ) );
			$email_product_id = ! empty( $_POST['emailProductID'] ) ? absint( wp_unslash( $_POST['emailProductID'] ) ) : $product_id;
			$product_emails   = $this->get_subscription_emails( $product_id );
			$emails           = array();

			if ( 0 === $email_product_id ) {
				wp_send_json_success(
					array(
						'status'  => 'error',
						'message' => esc_html__( 'Please, Select which email to be sent.', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
					)
				);
			}

			if ( $product_id !== $email_product_id && ! empty( $product_emails['variations'][ $email_product_id ] ) ) {
				$emails = $product_emails['variations'][ $email_product_id ];
			} else {
				$emails = $product_emails['emails'];
			}
			if ( ! empty( $emails ) ) {
				ComingSoonBackend::add_background_emails_products_track( $email_product_id );
				$batch_key = self::send_email_in_background( $emails, $email_product_id );
				wp_send_json_success(
					array(
						'batchKey' => $batch_key,
						'status'   => 'success',
						'message'  => esc_html__( 'Emails are being sent in a background process', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
					)
				);
			} else {
				wp_send_json_success(
					array(
						'status'  => 'error',
						'message' => esc_html__( 'Subscription list is empty!', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'status'  => 'error',
					'message' => esc_html__( 'Invalid nonce', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
				),
				400
			);
		}
	}

	/**
	 * AJAX Coming Soon Subscription Clear.
	 *
	 * @return void
	 */
	public function ajax_clear_subscription_list() {
		if ( ! empty( $_POST['productID'] ) && ! empty( $_POST['nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['nonce'] ), self::$plugin_info['name'] . '-nonce' ) ) {
			$product_id       = absint( sanitize_text_field( wp_unslash( $_POST['productID'] ) ) );
			$email_product_id = ! empty( $_POST['emailProductID'] ) ? absint( wp_unslash( $_POST['emailProductID'] ) ) : $product_id;
			$this->delete_subscription_list( $product_id, $email_product_id );

			$emails_list = $this->get_subscription_emails( $product_id );
			ob_start();
			$this->subscription_list_html( $emails_list, $product_id );
			$emails_list_html = ob_get_clean();
			wp_send_json_success(
				array(
					'status'      => 'success',
					'emails_list' => $emails_list_html,
					'message'     => esc_html__( 'List has been cleared successfully!', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
				)
			);
		}
		wp_send_json_error(
			array(
				'status'  => 'error',
				'message' => esc_html__( 'Invalid nonce', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
			),
			400
		);
	}

	/**
	 * AJAX Coming Soon Subscription Submit.
	 *
	 * @return void
	 */
	public function ajax_subscription_submit() {
		if ( ! empty( $_POST['productID'] ) && ! empty( $_POST['email'] ) && ! empty( $_POST['nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['nonce'] ), self::$plugin_info['name'] . '-nonce' ) ) {
			$email          = sanitize_email( wp_unslash( $_POST['email'] ) );
			$product_id     = absint( sanitize_text_field( wp_unslash( $_POST['productID'] ) ) );
			$target_product = wc_get_product( $product_id );
			$variation_id   = null;

			if ( ! empty( $email ) && $target_product ) {
				if ( is_a( $target_product, \WC_Product_Variation::class ) ) {
					$variation_id = $product_id;
					$product_id   = $target_product->get_parent_id();
				}
				$this->add_subscription_emails( $product_id, $email, $variation_id );
				do_action( self::$plugin_info['name'] . '-subscription-email-submit-save', $product_id, $email, $variation_id );
			}

			wp_send_json_success(
				array(
					'status'  => 'success',
					'message' => esc_html__( 'Email has been saved successfully!', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
				)
			);
		}
		wp_send_json_error(
			array(
				'status'  => 'error',
				'message' => esc_html__( 'Invalid nonce', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
			),
			400
		);
	}

	/**
	 * Send emails sync.
	 *
	 * @param array $emails
	 * @param int   $product_id
	 * @return void
	 */
	private function send_email_manual( $emails, $product_id ) {
		// 1) Init the mailer.
		WC()->mailer();
		// 2) Start sending the emails.
		do_action( self::$plugin_info['classes_prefix'] . '-product-coming-soon-ended-email_notification', $emails, $product_id );
		// 3) Save last sent date.
		$last_sent = current_time( 'timestamp' );
		update_post_meta( $product_id, self::$last_sent_key, $last_sent );

		wp_send_json_success(
			array(
				'status'    => 'success',
				'sent_date' => date_i18n( 'F j, Y g:i A', $last_sent ),
			)
		);
	}

	/**
	 * Send Emails Async.
	 *
	 * @param array $emails
	 * @param int   $product_id
	 * @return string
	 */
	public static function send_email_in_background( $emails, $product_id, $auto_dispatch = true ) {
		foreach ( $emails as $email ) {
			$item = array(
				'email_address' => $email,
				'product_id'    => $product_id,
			);
			self::$email_background_instance->push_to_queue( $item );
		}
		$batch_key = self::$email_background_instance->save();
		if ( $auto_dispatch ) {
			self::$email_background_instance->dispatch();
		}
		return $batch_key;
	}

	/**
	 * Fetch the translation of the email fields [ subject - heading - body ] from the translation product version.
	 *
	 * @param int $product_id
	 * @param string $lang_code
	 * @param string $field_key
	 * @return string
	 */
	private function wpml_product_email_parts_translation_version( $product_id, $lang_code, $field_key ) {
		global $sitepress;
		$trid = $sitepress->get_element_trid( $product_id, 'post_' . get_post_type( $product_id ) );
		if ( $trid ) {
			$translation_row = $this->wpml_get_translation_version_from_original_product( $trid, $lang_code );
			if ( is_array( $translation_row ) && ! empty( $translation_row['element_id'] ) ) {
				$translation_product_id = absint( $translation_row['element_id'] );
				$translation_product    = wc_get_product( $translation_product_id );
				if ( $translation_product ) {
					$translated_content = self::get_settings( $translation_product->get_id(), $field_key );
					if ( ! empty( $translated_content ) ) {
						return $translated_content;
					}
				}
			}
		}

		return '';
	}

	/**
	 * Get The translated version from the main product ID.
	 *
	 * @param int    $trid
	 * @param string $language_code
	 * @return array|null
	 */
	private function wpml_get_translation_version_from_original_product( $trid, $language_code ) {
		global $wpdb;
		$translation = $wpdb->get_row(
			$wpdb->prepare(
				"
			SELECT *
			FROM {$wpdb->prefix}icl_translations tr
			WHERE tr.trid=%d AND tr.language_code= %s",
				$trid,
				$language_code
			),
			\ARRAY_A
		);
		return $translation;
	}

	/**
	 * Custom Email Subject.
	 *
	 * @param string $email_subject
	 * @param object $product
	 * @return string
	 */
	public function custom_email_subject( $email_subject, $product ) {
		$custom_subject = self::get_settings( $product->get_id(), 'custom_email_subject' );
		if ( ! empty( trim( $custom_subject ) ) ) {
			// WPML Comp: check translation.
			global $sitepress, $icl_language_switched;
			if ( $sitepress && $icl_language_switched ) {
				// check if the language is switched.
				if ( ! self::wpml_is_translation( $product->get_id(), 'post_' . get_post_type( $product->get_id() ) ) && $icl_language_switched && ! empty( $GLOBALS[ self::$plugin_info['name'] . '-wpml-switched-lang-code' ] ) ) {
					$switched_lang_code  = $GLOBALS[ self::$plugin_info['name'] . '-wpml-switched-lang-code' ];
					$translation_version = $this->wpml_product_email_parts_translation_version( $product->get_id(), $switched_lang_code, 'custom_email_subject' );
					if ( ! empty( $translation_version ) ) {
						$custom_subject = $translation_version;
					}
				}
			}
			// Return email custom subject.
			$email_subject = sprintf( esc_html__( '%s', 'gpls-wcsamm-coming-soon-for-woocommerce' ), $custom_subject );
		}
		return $email_subject;
	}

	/**
	 * Custom Email Heading.
	 *
	 * @param string $email_heading
	 * @param object $product
	 * @return string
	 */
	public function custom_email_heading( $email_heading, $product ) {
		$custom_heading = self::get_settings( $product->get_id(), 'custom_email_heading' );
		if ( ! empty( trim( $custom_heading ) ) ) {
			// WPML Comp: check translation.
			global $sitepress, $icl_language_switched;
			if ( $sitepress && $icl_language_switched ) {
				// check if the language is switched.
				if ( ! self::wpml_is_translation( $product->get_id(), 'post_' . get_post_type( $product->get_id() ) ) && $icl_language_switched && ! empty( $GLOBALS[ self::$plugin_info['name'] . '-wpml-switched-lang-code' ] ) ) {
					$switched_lang_code  = $GLOBALS[ self::$plugin_info['name'] . '-wpml-switched-lang-code' ];
					$translation_version = $this->wpml_product_email_parts_translation_version( $product->get_id(), $switched_lang_code, 'custom_email_heading' );
					if ( ! empty( $translation_version ) ) {
						$custom_heading = $translation_version;
					}
				}
			}
			$email_heading = sprintf( esc_html__( '%s', 'gpls-wcsamm-coming-soon-for-woocommerce' ), $custom_heading );
		}
		return $email_heading;
	}

	/**
	 * Custom Email Body.
	 *
	 * @param string $email_additional_content
	 * @param object $product
	 * @return string
	 */
	public function custom_email_body( $email_additional_content, $product, $email_obj ) {
		$custom_body = self::get_settings( $product->get_id(), 'custom_email_body' );
		if ( ! empty( trim( $custom_body ) ) ) {
			// WPML Comp: check translation.
			global $sitepress, $icl_language_switched;
			if ( $sitepress && $icl_language_switched ) {
				// check if the language is switched.
				if ( ! self::wpml_is_translation( $product->get_id(), 'post_' . get_post_type( $product->get_id() ) ) && $icl_language_switched && ! empty( $GLOBALS[ self::$plugin_info['name'] . '-wpml-switched-lang-code' ] ) ) {
					$switched_lang_code  = $GLOBALS[ self::$plugin_info['name'] . '-wpml-switched-lang-code' ];
					$translation_version = $this->wpml_product_email_parts_translation_version( $product->get_id(), $switched_lang_code, 'custom_email_body' );
					if ( ! empty( $translation_version ) ) {
						$custom_body = $translation_version;
					}
				}
			}
			$email_additional_content = $email_obj->format_string( $custom_body );
		}
		return $email_additional_content;
	}

	/**
	 * Delete Subscription List.
	 *
	 * @param int $product_id
	 * @return void
	 */
	private function delete_subscription_list( $product_id, $variation_id ) {
		if ( $product_id !== $variation_id ) {
			$emails_list = $this->get_subscription_emails( $product_id, $variation_id );
			if ( ! empty( $emails_list ) && ! empty( $emails_list['variations'] ) && ! empty( $emails_list['variations'][ $variation_id ] ) ) {
				unset( $emails_list['variations'][ $variation_id ] );
				update_post_meta( $product_id, self::$subscription_emails_key, $emails_list );
			}
		} else {
			delete_post_meta( $product_id, self::$subscription_emails_key );
		}
	}

	/**
	 * Add Subscription Email to the product emails list.
	 *
	 * @param int    $product_id
	 * @param string $email
	 * @return void
	 */
	private function add_subscription_emails( $product_id, $email, $variation_id = null ) {
		$emails_list = $this->get_subscription_emails( $product_id );
		if ( empty( $emails_list['emails'] ) ) {
			$emails_list['emails'] = array();
		}

		if ( ! is_null( $variation_id ) ) {
			if ( empty( $emails_list['variations'] ) ) {
				$emails_list['variations'] = array();
			}
			if ( empty( $emails_list['variations'][ $variation_id ] ) ) {
				$emails_list['variations'][ $variation_id ] = array();
			}
			if ( ! in_array( $email, $emails_list['variations'][ $variation_id ] ) ) {
				$emails_list['variations'][ $variation_id ][] = $email;
			}
		} else {
			if ( ! in_array( $email, $emails_list['emails'] ) ) {
				$emails_list['emails'][] = $email;
			}
		}
		update_post_meta( $product_id, self::$subscription_emails_key, $emails_list );
	}

	/**
	 * Get Coming Soon Product Subscription Emails.
	 *
	 * @param int $product_id
	 * @return array
	 */
	private function get_subscription_emails( $product_id ) {
		$emails = get_post_meta( $product_id, self::$subscription_emails_key, true );
		if ( empty( $emails ) ) {
			$emails = array(
				'emails'     => array(),
				'variations' => array(),
			);
		}
		return $emails;
	}

	/**
	 * Get Directory Subscription Emails.
	 *
	 * @param int $product_id
	 * @param int $variation_id
	 * @return array
	 */
	public static function get_direct_subscription_emails( $product_id, $variation_id = null ) {
		$emails_list = array();
		$emails      = get_post_meta( $product_id, self::$subscription_emails_key, true );

		if ( ! empty( $emails ) && ! empty( $emails['emails'] ) && is_null( $variation_id ) ) {
			$emails_list = $emails['emails'];
		}
		if ( ! empty( $emails ) && ! empty( $emails['variations'] ) && ! empty( $emails['variations'][ $variation_id ] ) ) {
			$emails_list = $emails['variations'][ $variation_id ];
		}
		return $emails_list;
	}


	/**
	 * Check if the background email process is finished or not.
	 *
	 * @return bool
	 */
	protected function is_background_email_process_finished( $batch_key ) {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}
		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT
					COUNT(*)
				FROM
					{$table}
				WHERE
					{$column} = %s",
				$batch_key
			)
		);

		return ! ( $count > 0 );
	}

	/**
	 * Track the background emails progress.
	 *
	 * @return void
	 */
	public function ajax_followup_track_background_emails() {
		if ( ! empty( $_POST['batchKey'] ) && ! empty( $_POST['productID'] ) && check_ajax_referer( self::$plugin_info['name'] . '-track-background-emails-nonce', 'nonce' ) ) {
			$batch_key   = sanitize_text_field( wp_unslash( $_POST['batchKey'] ) );
			$product_id  = sanitize_text_field( wp_unslash( $_POST['productID'] ) );
			$is_finished = $this->is_background_email_process_finished( $batch_key );
			$response    = array(
				'status' => $is_finished,
			);
			if ( $is_finished ) {
				$last_sent             = get_post_meta( $product_id, self::$last_sent_key, true );
				$response['last_sent'] = esc_html( date_i18n( 'F j, Y g:i A', $last_sent ) );
				self::background_emails_notices( 'remove', $product_id );
			}
			wp_send_json_success( $response );
		}
		wp_send_json_error(
			array(
				'status'  => 'error',
				'message' => esc_html__( 'Invalid nonce', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
			),
			400
		);
	}

}
