<?php
namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM\Modules\Updates;

use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Modules\Services\Helpers;

defined( 'ABSPATH' ) || exit();

/**
 * Plugin Update Class
 */
class Update {

	use Helpers;

	/**
	 * Core Object.
	 *
	 * @var object
	 */
	private $core;

	/**
	 * Single Instance
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Plugin Info
	 *
	 * @var array
	 */
	protected $plugin_info;

	/**
	 * Array of Cron Events
	 *
	 * @var array
	 */
	protected $cron_events = array();

	/**
	 * Single Instance Initalization
	 *
	 * @param array $plugin_info
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
	 */
	private function __construct( $plugin_info, $core ) {
		$this->core        = $core;
		$this->plugin_info = $plugin_info;
		$this->hooks();
	}


	/**
	 * Actions and Filters Hooks
	 *
	 * @return void
	 */
	public function hooks() {
		if ( $this->core->is_active() ) {
			add_action( 'gpls_core_plugins_update_check_action', array( $this, 'check_plugin_new_version' ), 100, 1 );
			add_filter( 'gpls_core_plugins_ids_and_versions_for_update', array( $this, 'pass_plugin_id_and_version_for_update_check' ), 100, 1 );
			add_action( 'after_plugin_row_' . $this->plugin_info['basename'], array( $this, 'update_plugin_message' ), 10, 3 );
			add_action( 'admin_notices', array( $this, 'update_plugin_admin_notice' ) );
			add_action( 'wp_ajax_' . esc_attr( $this->plugin_info['name'] ) . '_update_notice_dismiss_action', array( $this, 'dismiss_update_notice' ) );
			add_filter( 'plugin_action_links_' . $this->plugin_info['basename'], array( $this, 'plugin_update_button' ), 10, 1 );
			add_action( 'wp_ajax_' . esc_attr( $this->plugin_info['name'] ) . '_check_for_updates', array( $this, 'check_for_updates' ) );
			add_action( 'wp_ajax_' . esc_attr( $this->plugin_info['name'] ) . '_update_check_login', array( $this, 'check_user_account_before_update' ) );
			add_action( 'wp_ajax_' . esc_attr( $this->plugin_info['name'] ) . '_update_start', array( $this, 'perform_update' ) );
			add_action( 'upgrader_process_complete', array( $this, 'update_plugin_info_with_new_version' ), 100, 2 );
			add_action( 'admin_footer', array( $this, 'update_handle_js' ), 10000 );
			add_filter( 'upgrader_pre_download', array( $this, 'before_downloading_update' ), 1000, 4 );
		}
		add_filter( 'site_transient_update_plugins', array( $this, 'disable_wp_updates' ), 1000, 1 );
	}

	/**
	 * Disable WP Updates.
	 *
	 * @param object $update_plugins
	 * @return array
	 */
	public function disable_wp_updates( $update_plugins ) {
		if ( isset( $update_plugins->response[ $this->plugin_info['basename'] ] ) ) {
			unset( $update_plugins->response[ $this->plugin_info['basename'] ] );
		}
		return $update_plugins;
	}

	public function update_handle_js() {
		$screen = get_current_screen();
		if ( $screen && ( 'plugins' === $screen->base ) ) :
			?>
			<script type="text/javascript" id="<?php echo esc_attr( $this->plugin_info['name'] . '-core-js' ); ?>" >
			( function( $ ) {
				var pluginID              = '';
				var pluginSlug            = '';
				var pluginVersion         = '';
				var pluginNewVersion      = '';
				var pluginRow;
				var email                 = '';
				var password              = '';


				$(document).on( 'ready', function() {
					// Update plugin link action.
					$('.<?php echo esc_attr( $this->plugin_info['name'] ); ?>-update-plugin').on( 'click', function(e) {
						e.preventDefault();
						var buttonEl     = $(this);
						pluginID         = buttonEl.data('id');
						pluginVersion    = buttonEl.data('version');
						pluginNewVersion = buttonEl.data('new_version');
						pluginSlug       = buttonEl.data('plugin');
						pluginRow        = $( 'table.plugins tr[data-plugin="' + pluginSlug + '"]' );
						email            = '';
						password         = '';
						( async () => {
							Swal.queue([
								{
									title: '<h5 style="line-height:30px;font-size:25px;margin:10px;"><?php esc_html_e( 'Enter your', 'gpls-core-plugins-pro' ); ?> <a href="https://grandplugins.com" target="_blank" >GrandPlugins</a> <?php esc_html_e( 'Account Email and Password', 'gpls-core-plugins-pro' ); ?></h5>',
									imageUrl: '<?php echo esc_attr( $this->core->core_assets_url . '/dist/images/full-logo.png' ); ?>',
									customClass: {
										container: 'gpls-core-plugins-pro-update-plugin-sweetalert-container'
									},
									confirmButtonText: 'Check',
									html:
									`<label for="<?php echo esc_attr( $this->plugin_info['name'] . '-update-plugin-email' ); ?>" >
										<h4 style="padding-left: 30px; text-align: left;"><?php esc_html_e( 'Email', 'gpls-core-plugins-pro' ); ?></h4>
										<input style="line-height: 40px;" type="email" id="<?php echo esc_attr( $this->plugin_info['name'] . '-update-plugin-email' ); ?>" class="<?php echo esc_attr( $this->plugin_info['name'] . '-update-plugin-email' ); ?> regular-text" >
									</label>
									<label for=<?php echo esc_attr( $this->plugin_info['name'] . '-update-plugin-password' ); ?>-update-plugin-password" >
										<h4 style="padding-left: 30px; text-align: left;"><?php esc_html_e( 'Password', 'gpls-core-plugins-pro' ); ?></h4>
										<input style="line-height: 40px;" type="password" id="<?php echo esc_attr( $this->plugin_info['name'] . '-update-plugin-password' ); ?>" class="<?php echo esc_attr( $this->plugin_info['name'] . '-update-plugin-password' ); ?> regular-text" >
									</label>
									`,
									showLoaderOnConfirm: true,
									preConfirm: () => {
										email    = $('.<?php echo esc_attr( $this->plugin_info['name'] . '-update-plugin-email' ); ?>').val();
										password = $('.<?php echo esc_attr( $this->plugin_info['name'] . '-update-plugin-password' ); ?>').val();
										if ( ! email.length || ! password.length ) {
											Swal.showValidationMessage( "<?php esc_html_e( 'Email or Password are empty', 'gpls-core-plugins-pro' ); ?>" );
											return false;
										}

										if ( ! validateEmail( email ) ) {
											Swal.showValidationMessage( "<?php esc_html_e( 'Email is not valid!', 'gpls-core-plugins-pro' ); ?>" );
											return false;
										}
									},
									inputValidator: ( value ) => {
									}
								},
								{
									title : '<?php esc_html_e( 'checking login details...', 'gpls-core-plugins-pro' ); ?>',
									imageUrl: '<?php echo esc_attr( includes_url( 'images/spinner-2x.gif' ) ); ?>',
									confirmButtonText: 'Update',
									customClass: {
										container: 'gpls-core-plugins-pro-update-plugin-sweetalert-container',
										confirmButton: 'confirm-update'
									},
									preConfirm: ( val ) => {
										let link   = $('.gpls-core-plugins-pro-update-plugin-sweetalert-container .confirm-update').data('link');
										let plugin = $('.gpls-core-plugins-pro-update-plugin-sweetalert-container .confirm-update').data('plugin');

										// Update the plugin notice row with Updating...
										pluginRow.find('.update-message').removeClass('notice-error').addClass('updating-message notice-warning' ).find('p').text( '<?php esc_html_e( 'Updating...', 'gpls-core-plugins-pro' ); ?>' );
										start_update( link, plugin, buttonEl );
									},
									willOpen: () => {
										let data = {
											id: pluginID,
											version: pluginVersion,
											email: email,
											password: password
										}
										check_update_user_login_details( data );
									}
								},

							]);
						})();

					});

					// Check for updates action.
					$('.<?php echo esc_attr( $this->plugin_info['name'] ); ?>-check-for-updates').on( 'click', function(e) {
						e.preventDefault();
						var buttonEl     = $(this);
						pluginID         = buttonEl.data('id');
						pluginVersion    = buttonEl.data('version');
						let data         = {
							'id': pluginID,
							'version': pluginVersion
						};
						check_for_updates_callback( data );
					});

				});

				/**
				 * Check for new updates Button.
				 */
				function check_for_updates_callback( data ) {
					$('.<?php echo esc_attr( $this->plugin_info['name'] ); ?>-check-for-updates').addClass( 'disabled' );
					$('.<?php echo esc_attr( $this->plugin_info['name'] ); ?>-check-for-updates-spinner').css( 'display', 'inline-block' );
					$.ajax({
						method: 'POST',
						url: '<?php echo esc_url_raw( admin_url( 'admin-ajax.php' ) ); ?>',
						data: {
							action: '<?php echo esc_attr( $this->plugin_info['name'] ); ?>_check_for_updates',
							nonce: '<?php echo esc_attr( wp_create_nonce( 'gpls-core-plugins-pro-update-plugin' ) ); ?>'
						},
						success: function( resp ) {
							if ( ! resp.data.result ) {
								Swal.fire({
									icon: 'success',
									html: '<p><?php esc_html_e( 'You have the plugin latest version', 'gpls-core-plugins-pro' ); ?></p>'
								});
							} else {
								Swal.fire({
									icon: 'info',
									html: '<p><?php esc_html_e( 'A new version is avaible, please refresh to update', 'gpls-core-plugins-pro' ); ?></p>'
								});
							}
						},
						error: function( err ) {
						},
						complete: function() {
							$('.<?php echo esc_attr( $this->plugin_info['name'] ); ?>-check-for-updates').removeClass( 'disabled' );
							$('.<?php echo esc_attr( $this->plugin_info['name'] ); ?>-check-for-updates-spinner').css( 'display', 'none' );
						}
					});
				}

				/**
				* Check User Login Details Before Updating the Plugin.
				*
				*/
				function check_update_user_login_details( data ) {
					$.ajax({
						method: 'POST',
						url: '<?php echo esc_url_raw( admin_url( 'admin-ajax.php' ) ); ?>',
						data: {
							action: '<?php echo esc_attr( $this->plugin_info['name'] . '_update_check_login' ); ?>',
							nonce: '<?php echo esc_attr( wp_create_nonce( 'gpls-core-plugins-pro-update-plugin' ) ); ?>',
							data
						},
						success: function( resp ) {
							var newOptions = {
								icon: 'success',
								imageUrl: '',
								title: ''
							};

							if ( ! resp['success'] ) {
								newOptions['icon'] = 'error';
							}

							if ( resp['success'] && resp['data']['download_url'] ) {
								$('.gpls-core-plugins-pro-update-plugin-sweetalert-container .confirm-update')
								.attr(
									{
										'data-link': resp['data']['download_url'],
										'data-plugin': pluginSlug
									}
								).css( 'visibility', 'visible' );
							}

							newOptions['html'] = resp['data']['message'];

							Swal.update( newOptions );

						},
						error: function( err ) {
						}
					});
				}

				/**
				* Start Update the plugin with new version.
				* @param {string} downloadUrl New Plugin Update Link.
				* @param {string} plugin plugin basename.
				* @param {object} buttonElement The Update Button jQuery Element.
				*/
				function start_update( downloadUrl, plugin, buttonElement ) {
					// Hide update button
					buttonElement.hide();

					// Update the plugin notice row with Updating...
					pluginRow.find('.update-message').removeClass('notice-error').addClass('updating-message notice-warning' ).find('p').text( '<?php esc_html_e( 'Updating...', 'gpls-core-plugins-pro' ); ?>' );

					$.ajax({
						method: 'POST',
						url: '<?php echo esc_url_raw( admin_url( 'admin-ajax.php' ) ); ?>',
						data: {
							action: '<?php echo esc_attr( $this->plugin_info['name'] . '_update_start' ); ?>',
							nonce: '<?php echo esc_attr( wp_create_nonce( 'gpls-core-plugins-pro-update-plugin' ) ); ?>',
							download_url: downloadUrl,
							plugin: plugin
						},
						success: function( resp ) {
							if ( ! resp['success'] ) {
								errorAlert( resp['data'] );
								pluginRow.find('.update-message').removeClass('updating-message notice-warning').addClass('notice-error').find('p').text( '<?php esc_html_e( 'Update failed', 'gpls-core-plugins-pro' ); ?>' );
								buttonElement.show();
							} else {
								// Update the plugin notice row with Updated
								pluginRow.removeClass('update').addClass('updated');
								pluginRow.find('.update-message').removeClass('updating-message notice-warning').addClass( 'updated-message notice-success' ).find('p').text( '<?php esc_html_e( 'Updated!', 'gpls-core-plugins-pro' ); ?>' );

								// Update the plugin Version with the new Version String
								let newVersionText = pluginRow.find('.plugin-version-author-uri').html().replace( pluginVersion, pluginNewVersion );
								pluginRow.find('.plugin-version-author-uri').html( newVersionText );
							}
						},
						error: function( err ) {
						}
					});
				}

				/**
				* Fires Swal Alert.
				* @param {string} alertMessage Alert Message String.
				*/
				function errorAlert( alertMessage ) {
					Swal.fire({
						icon: 'error',
						html: alertMessage
					});
				}

				function validateEmail(email) {
					const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
					return re.test(String(email).toLowerCase());
				}

			})( jQuery );
			</script>
			<?php
		endif;
	}

	/**
	 * Ajax Check of updates.
	 *
	 * @return void
	 */
	public function check_for_updates() {

		if ( ! empty( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'gpls-core-plugins-pro-update-plugin' ) ) {

			$plugins_ids_and_versions = apply_filters( 'gpls_core_plugins_ids_and_versions_for_update', array() );
			$plugins_new_versions     = $this->plugin_update_request( $plugins_ids_and_versions );
			$this->set_plugin_update_object( $plugins_new_versions, false );

			if ( is_array( $plugins_new_versions ) && ! empty( $plugins_ids_and_versions[ $this->plugin_info['name'] ] ) ) {
				$result = true;
			} else {
				$result = false;
			}
			wp_send_json_success(
				array(
					'result' => $result,
				)
			);
		} else {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'The link has expired, please refresh the page!', 'gpls-core-plugins-pro' ),
				)
			);
		}
	}

	/**
	 * Custom Plugin Update Message.
	 *
	 * @return void
	 */
	public function update_plugin_message( $plugin_file, $plugin_data, $status ) {
		$main_options = get_option( $this->core->plugins_main_options );
		if ( ! empty( $main_options ) && ! empty( $main_options['installed_plugins'][ $this->plugin_info['name'] ] ) && ! empty( $main_options['installed_plugins'][ $this->plugin_info['name'] ]['new_version'] ) ) {
			ob_start();
			$new_version      = $main_options['installed_plugins'][ $this->plugin_info['name'] ]['new_version'];
			$new_version_logs = $main_options['installed_plugins'][ $this->plugin_info['name'] ]['logs'];
			?>
			<tr class="plugin-update-tr <?php echo esc_attr( $status ); ?>" id="<?php echo $this->plugin_info['name']; ?>-update" data-slug="<?php echo $this->plugin_info['name']; ?>" data-plugin="<?php echo $this->plugin_info['basename']; ?>" >
				<td colspan="4" class="plugin-update colspanchange" >
					<div class="update-message notice inline notice-warning notice-alt">
						<p><?php printf( esc_html__( 'Version %s available', 'gpls-text-domain' ), $new_version ); ?></p>
						<div class="gpls-core-plugins-pro-new-version-logs">
							<?php echo wp_kses_post( $new_version_logs ); ?>
						</div>
					</div>
				</td>
			</tr>
			<?php
			echo ob_get_clean();
		}
	}

	/**
	 * License Key Activation Settings Page Link.
	 *
	 * @param array $links
	 * @return array
	 */
	public function plugin_update_button( $links ) {
		$main_options = get_option( $this->core->plugins_main_options );
		if ( ! empty( $main_options ) && ! empty( $main_options['installed_plugins'][ $this->plugin_info['name'] ] ) && ! empty( $main_options['installed_plugins'][ $this->plugin_info['name'] ]['new_version'] ) ) {
			$links[] = '<a href="javascript: void(0)" class="' . esc_attr( $this->plugin_info['name'] . '-update-plugin' ) . '" data-new_version="' . esc_attr( $main_options['installed_plugins'][ $this->plugin_info['name'] ]['new_version'] ) . '" data-version = "' . $this->plugin_info['version'] . '" data-plugin="' . $this->plugin_info['basename'] . '" data-id="' . $this->plugin_info['id'] . '" data-version="' . $this->plugin_info['version'] . '">' . esc_html__( 'Update', 'gpls-core-plugins-pro' ) . '</a>';
		} else {
			$links[] = '<a data-prefix="' . esc_attr( $this->plugin_info['name'] ) . '" href="javascript: void(0)" class="gpls-core-plugins-pro-check-for-updates ' . esc_attr( $this->plugin_info['name'] ) . '-check-for-updates" data-id="' . $this->plugin_info['id'] . '" data-version="' . $this->plugin_info['version'] . '">' . esc_html__( 'Check for new updates', 'gpls-core-plugins-pro' ) . '<img class="' . esc_attr( $this->plugin_info['name'] ) . '-check-for-updates-spinner gpls-core-plugins-pro-check-for-updates-spinner" src="' . esc_url_raw( includes_url( 'images/spinner.gif' ) ) . '" /></a>';
		}
		if ( $this->core->is_active() ) {
			if ( ! empty( $this->plugin_info['review_link'] ) ) {
				$links[] = '<a target="_blank" data-plugin="' . $this->plugin_info['basename'] . '" href="' . esc_url_raw( $this->plugin_info['review_link'] ) . '" >' . esc_html__( 'Review' ) . '</a>';
			}
			$links[] = '<a target="_blank" data-plugin="' . $this->plugin_info['basename'] . '" href="https://grandplugins.com/tickets/" >' . esc_html__( 'Support' ) . '</a>';
		}
		return $links;
	}

	/**
	 * Dismiss new Version notice.
	 *
	 * @return void
	 */
	public function dismiss_update_notice() {
		if ( wp_doing_ajax() && ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gpls-core-plugins-main-nonce' ) ) && ! empty( $_POST['plugin_name'] ) ) {
			$plugin_name  = sanitize_text_field( wp_unslash( $_POST['plugin_name'] ) );
			$main_options = get_option( $this->core->plugins_main_options );
			if ( ! empty( $main_options ) && ! empty( $main_options['installed_plugins'][ $plugin_name ] ) ) {
				$main_options['installed_plugins'][ $plugin_name ]['admin_notice_dismiss'] = true;
				update_option( $this->core->plugins_main_options, $main_options, true );
			}
		}
		wp_send_json_success( 'end' );
	}

	/**
	 * Admin Notice for Plugin Update
	 *
	 * @return void
	 */
	public function update_plugin_admin_notice() {
		$main_options = get_option( $this->core->plugins_main_options );
		if ( ! empty( $main_options ) && ! empty( $main_options['installed_plugins'][ $this->plugin_info['name'] ] ) && ! empty( $main_options['installed_plugins'][ $this->plugin_info['name'] ]['new_version'] ) && empty( $main_options['installed_plugins'][ $this->plugin_info['name'] ]['admin_notice_dismiss'] ) ) {
			?>
			<div class="notice notice-warning" style="position:relative;">
				<p>
					<?php esc_html_e( 'A new version of', 'gpls-text-domain' ); ?> <b><?php echo esc_html( $this->plugin_info['public_name'] ); ?></b> <?php esc_html_e( 'Plugin is available', 'gpls-text-domain' ); ?>.
					<button data-plugin="<?php echo esc_attr( $this->plugin_info['name'] ); ?>" type="button" class="notice-dismiss <?php echo esc_attr( $this->plugin_info['name'] . '_dismiss_button_class' ); ?>"><span class="screen-reader-text"></span></button>
				</p>
			</div>
			<script type="text/javascript">
			( function( $ ) {
				$('.<?php echo esc_attr( $this->plugin_info['name'] . '_dismiss_button_class' ); ?>').on( 'click', function() {
					var plugin_name = $(this).data('plugin');
					$(this).closest('.notice').remove();
					$.ajax({
						method: 'POST',
						url: '<?php echo esc_url_raw( admin_url( 'admin-ajax.php' ) ); ?>',
						data: {
							action: '<?php echo esc_attr( esc_attr( $this->plugin_info['name'] ) . '_update_notice_dismiss_action' ); ?>',
							nonce: '<?php echo esc_attr( wp_create_nonce( 'gpls-core-plugins-main-nonce' ) ); ?>',
							plugin_name: plugin_name
						}
					});
				});
			})(jQuery);
			</script>
			<?php
		}
	}

	/**
	 * Update the plugin new version in main options
	 *
	 * @param array|false $plugins_new_versions Plugins New Versions Array.
	 * @return void
	 */
	public function set_plugin_update_object( $plugins_new_versions, $update_time = true ) {
		$main_options = get_option( $this->core->plugins_main_options );
		if ( is_array( $plugins_new_versions ) && ! empty( $plugins_new_versions ) ) {
			if ( ! empty( $main_options ) ) {
				foreach ( $plugins_new_versions['plugins'] as $plugin_name => $new_version ) {
					if ( ! empty( $main_options['installed_plugins'][ sanitize_text_field( $plugin_name ) ] ) && ! empty( $main_options['installed_plugins'][ sanitize_text_field( $plugin_name ) ]['type'] ) && ( 'pro' === $main_options['installed_plugins'][ sanitize_text_field( $plugin_name ) ]['type'] ) ) {
						$main_options['installed_plugins'][ sanitize_text_field( $plugin_name ) ]['new_version'] = sanitize_text_field( $new_version );
						$main_options['installed_plugins'][ sanitize_text_field( $plugin_name ) ]['logs']        = $plugins_new_versions['logs'][ $plugin_name ];
						unset( $main_options['installed_plugins'][ sanitize_text_field( $plugin_name ) ]['admin_notice_dismiss'] );
					}
				}
			}
		}
		if ( $update_time ) {
			$main_options['plugins_update_check']['timestamp'] = microtime( true );
		}
		update_option( $this->core->plugins_main_options, $main_options, true );
	}

	/**
	 * Pass Plugin ID and Version to check for new version.
	 *
	 * @param array $plugins_ids_versions Activated Plugins IDs and Versions.
	 * @return array
	 */
	public function pass_plugin_id_and_version_for_update_check( $plugins_ids_versions ) {
		$plugins_ids_versions[ $this->plugin_info['name'] ] = array(
			'id'      => $this->plugin_info['id'],
			'version' => $this->plugin_info['version'],
		);
		return $plugins_ids_versions;
	}

	/**
	 * Check if there is a new version of the plugin.
	 *
	 * @param $counter Execution Coutner.
	 * @return void
	 */
	public function check_plugin_new_version( $counter ) {
		if ( 0 === $counter ) {
			$plugins_ids_and_versions = apply_filters( 'gpls_core_plugins_ids_and_versions_for_update', array() );
			$plugins_new_versions     = $this->plugin_update_request( $plugins_ids_and_versions );
			$this->set_plugin_update_object( $plugins_new_versions );
		}
		return ++$counter;
	}


	/**
	 * Check if there is a plugin update or not
	 *
	 * @param array $plugins_new_versions Plugins IDs and Versions.
	 * @return array
	 */
	public function plugin_update_request( $plugins_new_versions ) {
		$response = wp_remote_post(
			$this->plugin_update_route,
			array(
				'body' => array(
					'plugins' => $plugins_new_versions,
				),
			)
		);

		if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
			$body = (array) json_decode( wp_remote_retrieve_body( $response ) );
			if ( ! empty( $body['result'] ) && ( true === $body['result'] ) && ( ! empty( $body['plugins'] ) ) ) {
				return array(
					'plugins' => (array) $body['plugins'],
					'logs'    => (array) $body['logs'],
				);
			}
		}
		return false;
	}

	/**
	 * Ajax Check User Login Email and Password before updating the plugin.
	 */
	public function check_user_account_before_update() {
		if ( ! empty( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'gpls-core-plugins-pro-update-plugin' ) ) {
			if ( empty( $_POST['data']['email'] ) || empty( $_POST['data']['password'] ) || empty( $_POST['data']['id'] ) || empty( $_POST['data']['version'] ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Invalid data', 'gpls-core-plugins-pro' ),
					)
				);
			}

			$email    = sanitize_email( wp_unslash( $_POST['data']['email'] ) );
			$password = trim( $_POST['data']['password'] );
			$id       = absint( sanitize_text_field( wp_unslash( $_POST['data']['id'] ) ) );
			$version  = sanitize_text_field( wp_unslash( $_POST['data']['version'] ) );

			$response = wp_remote_post(
				$this->plugin_do_update_route,
				array(
					'body'    => array(
						'email'    => $email,
						'password' => $password,
						'id'       => $id,
						'version'  => $version,
						'domain'   => get_site_url(),
					),
					'timeout' => 10,
				)
			);

			if ( is_wp_error( $response ) ) {
				$remote_blocked_error = $response->get_error_message( 'http_request_not_executed' );
				if ( ! empty( $remote_blocked_error ) && ( 0 === strpos( $remote_blocked_error, 'User has blocked requests through HTTP' ) ) ) {
					$error_msg = sprintf(
						esc_html__( '<h5>Remote Requests are blocked, You can either disable that by changing this line </p><strong class="code">%1$s</strong> <p>to</p> <strong class="code">%2$s</strong> <p>in</p> <strong class="code">wp-config.php</strong> <p>until the update is finished or download the plugin from your <a href="%3$s" target="_blank" >account</a> and upload it</p>', 'gpls-core-plugins-pro' ),
						'define( \'WP_HTTP_BLOCK_EXTERNAL\', true );',
						'define( \'WP_HTTP_BLOCK_EXTERNAL\', false );',
						$this->my_account_link
					);
					wp_send_json_error(
						array(
							'message' => $error_msg,
						)
					);
				}
				wp_send_json_error(
					array(
						'message' => $response->get_error_message(),
					)
				);
			}

			if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
				$body = (array) json_decode( wp_remote_retrieve_body( $response ) );
				wp_send_json_success( $body );
			} else {
				wp_send_json_error( (array) json_decode( wp_remote_retrieve_body( $response ) ) );
			}
		} else {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'The link has expired, please refresh the page!', 'gpls-core-plugins-pro' ),
				)
			);
		}
	}

	/**
	 * Pypass unsafe reject flag for update request.
	 *
	 * @param boolean      $return
	 * @param array        $package
	 * @param \WP_Upgrader $upgrader
	 * @param array        $hook_extra
	 * @return void
	 */
	public function before_downloading_update( $return, $package, $upgrader, $hook_extra ) {
		if ( ! empty( $hook_extra['gpls_update_type'] ) ) {
			add_filter( 'http_request_args', array( $this, 'pypass_unsafe_reject_for_update' ), PHP_INT_MAX, 2 );
		}

		return $return;
	}

	/**
	 * Pypass unsafe urls reject to perform remote update.
	 *
	 * @param array  $parsed_args
	 * @param string $url
	 * @return array
	 */
	public function pypass_unsafe_reject_for_update( $parsed_args, $url ) {
		$parsed_args['reject_unsafe_urls'] = false;
		return $parsed_args;
	}

	/**
	 * Perform Update.
	 *
	 */
	public function perform_update() {
		if ( ! empty( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'gpls-core-plugins-pro-update-plugin' ) ) {
			if ( ! empty( $_POST['download_url'] ) && ! empty( $_POST['plugin'] ) ) {
				$download_url = wp_unslash( $_POST['download_url'] );
				$plugin       = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );

				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

				$skin     = new \WP_Ajax_Upgrader_Skin();
				$upgrader = new \Plugin_Upgrader( $skin );
				$upgrader->init();
				$upgrader->upgrade_strings();

				$res = $upgrader->fs_connect( array( WP_CONTENT_DIR, WP_PLUGIN_DIR ) );
				if ( ! $res ) {
					wp_send_json_error(
						esc_html__( 'Failed to connect to FileSystem', 'gpls-core-plugins-pro' )
					);
				}

				if ( is_plugin_active( $plugin ) ) {
					$upgrader->maintenance_mode( true );
				}

				$result = $upgrader->run(
					array(
						'package'                     => $download_url,
						'destination'                 => WP_PLUGIN_DIR,
						'clear_destination'           => false,
						'abort_if_destination_exists' => false,
						'clear_working'               => true,
						'hook_extra'                  => array(
							'plugin'           => $plugin,
							'type'             => 'plugin',
							'action'           => 'update',
							'gpls_update_type' => 'plugin',
						),
					)
				);

				// Remove pypass afterwards.
				remove_filter( 'http_request_args', array( $this, 'pypass_unsafe_reject_for_update' ), PHP_INT_MAX );

				$upgrader->maintenance_mode( false );

				if ( ! $result ) {
					wp_send_json_error( $upgrader->strings['download_failed'] );
				} elseif ( is_wp_error( $result ) ) {
					wp_send_json_error(
						( ! empty( $result->get_error_data( 'download_failed' ) ) ? $result->get_error_message( 'download_failed' ) : $result->get_error_message() )
					);
				}
				wp_send_json_success();
			}

			wp_send_json_error(
				esc_html__( 'An error occured, please try again.', 'gpls-core-plugins-pro' )
			);
		} else {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'The link has expired, please refresh the page!', 'gpls-core-plugins-pro' ),
				)
			);
		}
	}


	/**
	 * This will fire after finishing updating the plugin.
	 * Clear the new_version to indicate as up-to-date.
	 *
	 * @param \WP_Upgrader $wp_upgrader_obj WP_Upgrader Object.
	 * @param array        $hook_extra Hook Extra Array of the updater Run function.
	 * @return void
	 */
	public function update_plugin_info_with_new_version( $wp_upgrader_obj, $hook_extra ) {
		if ( ! empty( $hook_extra ) && is_array( $hook_extra ) && ! empty( $hook_extra['plugin'] ) && ! empty( $hook_extra['gpls_update_type'] ) && ( 'plugin' === $hook_extra['gpls_update_type'] ) ) {
			$main_options = get_option( $this->core->plugins_main_options );
			if ( $main_options ) {
				foreach ( $main_options['installed_plugins'] as $plugin_name => $plugin_data_arr ) {
					if ( ! empty( $plugin_data_arr['basename'] ) && ( $hook_extra['plugin'] === $plugin_data_arr['basename'] ) ) {
						$main_options['installed_plugins'][ $plugin_name ]['new_version'] = '';
						update_option( $this->plugins_main_options, $main_options, true );
						return;
					}
				}
			}
		}
	}
}
