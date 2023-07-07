<?php
namespace GPLSCorePro\GPLS_PLUGIN_WCSAMM;

defined( 'ABSPATH' ) || exit;

use GPLSCorePro\GPLS_PLUGIN_WCSAMM\ComingSoon;

/**
 * Coming Soon Compatibility Class.
 */
class ComingSoonComp extends ComingSoon {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @return void
	 */
	private function hooks() {
		// Blocksy Front Assets Comp.
		add_action( self::$plugin_info['name'] . '-frontend-inline-styles', array( $this, 'blocksy_inline_styles' ) );
	}

	/**
	 * Blocksy Comp Inline Styles.
	 *
	 * @return void
	 */
	public function blocksy_inline_styles() {
		if ( ! $this->is_theme_active( 'blocksy' ) ) {
			return;
		}
		?>
		.ct-image-container .<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-coming-soon-badge-img-wrapper' ); ?> {
			width: 100% !important;
			height: 100% !important;
		}
		<?php
	}
}
