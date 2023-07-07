<?php
namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM;

use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Modules\Services\Helpers;
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Modules\Updates\Update;
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Modules\License\License;

defined( 'ABSPATH' ) || exit();

/**
 * Core Class
 */
class Core {

	use Helpers;

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
	 * Core Path
	 *
	 * @var string
	 */
	public $core_path;

	/**
	 * Core URL
	 *
	 * @var string
	 */
	public $core_url;

	/**
	 * Core Assets PATH
	 *
	 * @var string
	 */
	public $core_assets_path;

	/**
	 * Core Assets URL
	 *
	 * @var string
	 */
	public $core_assets_url;

	/**
	 * Core Version.
	 *
	 * @var string
	 */
	private $version = '2.0';

	/**
	 * Constructor.
	 *
	 * @param array $plugin_info
	 */
	public function __construct( $plugin_info ) {
		$this->init( $plugin_info );
		$this->hooks();
		$this->init_modules();
	}

	/**
	 * Init constants and other variables.
	 *
	 * = Set the Plugin Update URL
	 *
	 * @return void
	 */
	public function init( $plugin_info ) {
		$this->plugin_info      = $plugin_info;
		$this->core_path        = plugin_dir_path( __FILE__ );
		$this->core_url         = plugin_dir_url( __FILE__ );
		$this->core_assets_path = $this->core_path . 'assets';
		$this->core_assets_url  = $this->core_url . 'assets';
	}

	/**
	 * Initialize Module Classes
	 *
	 * @return void
	 */
	public function init_modules() {
		Update::init( $this->plugin_info, $this );
		License::init( $this->plugin_info, $this );
	}

	/**
	 * Return License Instnace
	 *
	 * @return object
	 */
	public function is_active( $display_activate_msg = false, $inline = false ) {
		$is_active = License::init( $this->plugin_info, $this )->is_active();
		if ( ! $is_active && $display_activate_msg ) {
			License::init( $this->plugin_info, $this )->activate_msg( $inline );
		}
		return $is_active;
	}

	/**
	 * Core Hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'plugin_update_check' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 100, 1 );
	}

	/**
	 * Plugin Update Check
	 *
	 * @return void
	 */
	public function plugin_update_check() {
		if ( wp_doing_ajax() ) {
			return;
		}
		$main_options = get_option( $this->plugins_main_options );
		$current      = microtime( true );
		if ( is_array( $main_options ) && ! empty( $main_options['plugins_update_check'] ) && ( ( $main_options['plugins_update_check']['timestamp'] + 86400 ) < $current ) ) {
			apply_filters( 'gpls_core_plugins_update_check_action', 0 );
		}
	}

	/**
	 * Core Admin Scripts.
	 *
	 * @param string $hook_prefix
	 *
	 * @return void
	 */
	public function admin_scripts( $hook_suffix ) {
		global $pagenow;

		if ( ! wp_style_is( 'gpls-core-plugins-general-admin-head-styles-' . $this->version ) ) {
			wp_enqueue_style( 'gpls-core-plugins-general-admin-head-styles-' . $this->version, $this->core_assets_file( 'admin-head', 'css', 'css' ), array(), 'all' );
		}

		if ( is_admin() && 'toplevel_page_' . $this->plugins_main_menu_slug === $hook_suffix ) {

			if ( ! wp_style_is( 'gpls-core-plugins-general-pro-admin-bootstrap-css-lib' ) ) {
				wp_enqueue_style( 'gpls-core-plugins-general-pro-admin-bootstrap-css-lib', $this->core_assets_lib( 'bootstrap', 'css' ), array(), 'all' );
			}

			if ( ! wp_style_is( 'gpls-core-plugins-pro-general-admin-styles' ) ) {
				wp_enqueue_style( 'gpls-core-plugins-pro-general-admin-styles', $this->core_assets_file( 'style', 'css', 'css' ), array(), 'all' );
			}

			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}

			if ( ! wp_script_is( 'gpls-core-plugins-general-pro-admin-bootstrap-js-lib' ) ) {
				wp_enqueue_script( 'gpls-core-plugins-general-pro-admin-bootstrap-js-lib', $this->core_assets_lib( 'bootstrap.bundle', 'js' ), array( 'jquery' ), $this->plugin_info['version'], true );
			}
		}

		if ( 'plugins.php' === $pagenow ) {
			if ( ! wp_style_is( 'gpls-core-plugins-pro-general-admin-styles' ) ) {
				wp_enqueue_style( 'gpls-core-plugins-pro-general-admin-styles', $this->core_assets_file( 'style', 'css', 'css' ), array(), 'all' );
			}

			if ( ! wp_script_is( 'gpls-core-plugins-general-pro-admin-sweetalert-js-lib' ) ) {
				wp_enqueue_script( 'gpls-core-plugins-general-pro-admin-sweetalert-js-lib', $this->core_assets_lib( 'sweetalert2', 'js' ), array(), $this->plugin_info['version'], true );
			}
		}
	}

	/**
	 * Get Core assets file
	 *
	 * @param string $asset_file    Assets File Name
	 * @param string $type          Assets File Folder Type [ js / css /images / etc.. ]
	 * @param string $suffix        Assets File Type [ js / css / png /jpg / etc ... ]
	 * @param string $prefix        [ .min ]
	 * @return string
	 */
	public function core_assets_file( $asset_file, $type, $suffix, $prefix = 'min' ) {
		return $this->core_assets_url . '/dist/' . $type . '/' . $asset_file . ( ! empty( $prefix ) ? ( '.' . $prefix ) : '' ) . '.' . $suffix;
	}

	/**
	 * Get Core assets lib file
	 *
	 * @param string $asset_file    Assets File Name
	 * @param string $suffix        Assets File Type [ js / css / png /jpg / etc ... ]
	 * @param string $prefix        [ .min ]
	 * @return string
	 */
	public function core_assets_lib( $asset_file, $suffix, $prefix = 'min' ) {
		return $this->core_assets_url . '/libs/' . $asset_file . ( ! empty( $prefix ) ? ( '.' . $prefix ) : '' ) . '.' . $suffix;
	}

	/**
	 * Plugin Activation Hub function
	 *
	 * @return void
	 */
	public function plugin_activated() {
		// set the main options value.
		$main_options = get_option( $this->plugins_main_options );
		if ( ! $main_options ) {
			$main_options = array(
				'installed_plugins' => array(),
			);
		}
		if ( empty( $main_options['plugins_update_check'] ) ) {
			$main_options['plugins_update_check'] = array(
				'timestamp' => microtime( true ),
			);
		}
		$main_options['installed_plugins'][ $this->plugin_info['name'] ] = array(
			'public_name' => $this->plugin_info['public_name'],
			'basename'    => $this->plugin_info['basename'],
			'id'          => $this->plugin_info['id'],
			'name'        => $this->plugin_info['name'],
			'type'        => $this->plugin_info['type'],
			'status'      => 'active',
			'new_version' => '',
		);
		update_option( $this->plugins_main_options, $main_options, true );

		do_action( $this->plugin_info['name'] . '-core-activated', $this );
	}

	/**
	 * Plugin Deactivation Hub function
	 *
	 * @return void
	 */
	public function plugin_deactivated() {
		foreach ( $this->cron_events as $event ) {
			$timestamp = wp_next_scheduled( $event );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, $event );
			}
		}

		$main_options = get_option( $this->plugins_main_options );
		$main_options['installed_plugins'][ $this->plugin_info['name'] ]['status'] = 'inactive';
		update_option( $this->plugins_main_options, $main_options, true );

		do_action( $this->plugin_info['name'] . '-core-deactivated', $this );
	}

	/**
	 * Uninstall the plugin hook.
	 *
	 * @return void
	 */
	public function plugin_uninstalled() {
		if ( ! is_plugin_active( $this->plugin_info['text_domain'] . '/' . $this->plugin_info['name'] . '.php' ) ) {
			$main_options = get_option( $this->plugins_main_options );
			unset( $main_options['installed_plugins'][ $this->plugin_info['name'] ] );
			if ( empty( $main_options['installed_plugins'] ) ) {
				delete_option( $this->plugins_main_options );
			} else {
				update_option( $this->plugins_main_options, $main_options, true );
			}
		}

		delete_option( $this->plugin_info['name'] . '-license-data' );
		delete_option( $this->plugin_info['name'] . '-license-key' );

		do_action( $this->plugin_info['name'] . '-core-uninstalled', $this );
	}

	/**
	 * Default Footer Section
	 *
	 * @return void
	 */
	public function default_footer_section() {
		?>
		<style>
		#wpfooter {display: block !important;}
		.wrap.woocommerce {position: relative;}
		.gpls-contact {position: absolute; bottom: 0px; right: 20px; max-width: 350px; z-index: 1000;}
		.gpls-contact .link { color: #acde86!important; }
		.gpls-contact .text { background-color: #176875!important; }
		</style>
		<div class="gpls-contact">
		  <p class="p-3 bg-light text-center text text-white">in case you want to report a bug, submit a new feature or request a custom plugin, Please <a class="link" target="_blank" href="https://grandplugins.com/contact-us"> contact us </a></p>
		</div>
		<?php
	}

	/**
	 * Review Link.
	 *
	 * @param string $review_link
	 * @return void
	 */
	public function review_notice( $review_link = '', $is_dismissible = true ) {
		if ( empty( $review_link ) && empty( $this->plugin_info['review_link'] ) ) {
			return;
		}
		$review_link = ! empty( $review_link ) ? $review_link : $this->plugin_info['review_link'];
		?>
		<p class="notice notice-success p-4 <?php echo esc_attr( $is_dismissible ? 'is-dismissible' : '' ); ?>">
			<?php esc_html_e( 'We would love your feedback. leaving ' ); ?>
			<a class="text-decoration-none" href="<?php echo esc_url_raw( $review_link ); ?>" target="_blank">
				<u><?php esc_html_e( 'a review is much appreciated' ); ?></u>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
			</a>
			<?php esc_html_e( ':) Thanks!' ); ?>
		</p>
		<?php
	}

	/**
	 * New Keyword.
	 *
	 * @param string $title
	 * @param boolean $return
	 *
	 * @return string|void
	 */
	public function new_keyword( $title = 'new', $return = true ) {
		if ( $return ) {
			ob_start();
		}
		?>
		<span class="<?php echo esc_attr( $this->plugin_info['classes_general'] . '-new-keyword' ); ?> ms-1"><?php esc_html_e( 'New' ); ?></span>
		<?php
		if ( $return ) {
			return ob_get_clean();
		}
	}
}
