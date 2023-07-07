<?php
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Settings;

if ( $core->is_active( true ) ) :
?>
<div class="<?php echo esc_attr( $plugin_info['classes_prefix'] . '-countdown-settings-wrapper' ); ?>">
	<div class="container-fluid">
		<!-- Colors -->
		<div class="row border p-3 colors">
			<div class="col-12">
				<div class="settings-list row">
					<div class="loop-wrapper col-12 my-3 p-3 bg-white shadow-sm">
						<div class="container-fluid border">
							<!-- Coming Soon Text -->
							<div class="loop-coming-soon-text-wrapper settings-group my-4 py-4 col-md-12">
								<div class="row">
									<div class="col-md-3">
										<h6><?php esc_html_e( 'General Coming Soon Text', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									</div>
									<div class="col-md-9">
										<textarea id="<?php echo esc_attr( $plugin_info['name'] . '-coming-soon-text' ); ?>" type="text" class="<?php echo esc_attr( $plugin_info['name'] . '-texteditor' ); ?>" name="<?php echo esc_attr( $plugin_info['name'] . '[general][coming_soon_text]' ); ?>" ><?php printf( esc_html__( '%s', 'gpls-wcsamm-coming-soon-for-woocommerce' ), $general_settings['coming_soon_text'] ); ?></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="<?php echo esc_attr( $plugin_info['name'] . '-general-settings-nonce' ); ?>" value="<?php echo esc_attr( wp_create_nonce( $plugin_info['name'] . '-general-settings-nonce' ) ); ?>">
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$core->review_notice();
$core->default_footer_section();
endif;
