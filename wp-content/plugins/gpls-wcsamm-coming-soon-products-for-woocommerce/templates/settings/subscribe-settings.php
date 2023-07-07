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
							<!-- Subscription Form Title -->
							<div class="loop-coming-soon-text-wrapper settings-group my-4 py-4 col-md-12">
								<div class="row">
									<div class="col-md-3">
										<h6><?php esc_html_e( 'Subscription Form Title', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									</div>
									<div class="col-md-9">
										<textarea id="<?php echo esc_attr( $plugin_info['classes_prefix'] . '-subscribe-title' ); ?>" class="<?php echo esc_attr( $plugin_info['classes_prefix'] . '-subscribe-title' ); ?>" name="<?php echo esc_attr( $plugin_info['name'] . '[subscribe][subscribe_title]' ); ?>" ><?php echo wp_kses_post( $subscribe_settings['subscribe_title'] ); ?></textarea>
									</div>
								</div>
							</div>
							<!-- Subscription Form Placeholder -->
							<div class="loop-coming-soon-text-wrapper settings-group my-4 py-4 col-md-12">
								<div class="row">
									<div class="col-md-3">
										<h6><?php esc_html_e( 'Subscription Form Placeholder', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									</div>
									<div class="col-md-9">
										<input type="text" class="regular-text" name="<?php echo esc_attr( $plugin_info['name'] . '[subscribe][subscribe_placeholder]' ); ?>" value="<?php echo esc_attr( $subscribe_settings['subscribe_placeholder'] ); ?>" >
									</div>
								</div>
							</div>
							<!-- After Submit Text -->
							<div class="loop-coming-soon-text-wrapper settings-group my-4 py-4 col-md-12">
								<div class="row">
									<div class="col-md-3">
										<h6><?php esc_html_e( 'After Submit Text', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										<span><?php esc_html_e( 'This text will appear after the Subscription form is submitted', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></span>
									</div>
									<div class="col-md-9">
										<textarea id="<?php echo esc_attr( $plugin_info['classes_prefix'] . '-post-subscribe-text' ); ?>" class="<?php echo esc_attr( $plugin_info['classes_prefix'] . '-post-subscribe-text' ); ?>" name="<?php echo esc_attr( $plugin_info['name'] . '[subscribe][post_subscribe_title]' ); ?>" ><?php echo wp_kses_post( $subscribe_settings['post_subscribe_title'] ); ?></textarea>
									</div>
								</div>
							</div>
							<!-- Submit Button Text -->
							<div class="loop-coming-soon-text-wrapper settings-group my-4 py-4 col-md-12">
								<div class="row">
									<div class="col-md-3">
										<h6><?php esc_html_e( 'Submit Button Text', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									</div>
									<div class="col-md-9">
										<input type="text" class="regular-text" name="<?php echo esc_attr( $plugin_info['name'] . '[subscribe][submit_button_text]' ); ?>" value="<?php echo esc_attr( $subscribe_settings['submit_button_text'] ); ?>" >
									</div>
								</div>
							</div>
							<!-- Submit Button Background -->
							<div class="loop-coming-soon-text-wrapper settings-group my-4 py-4 col-md-12">
								<div class="row">
									<div class="col-md-3">
										<h6><?php esc_html_e( 'Submit Button Background', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									</div>
									<div class="col-md-9">
										<input type="text" class="regular-text <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-picker' ); ?>" name="<?php echo esc_attr( $plugin_info['name'] . '[subscribe][submit_button_bg]' ); ?>" value="<?php echo esc_attr( $subscribe_settings['submit_button_bg'] ); ?>" >
									</div>
								</div>
							</div>
							<!-- Submit Button Color -->
							<div class="loop-coming-soon-text-wrapper settings-group my-4 py-4 col-md-12">
								<div class="row">
									<div class="col-md-3">
										<h6><?php esc_html_e( 'Submit Button Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									</div>
									<div class="col-md-9">
										<input type="text" class="regular-text <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-picker' ); ?>" name="<?php echo esc_attr( $plugin_info['name'] . '[subscribe][submit_button_color]' ); ?>" value="<?php echo esc_attr( $subscribe_settings['submit_button_color'] ); ?>" >
									</div>
								</div>
							</div>
							<!-- Consent Text -->
							<div class="loop-coming-soon-text-wrapper settings-group my-4 py-4 col-md-12">
								<div class="row">
									<div class="col-md-3">
										<h6><?php esc_html_e( 'Consent Text', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									</div>
									<div class="col-md-9">
										<textarea id="<?php echo esc_attr( $plugin_info['classes_prefix'] . '-consent-text' ); ?>" class="<?php echo esc_attr( $plugin_info['classes_prefix'] . '-consent-text' ); ?>" name="<?php echo esc_attr( $plugin_info['name'] . '[subscribe][consent_text]' ); ?>" cols="100" rows="10"><?php echo wp_kses_post( $subscribe_settings['consent_text'] ); ?></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="<?php echo esc_attr( $plugin_info['name'] . '-current-version' ); ?>" value="1.1.5" >
					<input type="hidden" name="<?php echo esc_attr( $plugin_info['name'] . '-subscribe-settings-nonce' ); ?>" value="<?php echo esc_attr( wp_create_nonce( $plugin_info['name'] . '-subscribe-settings-nonce' ) ); ?>">
				</div>
			</div>
		</div>
	</div>
</div>
	<?php
	$core->review_notice();
endif;
