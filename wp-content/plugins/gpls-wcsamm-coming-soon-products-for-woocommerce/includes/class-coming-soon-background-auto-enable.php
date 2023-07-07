<?php
/**
 * Handles the Coming Soon products Emails.
 *
 * @category class
 * @package  ComingSoon
 */

namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM;

use GPLSCorePro\GPLS_PLUGIN_WCSAMM\ComingSoon;
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\WP_Background_Process;

/**
 * Coming Soon Sending Emails in background Class.
 */
class ComingSoonBackgroundAutoEnable extends WP_Background_Process {

	/**
	 * Plugin Info Array.
	 *
	 * @var array
	 */
	protected static $plugin_info;

	/**
	 * Action Name.
	 *
	 * @var string
	 */
	protected $action;

	/**
	 * Constructor.
	 *
	 * @param array $plugin_info Plugin Info Array.
	 */
	public function __construct( $plugin_info ) {
		self::$plugin_info = $plugin_info;
		$this->action      = self::$plugin_info['classes_prefix'] . '-background-auto-enable-action';
		parent::__construct();
	}

	/**
	 * Send the email task.
	 *
	 * @param string $email_address Email Address to send the email to.
	 *
	 * @return false
	 */
	protected function task( $data ) {
		$product_id = $data['product_id'];

		// 1) Check the product if is coming soon - auto enable - and arrival time is passed.
		if ( ComingSoon::is_product_coming_soon_enabled( $product_id ) && ComingSoon::is_auto_enable( $product_id ) && ComingSoon::is_product_arrival_time_passed( $product_id ) ) {
			// 2) remove it from hub, update status to no.
			ComingSoon::update_coming_soon_list( $product_id, 'remove' );
			ComingSoon::update_setting( $product_id, 'status', 'no' );
			ComingSoon::update_setting( $product_id, 'auto_enable', 'no' );

			// 3) Create background process to send emails.
			if ( ComingSoon::is_auto_email( $product_id ) ) {
				$_product = wc_get_product( $product_id );
				if ( ! $_product ) {
					return;
				}
				if ( is_a( $_product, \WC_Product_Variation::class ) ) {
					$product_parent_id = $_product->get_parent_id();
					$emails            = ComingSoonEmails::get_direct_subscription_emails( $product_parent_id, $product_id );
				} else {
					$emails = ComingSoonEmails::get_direct_subscription_emails( $product_id );
				}

				// 4) Create background process to send emails.
				if ( ! empty( $emails ) ) {
					ComingSoonBackend::add_background_emails_products_track( $product_id );
					ComingSoonEmails::send_email_in_background( $emails, $product_id, true );
				}
			}
		}
		return false;
	}

	/**
	 * Complete Sending Emails.
	 *
	 * @return void
	 */
	protected function complete() {
		parent::complete();
	}

	/**
	 * Get The Background Identifier.
	 *
	 * @return string
	 */
	public function get_identifier() {
		return $this->identifier;
	}
}
