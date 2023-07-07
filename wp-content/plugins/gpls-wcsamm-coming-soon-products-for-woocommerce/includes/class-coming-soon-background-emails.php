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
class ComingSoonBackgroundEmails extends WP_Background_Process {

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
	 * Track the product ID.
	 *
	 * @var int
	 */
	protected $product_id;

	/**
	 * Constructor.
	 *
	 * @param array $plugin_info Plugin Info Array.
	 */
	public function __construct( $plugin_info ) {
		self::$plugin_info = $plugin_info;
		$this->action      = self::$plugin_info['classes_prefix'] . '-background-emails-action';
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
		$this->product_id = $data['product_id'];
		// 1) Init the mailer.
		WC()->mailer();
		// 2) Start sending the emails.
		do_action( self::$plugin_info['classes_prefix'] . '-product-coming-soon-ended-email_notification_single', $data['email_address'], $data['product_id'] );

		return false;
	}

	/**
	 * Complete Sending Emails.
	 *
	 * @return void
	 */
	protected function complete() {
		parent::complete();
		$last_sent    = current_time( 'timestamp' );
		$products_ids = ComingSoonBackend::get_background_emails_products_tracking();
		foreach ( $products_ids as $product_id ) {
			update_post_meta( $product_id, ComingSoon::$last_sent_key, $last_sent );
		}
		ComingSoonEmails::background_emails_notices( 'add', $products_ids );
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
