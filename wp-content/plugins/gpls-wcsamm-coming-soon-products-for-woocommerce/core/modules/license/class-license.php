<?php
namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM\Modules\License;

use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Modules\Services\Helpers;


defined( 'ABSPATH' ) || exit();

/**
 * Plugin License Class
 */
class License {

	use Helpers;

	/**
	 * Core Object.
	 *
	 * @var object
	 */
	private $core;

	/**
	 * General Core Variables.
	 *
	 * @var array
	 */
	public $general_vars;

	/**
	 * License statuses
	 *
	 * @var array
	 */
	public static $statuses = array();

	/**
	 * Plugin Basename
	 *
	 * @var string
	 */
	protected $plugin_info;

	/**
	 * Single Instance
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Single Instance Initalization
	 *
	 * @param array $plugin_info
	 * @param array $general_vars
	 * @return object
	 */
	public static function init( $plugin_info, $core ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $plugin_info, $core );
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @param array $plugin_info
	 * @param array $general_vars
	 */
	private function __construct( $plugin_info, $core ) {
		$this->core        = $core;
		$this->plugin_info = $plugin_info;
		$this->hooks();

		self::$statuses = array(
			'nonce_failed' => array(
				'status_key' => 'nonce_failed',
				'status'     => 'Failed Nonce',
				'state'      => 'danger',
				'message'    => esc_html__( 'The link is expired. please, refresh the page!', 'gpls-core-plugins-pro' ),
			),
			'valid'        => array(
				'status_key' => 'valid',
				'status'     => 'Valid',
				'state'      => 'success',
				'message'    => esc_html__( 'The plugin is activated successfully', 'gpls-core-plugins-pro' ),
			),
			'expired'      => array(
				'status_key' => 'expired',
				'status'     => 'Expired',
				'state'      => 'danger',
				'message'    => esc_html__( 'The License Key is expired, please renew the lience from your', 'gpls-core-plugins-pro' ) . ' <a target="_blank" href="' . $this->downloads_link . '" >account</a> to get the new plugin\'s versions',
			),
			'failed'       => array(
				'status_key' => 'failed',
				'status'     => 'Failed',
				'state'      => 'danger',
				'message'    => esc_html__( 'Something went wrong, please try again. If the problem persists, Please contact us', 'gpls-core-plugins-pro' ),
			),
			'invalid'      => array(
				'status_key' => 'invalid',
				'status'     => 'Invalid',
				'state'      => 'danger',
				'message'    => esc_html__( 'Invalid License Key!', 'gpls-core-plugins-pro' ),
			),
			'exceeded'     => array(
				'status_key' => 'exceeded',
				'status'     => 'Exceeded',
				'state'      => 'danger',
				'message'    => esc_html__( 'Max Sites Exceeded for the License Key', 'gpls-core-plugins-pro' ),
			),
			'empty'        => array(
				'status_key' => 'empty',
				'status'     => 'Empty',
				'state'      => 'danger',
				'message'    => esc_html__( 'The License Key is empty!', 'gpls-core-plugins-pro' ),
			),
			'inactive'     => array(
				'status_key' => 'inactive',
				'status'     => 'Inactive',
				'state'      => 'danger',
				'message'    => esc_html__( 'The plugin is inactive', 'gpls-core-plugins-pro' ),
			),
			'removed'      => array(
				'status_key' => 'removed',
				'status'     => 'Inactive',
				'state'      => 'danger',
				'message'    => esc_html__( 'The License Key is empty!', 'gpls-core-plugins-pro' ),
			),
		);
	}


	/**
	 * Actions and Filters Hooks
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_ajax_' . $this->plugin_info['name'] . '-license-key-activate', array( $this, 'license_key_form_submit' ) );
		add_action( 'gpls-plugins-main-menu-list', array( $this, 'license_key_box_html' ) );
		add_action( 'gpls-plugins-main-menu-actions', array( $this, 'license_box_actions' ) );
		add_action( 'admin_menu', array( $this, 'main_plugins_menu' ) );
		add_filter( 'plugin_action_links_' . $this->plugin_info['basename'], array( $this, 'activate_page_link' ), 11, 1 );
	}

	/**
	 * License Key Activation Settings Page Link.
	 *
	 * @param array $links
	 * @return array
	 */
	public function activate_page_link( $links ) {
		if ( ! $this->is_active() ) :
			$links[] = '<a href="' . admin_url( 'admin.php?page=' . $this->plugins_main_menu_slug ) . '" >' . esc_html__( 'Activate License', 'gpls-core-plugins-pro' ) . '</a>';
		endif;
		return $links;
	}

	/**
	 * Grand Plugins Main Menu.
	 *
	 * @return void
	 */
	public function main_plugins_menu() {
		global $admin_page_hooks;

		if ( ! in_array( $this->plugins_main_menu_slug, array_keys( $admin_page_hooks ) ) ) {
			add_menu_page(
				esc_html__( 'GrandPlugins List', 'gpls-core-plugins-pro' ),
				'<span style="color:#FFF; font-family:\'gpls-plugins-vegan-font\'">GrandPlugins</span>',
				'manage_options',
				'gpls-main-plugins-menu',
				array( $this, 'plugins_menu_page_func' ),
				$this->core->core_assets_url . '/dist/images/logo-icon-sm.png'
			);
		}
	}

	/**
	 * Main Plugins Menu Page.
	 *
	 * @return void
	 */
	public function plugins_menu_page_func() {
		$this->license_box_styles();
		?>
		<div class="wrap">
			<h3 class="text-center my-5"><?php esc_html_e( 'Pro Plugins', 'gpls-core-plugins-pro' ); ?></h3>
			<div class="gpls-plugins-main-menu-wrapper container py-3">
				<ul class="list-group">
					<?php do_action( 'gpls-plugins-main-menu-list' ); ?>
				</ul>
			</div>
		</div>
		<?php
		do_action( 'gpls-plugins-main-menu-actions' );
	}

	/**
	 * License Key Box HTML
	 *
	 * @return void
	 */
	public function license_key_box_html() {
		$license_key = $this->get_license_key();
		?>
		<li class="list-group-item">
			<span class="font-weight-bold"><?php echo $this->plugin_info['public_name']; ?></span>
			<span class="plugin-list-item-icon dashicons dashicons-arrow-down float-right" data-toggle="collapse" aria-expanded="true" data-target="#<?php echo $this->plugin_info['name']; ?>-license-box" aria-controls="<?php echo $this->plugin_info['name']; ?>-license-box"></span>
		</li>

		<div id="<?php echo esc_attr( $this->plugin_info['name'] ); ?>-license-box" class="collapse plugin-list-item-license-box show mb-5">
			<div class="gpls-license-box m-4 px-4 pb-3 pt-4">
				<div class="d-none license-loader">
					<div class="loader-wrapper">
						<img src="<?php echo esc_url( admin_url( 'images/spinner-2x.gif' ) ); ?>" alt="license-spinner" >
					</div>
				</div>
				<?php if ( empty( $license_key ) || false === $license_key ) : ?>
				<div class="pl-3 license-pre-message">
					<p><?php esc_html_e( 'Enter your license Key here to activate The Plugin, and get feature updates, premium support', 'gpls-plugins-core-lang' ); ?></p>
					<ol>
						<li>Log in to <a href="<?php echo esc_url( $this->my_account_link ); ?>" target="_blank" > your account </a> to get your license key</li>
						<li>If you don't yet have a license key, get <a href="<?php echo esc_url( $this->plugin_info['plugin_url'] ); ?>" target="_blank" > one </a></li>
						<li>Copy the license key from your account and paste it below</li>
					</ol>
				</div>
				<?php endif; ?>
				<div class="license-inputs-wrapper p-3">
					<div class="gpls-plugin-license-key-form">
						<input type="hidden" class="license-key-nonce" id="<?php echo esc_attr( $this->plugin_info['name'] ); ?>-license-key-nonce" name="<?php echo esc_attr( $this->plugin_info['name'] ); ?>-license-key-nonce" value="<?php echo wp_create_nonce( esc_attr( $this->plugin_info['name'] ) . '-license-key' ); ?>">
						<label class="d-block font-weight-bold" for="<?php echo esc_attr( $this->plugin_info['name'] ); ?>-license-key">Your License Key:</label>
					<?php if ( empty( $license_key ) || false === $license_key ) : ?>
						<input type="hidden" class="subaction" name="sub-action" value="activate">
						<input class="regular-text license-input mr-2" type="text" id="<?php echo esc_attr( $this->plugin_info['name'] ); ?>-license-key" name="<?php echo esc_attr( $this->plugin_info['name'] ); ?>-license-key" value="" required>
						<input class="button button-primary license-submit" type="submit" class="button" value="Activate" data-box-id="<?php echo esc_attr( $this->plugin_info['name'] ); ?>-license-box">
					<?php else : ?>
						<input type="hidden" class="subaction" name="sub-action" value="remove">
						<input class="regular-text license-input mr-2" type="text" id="<?php echo esc_attr( $this->plugin_info['name'] ); ?>-license-key" name="<?php echo esc_attr( $this->plugin_info['name'] ); ?>-license-key" value="<?php echo esc_attr( $this->get_hidden_key( $license_key ) ); ?>" required disabled >
						<input class="button button-primary license-submit" type="submit" class="button" value="Remove" data-box-id="<?php echo esc_attr( $this->plugin_info['name'] ); ?>-license-box">
					<?php endif; ?>
					</div>
				</div>

				<?php $license_data = $this->get_license_data(); ?>
				<p class="pl-3 license-status">
					<span>Status :  </span> <span class="status <?php echo esc_attr( ( false === $license_data || empty( self::$statuses[ $license_data['status'] ]['status'] ) ) ? 'danger' : self::$statuses[ $license_data['status'] ]['state'] ); ?>"><?php echo esc_html( ( empty( $license_data ) || false === $license_data ) ? 'Inactive' : self::$statuses[ $license_data['status'] ]['status'] ); ?></span>
				</p>

				<h6 class="license-message bg-light shadow-sm rounded p-2">
				<?php if ( ! empty( $license_data ) ) : ?>
					<?php echo ( is_array( $license_data ) && isset( $license_data['status'] ) && ! empty( self::$statuses[ $license_data['status'] ]['status'] ) ? self::$statuses[ $license_data['status'] ]['message'] : '' ); ?>
				<?php else : ?>
					<?php echo self::$statuses['inactive']['message']; ?>
				<?php endif; ?>
				</h6>
			</div>
		</div>
		<?php
	}

	/**
	 * License Box Styles
	 *
	 * @return void
	 */
	public function license_box_styles() {
		?>
		<style>
			.gpls-plugins-main-menu-wrapper .plugin-list-item-icon { cursor: pointer; }
			.gpls-license-box {position: relative; display: inline-block;box-shadow: 1px 3px 7px 3px rgba(9, 30, 66, 0.14);border-radius: 6px 6px 6px 6px;}
			.gpls-license-box .license-inputs-wrapper .license-input {height: 35px;border-radius: 4px;border: 1px solid #DEDEDE;}
			.gpls-license-box .license-inputs-wrapper .license-submit {height: 35px;}
			.gpls-license-box .license-loader {z-index: 1000;position: absolute;width: 100%;height: 100%;top: 0;left: 0;}
			.gpls-license-box .license-loader .loader-wrapper {width: 100%;height: 100%;background: #EEE;opacity: 0.8;}
			.gpls-license-box .license-loader .loader-wrapper img {position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);}
			.gpls-license-box .license-status .status {font-weight: bolder; font-size: 1.2em; text-transform: capitalize;}
			.gpls-license-box .license-status .status.danger{color: #ff2300;}
			.gpls-license-box .license-status .status.success{color: #7ddc15;}
			.gpls-license-box .license-status .status.warning{color: #06d432;}
			.gpls-license-box .license-pre-message ol{padding-left: 0px;font-size: 0.9em;}
		</style>
		<?php
	}

	/**
	 * License Box JS Actions
	 *
	 * @return void
	 */
	public function license_box_actions() {
		?>
		<script>
			jQuery(document).on('ready', function() {
				let licenseBox = jQuery('#<?php echo $this->plugin_info['name']; ?>-license-box');
				licenseBox.find('.gpls-plugin-license-key-form .license-submit').on('click', function(e) {
					e.preventDefault();
					let licenseKey       = licenseBox.find('.license-input').val();
					let licenseSubAction = licenseBox.find('.subaction').val();
					let licenseNonce     = licenseBox.find('.license-key-nonce').val();

					licenseBox.find('.license-loader').removeClass('d-none');
					licenseBox.find('.license-message').addClass('d-none');
					jQuery.ajax({
						method: 'POST',
						url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
						data: {
							action: '<?php echo esc_attr( $this->plugin_info['name'] ); ?>-license-key-activate',
							nonce: licenseNonce,
							subaction: licenseSubAction,
							key: licenseKey
						},
						success: function( resp ) {
							licenseBox.find('.license-status .status').text( resp['status'] ).removeClass('success danger warning').addClass( resp['state'] );
							licenseBox.find('.license-message').html( resp['message'] );
							if ( 'valid' === resp['status_key'] ) {

								licenseBox.find('.subaction').attr( 'value', 'remove' );
								licenseBox.find('.license-submit').attr( 'value', 'Remove' );
								licenseBox.find('.license-input').attr( 'disabled', true );
								licenseBox.find('.license-input').val( resp['hidden_key'] );
								licenseBox.find('.license-pre-message').hide();

							} else if ( 'expired' === resp['status_key'] || 'exceeded' === resp['status_key'] ) {

								licenseBox.find('.subaction').attr( 'value', 'activate' );
								licenseBox.find('.license-submit').attr( 'value', 'Activate' );
								licenseBox.find('.license-input').attr( 'disabled', false );
								licenseBox.find('.license-input').val('');
								licenseBox.find('.license-pre-message').show();

							} else if ( 'invalid' === resp['status_key'] || 'removed' === resp['status_key'] || 'inactive' === resp['status_key'] ) {

								licenseBox.find('.subaction').attr( 'value', 'activate' );
								licenseBox.find('.license-submit').attr( 'value', 'Activate' );
								licenseBox.find('.license-input').attr( 'disabled', false );
								licenseBox.find('.license-input').val('');
								licenseBox.find('.license-pre-message').show();

							}
						},
						error: function( err ) {
						},
						complete: function() {
							licenseBox.find('.license-loader').addClass('d-none');
							licenseBox.find('.license-message').removeClass('d-none');
						}
					});
				});
			});
		</script>
		<?php
	}

	/**
	 * Ajax License Key Activation
	 *
	 * @return void
	 */
	public function license_key_form_submit() {
		$resp = '';
		if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( wp_unslash( $_POST['nonce'] ), $this->plugin_info['name'] . '-license-key' ) ) {

			$license_key = ! empty( $_POST['key'] ) ? sanitize_text_field( $_POST['key'] ) : '';
			if ( empty( $license_key ) ) {
				$resp = self::$statuses['empty'];
			} else {
				$subaction = ( ! empty( $_POST['subaction'] ) ? sanitize_text_field( $_POST['subaction'] ) : 'activate' );

				if ( 'activate' === $subaction ) {
					$result = $this->check_license_key( $this->plugin_info['id'], $license_key );
					if ( ( false !== $result ) && ( is_array( $result ) ) && isset( $result['status'] ) && in_array( $result['status'], array_keys( self::$statuses ), true ) ) {
						$resp = self::$statuses[ $result['status'] ];
						if ( 'valid' === $result['status'] || 'expired' === $result['status'] ) {
							$this->set_license_key_and_data( $license_key, $result );
							$resp['hidden_key'] = $this->get_hidden_key( $license_key );
						}
					} else {
						$resp = self::$statuses['failed'];
					}
				} elseif ( 'remove' === $subaction ) {
					$this->remove_license_key();
					$resp = self::$statuses['removed'];
				}
			}
		} else {
			$resp = self::$statuses['nonce_failed'];
		}
		wp_send_json( $resp );
	}

	/**
	 * Check License Key
	 *
	 * @param int    $plugin_id
	 * @param string $license_key
	 * @param int    $counter
	 * @return array
	 */
	public function check_license_key( $plugin_id, $license_key, $other_check = false, $counter = 0 ) {
		$response = wp_remote_post(
			$this->plugin_license_route,
			array(
				'body' => array(
					'id'          => $plugin_id,
					'license_key' => $license_key,
					'domain'      => get_site_url(),
				),
			)
		);
		$body = (array) json_decode( wp_remote_retrieve_body( $response ) );
		if ( empty( $body ) && ( false === $other_check ) && ( $counter < 2 ) ) {
			$body = $this->check_license_key( $plugin_id, $license_key, true, ++$counter );
		}
		return $body;
	}

	/**
	 * Set License Key
	 *
	 * @param string $license_key
	 * @return boolean
	 */
	private function set_license_key_and_data( $license_key, $license_data ) {
		update_option( $this->plugin_info['name'] . '-license-key', $license_key );
		$result = $this->set_license_data( $license_data );
		return $result;
	}

	/**
	 * Set License Data
	 *
	 * @param string|array $license_data
	 * @return boolean
	 */
	private function set_license_data( $license_data ) {
		$transient_name = trim( $this->plugin_info['name'] . '-license-data' );
		$result         = set_site_transient(
			$transient_name,
			$license_data,
			24 * HOUR_IN_SECONDS
		);
		return $result;
	}

	/**
	 * Get License Key
	 *
	 * @return false|string
	 */
	private function get_license_key() {
		$result = get_option( $this->plugin_info['name'] . '-license-key' );
		return $result;
	}

	/**
	 * Get License Data
	 *
	 * @return false|string
	 */
	private function get_license_data() {
		$transient_name = trim( $this->plugin_info['name'] . '-license-data' );
		$result         = get_site_transient( $transient_name );

		if ( ! empty( $result ) && false !== $result ) {
			return $result;
		}

		$license_key = $this->get_license_key();

		if ( empty( $license_key ) || false === $license_key ) {
			return false;
		}

		$result = $this->check_license_key( $this->plugin_info['id'], $license_key );
		if ( is_array( $result ) && ! empty( $result['status'] ) && in_array( $result['status'], array_keys( self::$statuses ), true ) ) {
			$this->set_license_data( $result );
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * Check if activated and valid
	 *
	 * @return boolean
	 */
	public function is_active() {
		$license_key  = $this->get_license_key();
		$license_data = $this->get_license_data();

		if ( empty( $license_key ) || false === $license_key || empty( $license_data ) || false === $license_data ) {
			return false;
		}
		if ( is_array( $license_data ) && ! empty( $license_data['status'] ) && 'invalid' !== $license_data['status'] && 'exceeded' !== $license_data['status'] ) {
			return true;
		}
		return false;
	}

	/**
	 * Get Hidden License Key
	 *
	 * @param string $license_key
	 * @return string
	 */
	private function get_hidden_key( $license_key ) {
		return substr_replace( $license_key, 'XXXXX', 5, 20 );
	}

	/**
	 * Delete licence Key
	 *
	 * @return void
	 */
	private function remove_license_key() {
		delete_option( $this->plugin_info['name'] . '-license-key' );
		delete_site_transient( $this->plugin_info['name'] . '-license-data' );
	}

	/**
	 * Activate Notice.
	 *
	 * @return void
	 */
	public function activate_msg( $inline = false ) {
		?>
		<div class="notice notice-warning<?php echo esc_attr( $inline ? ' inline' : '' ); ?>">
			<p><?php echo esc_html__( 'Please, ' ) . '<a href="' . esc_url_raw( admin_url( '?page=gpls-main-plugins-menu' ) ) . '" >' . esc_html__( 'Activate' ) . '</a> <b>' . esc_html( $this->plugin_info['public_name'] ) . '</b>' .  esc_html__( ' plugin in order to access full features' ); ?></p>
		</div>
		<?php
	}
}
