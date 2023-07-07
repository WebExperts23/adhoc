<?php
/**
 * Handles the Coming Soon State of WooCommerce Products.
 *
 * @category class
 * @package  ComingSoon
 */

namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Settings;

/**
 * Coming Soon Class
 */
class ComingSoon {

	/**
	 * Core Object.
	 *
	 * @var object
	 */
	public static $core;

	/**
	 * Plugin Info Object.
	 *
	 * @var object
	 */
	public static $plugin_info;

	/**
	 * Coming Soon Default Settings.
	 *
	 * @var array
	 */
	public static $default_settings;

	/**
	 * Subsscription Emails Meta Key.
	 *
	 * @var string
	 */
	public static $subscription_emails_key;

	/**
	 * Last Coming Soon Available Email sent Date meta key.
	 *
	 * @var string
	 */
	public static $last_sent_key;

	/**
	 * List of coming soon products List Settings Key.
	 *
	 * @var string
	 */
	public static $coming_soon_hub_key;

	/**
	 * Coming Soon Query Args.
	 *
	 * @var array
	 */
	public static $query_args;

	/**
	 * Query Args for coming Soon products only has arrival time.
	 *
	 * @todo to be used later.
	 * @var string
	 */
	public static $coming_soon_with_arrival_time_query_args;

	/**
	 * Background Emails Notices Key.
	 *
	 * @var string
	 */
	public static $background_emails_notices_key;

	/**
	 * Background Emails Started Notices Key.
	 *
	 * @var string
	 */
	public static $background_emails_started_notices_key;

	/**
	 * Automatic Email Cron Hook Name.
	 *
	 * @var string
	 */
	public static $auto_enable_and_emails_cron_hook;

	/**
	 * Tracker Option Key for background emails porducts IDs.
	 *
	 * @var array
	 */
	protected static $background_emails_products_tracker;

	/**
	 * Custom Fields to Labels Mapping for WPML Translation Editor.
	 *
	 * @var array
	 */
	protected static $custom_fields_labels_for_wpml = array();

	 /**
	  * Constructor
	  *
	  * @param object $core Core Object.
	  * @param object $plugin_info Plugin Info.
	  */
	public function __construct( $core, $plugin_info, $init_settings = false ) {
		self::$core                                  = $core;
		self::$plugin_info                           = $plugin_info;
		self::$coming_soon_hub_key                   = self::$plugin_info['name'] . '-coming-soon-products-list-settings';
		self::$background_emails_notices_key         = self::$plugin_info['name'] . '-background-emails-notices';
		self::$background_emails_started_notices_key = self::$plugin_info['name'] . '-background-emails-started-notices';
		self::$auto_enable_and_emails_cron_hook      = self::$plugin_info['name'] . '-auto-enable-auto-email-cron';
		self::$background_emails_products_tracker    = self::$plugin_info['name'] . '-background-emails-products-tracker';

		if ( $init_settings ) {
			self::init_settings();
		}
	}


	/**
	 * Initialize the Settings.
	 *
	 * @return void
	 */
	public static function init_settings() {
		self::$subscription_emails_key                  = self::$plugin_info['name'] . '-subscription-emails-key';
		self::$last_sent_key                            = self::$plugin_info['name'] . '-last-coming-soon-sent-date-key';
		self::$default_settings                         = array(
			'status'                    => 'no',
			'coming_soon_text'          => Settings::get_settings( 'general', 'coming_soon_text' ),
			'hide_price'                => 'no',
			'disable_backorders'        => 'no',
			'arrival_time'              => '',
			'show_countdown'            => 'no',
			'auto_enable'               => 'no',
			'auto_email'                => 'no',
			'custom_badge_status'       => 'no',
			'custom_badge'              => '',
			'show_subscription_form'    => 'no',
			'custom_subscription_form'  => '',
			'custom_subscription_title' => '',
			'custom_email_subject'      => '',
			'custom_email_heading'      => '',
			'custom_email_body'         => '',
		);
		self::$default_settings                         = apply_filters( self::$plugin_info['name'] . '-coming-soon-product-default-settings', self::$default_settings );
		self::$query_args                               = array(
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_type'      => array( 'product', 'product_variation' ),
			'post_status'    => 'publish',
		);
		self::$coming_soon_with_arrival_time_query_args = array(
			'meta_type'  => 'DATETIME',
			'orderby'    => array(
				'meta_value' => 'ASC',
			),
			'meta_query' => array(
				array(
					'key'     => self::$plugin_info['name'] . '-arrival_time',
					'compare' => 'EXISTS',
				),
			),
		);
		self::$custom_fields_labels_for_wpml            = array(
			self::$plugin_info['name'] . '-coming_soon_text'          => esc_html__( 'Coming Soon Text', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
			self::$plugin_info['name'] . '-custom_subscription_title' => esc_html__( 'Subscription Form Title', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
			self::$plugin_info['name'] . '-custom_email_subject'      => esc_html__( 'Custom Email Subject', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
			self::$plugin_info['name'] . '-custom_email_heading'      => esc_html__( 'Custom Email Heading', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
			self::$plugin_info['name'] . '-custom_email_body'         => esc_html__( 'Custom Email Body', 'gpls-wcsamm-coming-soon-for-woocommerce' ),
		);
	}

	/**
	 * Get Product Coming Soon Settings.
	 *
	 * @param int $product_id Product ID.
	 * @return array|false|string
	 */
	public static function get_settings( $product_id, $custom_key = null ) {
		$settings = self::$default_settings;
		if ( ! is_null( $custom_key ) && ! in_array( $custom_key, array_keys( $settings ) ) ) {
			return false;
		}
		if ( is_null( $custom_key ) ) {
			foreach ( self::$default_settings as $setting_name => $setting_value ) {
				$new_setting_value = maybe_unserialize( get_post_meta( $product_id, self::$plugin_info['name'] . '-' . $setting_name, true ) );
				if ( $new_setting_value ) {
					$settings[ $setting_name ] = $new_setting_value;
				}
			}
			return $settings;
		} else {
			return maybe_unserialize( get_post_meta( $product_id, self::$plugin_info['name'] . '-' . $custom_key, true ) );
		}
	}

	/**
	 * Reset product Arrival Time.
	 *
	 * @param int $product_id
	 * @return void
	 */
	public static function reset_arrival_time( $product_id ) {
		self::update_setting( $product_id, 'arrival_time', '' );
	}

	/**
	 * Update Coming Soon Setting key.
	 *
	 * @param int        $product_id Product ID.
	 * @param string     $key Key Name.
	 * @param string|int $value Key Value.
	 * @return void
	 */
	public static function update_setting( $product_id, $key, $value ) {
		update_post_meta( $product_id, self::$plugin_info['name'] . '-' . $key, $value );
	}

	/**
	 * Check only if the coming soon status is enabled or not.
	 *
	 * @param int $product_id
	 * @return boolean
	 */
	public static function is_product_coming_soon_enabled( $product_id ) {
		$status = self::get_settings( $product_id, 'status' );
		if ( ! empty( $status ) && ( 'yes' === $status ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Is the product in Coming Soon Mode.
	 *
	 * @param int $product_id Product ID.
	 * @return boolean
	 */
	public static function is_product_coming_soon( $product_id ) {
		$status = self::get_settings( $product_id, 'status' );
		if ( ! empty( $status ) && ( 'yes' === $status ) ) {
			if ( ! self::is_auto_enable( $product_id ) ) {
				return true;
			}

			if ( self::is_auto_enable( $product_id ) ) {
				if ( ! self::is_product_arrival_time_passed( $product_id ) ) {
					return true;
				} else {
					self::update_coming_soon_list( $product_id, 'remove' );
					self::update_setting( $product_id, 'status', 'no' );
					self::update_setting( $product_id, 'auto_enable', 'no' );
					if ( self::is_auto_email( $product_id ) ) {
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
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Check if backorders are disabled or the product grants that.
	 *
	 * @param int $product_id Product ID.
	 * @return boolean
	 */
	public static function disable_backorders( $product_id ) {
		// 1) if it's disabled, return false directly.
		$_product           = wc_get_product( $product_id );
		$disable_backorders = self::get_settings( $product_id, 'disable_backorders' );
		if ( ! empty( $disable_backorders ) && ( 'yes' === $disable_backorders ) ) {
			return true;
		}
		// 2) return false only if the product allows any type of backorders.
		if ( ( 'onbackorder' === $_product->get_stock_status() ) || $_product->backorders_allowed() ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the product is unpurchasable based on the coming soon options and backorder options.
	 *
	 * @param int $product_id Product ID.
	 * @return boolean
	 */
	public static function is_product_unpurchasable( $product_id ) {
		return ( self::is_product_coming_soon( $product_id ) && self::disable_backorders( $product_id ) );
	}

	/**
	 * Get list of coming soon products.
	 *
	 * @return array
	 */
	public static function get_coming_soon_list() {
		return get_option( self::$coming_soon_hub_key, array() );
	}

	/**
	 * Update coming soon list.
	 *
	 * @param int    $product_id
	 * @param string $action 'add' - 'remove'
	 * @return void
	 */
	public static function update_coming_soon_list( $product_id, $action = 'add' ) {
		$coming_soon_list = self::get_coming_soon_list();
		$_product         = wc_get_product( $product_id );
		if ( 'add' === $action ) {
			if ( ! in_array( $product_id, $coming_soon_list ) ) {
				$coming_soon_list[] = $product_id;
			}
			// WPML Comp: Get all translations of the post to add them there.
			global $sitepress;
			if ( $sitepress ) {
				$trid = $sitepress->get_element_trid( $product_id, 'post_' . get_post_type( $product_id ) );
				if ( $trid ) {
					$translations = $sitepress->get_element_translations( $trid, 'post_' . get_post_type( $product_id ) );
					if ( ! empty( $translations ) && is_array( $translations ) ) {
						foreach ( $translations as $translation_code => $translation_data ) {
							$element_id = absint( $translation_data->element_id );
							if ( ! in_array( $element_id, $coming_soon_list ) ) {
								$coming_soon_list[] = $element_id;
							}
						}
					}
				}
			}
		} elseif ( 'remove' === $action ) {
			$index = array_search( $product_id, $coming_soon_list );
			if ( false !== $index ) {
				unset( $coming_soon_list[ $index ] );
			}
			// WPML Comp: Get all translations of the post to remove them from there.
			global $sitepress;
			if ( $sitepress ) {
				$trid = $sitepress->get_element_trid( $product_id, 'post_' . get_post_type( $product_id ) );
				if ( $trid ) {
					$translations = $sitepress->get_element_translations( $product_id, 'post_' . get_post_type( $product_id ) );
					if ( ! empty( $translations ) && is_array( $translations ) ) {
						foreach ( $translations as $translation_code => $translation_data ) {
							$element_id = absint( $translation_data->element_id );
							$index      = array_search( $element_id, $coming_soon_list );
							if ( false !== $index ) {
								unset( $coming_soon_list[ $index ] );
							}
						}
					}
				}
			}
		}
		update_option( self::$coming_soon_hub_key, $coming_soon_list, true );
	}

	/**
	 * Is the arrival time countdown should be shown.
	 *
	 * @param int $product_id Product ID.
	 * @return boolean
	 */
	public static function is_show_arrival_time_countdown( $product_id ) {
		$status = self::get_settings( $product_id, 'show_countdown' );
		if ( ! empty( $status ) && ( 'yes' === $status ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Is the coming soon product a variation.
	 *
	 * @param int $product_id Product ID.
	 * @return boolean
	 */
	public static function is_coming_soon_variation( $product_id ) {
		$vari_product = wc_get_product( $product_id );
		if ( ! $vari_product || ( 'variation' !== $vari_product->get_type() ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Is the product Arrival Time has passed.
	 *
	 * @param int $product_id Product ID.
	 * @return boolean
	 */
	public static function is_product_arrival_time_passed( $product_id ) {
		$settings     = self::get_settings( $product_id );
		$current_time = current_datetime()->getTimestamp();
		if ( empty( $settings['arrival_time'] ) ) {
			return false;
		}
		$arrival_time = DateTime::createFromFormat( 'Y-m-d\TH:i', $settings['arrival_time'], wp_timezone() );
		if ( false === $arrival_time ) {
			return false;
		}
		$arrival_time = $arrival_time->getTimestamp();
		return ( $current_time > $arrival_time );
	}

	/**
	 * Is Auto enable enabled after arrival time passed.
	 *
	 * @param int $product_id Product ID.
	 * @return boolean
	 */
	public static function is_auto_enable( $product_id ) {
		$settings = self::get_settings( $product_id );
		return ( ! empty( $settings['auto_enable'] ) && ( 'yes' === $settings['auto_enable'] ) );
	}

	/**
	 * Send Emails automatically.
	 *
	 * @param int $product_id Product ID.
	 * @return boolean
	 */
	public static function is_auto_email( $product_id ) {
		$settings = self::get_settings( $product_id );
		return ( ! empty( $settings['auto_email'] ) && ( 'yes' === $settings['auto_email'] ) );
	}

	/**
	 * Get Coming Soon products IDs along with arrival Time.
	 *
	 * @todo To be used later.
	 *
	 * @return array
	 */
	protected static function get_arrival_times_for_coming_soon_products() {
		$sorted_products          = array();
		$coming_soon_products_ids = array_map( 'absint', self::get_coming_soon_list() );
		if ( empty( $coming_soon_products_ids ) ) {
			return $sorted_products;
		}
		global $wpdb;
		$arrival_time_key = esc_sql( self::$plugin_info['name'] . '-arrival_time' );
		$ids_placeholders = implode( ', ', array_fill( 0, count( $coming_soon_products_ids ), '%d' ) );
		$datetime         = new DateTime();

		$datetime->add( new DateInterval( 'P1Y' ) );

		$datetime_for_nextyear = esc_sql( $datetime->format( 'Y-m-dTH:i' ) );
		$result                = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT
					ps.ID, IFNULL( pm.meta_value, '{$datetime_for_nextyear}' ) as arrival_time
				FROM
					{$wpdb->posts} ps
				LEFT JOIN
					{$wpdb->postmeta} pm
				ON
					ps.ID = pm.post_id
				AND
					pm.meta_key = '{$arrival_time_key}'
				WHERE
					ps.ID IN ($ids_placeholders)
				ORDER BY
					ps.post_date DESC",
				$coming_soon_products_ids
			),
			\ARRAY_A
		);

		return $result;
	}

	/**
	 * Sort Coming Soon Products based on arrival Time.
	 *
	 * @todo to be used later.
	 *
	 * @return array
	 */
	protected function sort_coming_soon_products_by_arrival_time() {
		$coming_soon_products_ids_with_arrival_time = self::get_arrival_times_for_coming_soon_products();
		usort(
			$coming_soon_products_ids_with_arrival_time,
			function( $a, $b ) {
				if ( $a['arrival_time'] === $b['arrival_time'] ) {
					return 0;
				}
				return ( $a['arrival_time'] < $b['arrival_time'] ) ? -1 : 1;
			}
		);
		return $coming_soon_products_ids_with_arrival_time;
	}

	/**
	 * Activation Hook.
	 *
	 * @return void
	 */
	public static function activate( $core, $plugin_info ) {
		new self( $core, $plugin_info );
		ComingSoonBackend::setup_auto_enable_and_emails_cron();
	}

	/**
	 * Deactivate Hook.
	 *
	 * @return void
	 */
	public static function deactivate() {
		delete_option( self::$background_emails_notices_key );
		delete_option( self::$background_emails_started_notices_key );
		// unschedule auto emails.
		$timestamp = wp_next_scheduled( self::$auto_enable_and_emails_cron_hook );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::$auto_enable_and_emails_cron_hook );
		}
	}

	/**
	 * Check if the product is a translation.
	 *
	 * @param int    $element_id
	 * @param string $element_type
	 * @return boolean
	 */
	public static function wpml_is_translation( $element_id, $element_type ) {
		global $sitepress, $wpdb;
		if ( ! $sitepress ) {
			return;
		}
		$query = "
		SELECT
			trid, language_code, source_language_code
		FROM
			{$wpdb->prefix}icl_translations
		WHERE
			element_id=%d
		AND
			element_type=%s
		";
		$result = $wpdb->get_row( $wpdb->prepare( $query, array( $element_id, $element_type ) ), \ARRAY_A );

		if ( ! $result || ! is_array( $result ) ) {
			return false;
		}

		return is_null( $result['source_language_code'] ) ? false : true;

	}

	/**
	 * Check if this is the current active theme.
	 *
	 * @param string $theme_slug
	 * @return boolean
	 */
	protected function is_theme_active( $theme_slug ) {
		return ( get_template() === $theme_slug );
	}
}
