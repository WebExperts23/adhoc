<?php
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Settings;

if ( $core->is_active( true ) ) :
?>
<div class="<?php echo esc_attr( $plugin_info['classes_prefix'] . '-countdown-settings-wrapper' ); ?>">
	<div class="container-fluid">
		<!-- Colors -->
		<div class="row border p-3 colors">
			<div class="col-md-12">
				<div class="settings-list row">
					<div class="loop-wrapper col-12 my-3 p-3 bg-white shadow-sm">
						<h4><?php esc_html_e( 'Loop Settings', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h4>
						<span><?php esc_html_e( 'Settings for countdown on loop pages like shop, categories, tags pages, etc...', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></span>
						<div class="container-fluid border mt-4">
							<!-- Loop coming soon status -->
							<div class="loop-coming-soon-text-wrapper settings-group my-4 py-4 col-md-12">
								<div class="row">
									<div class="col-md-3">
										<h6 class="mb-1"><?php esc_html_e( 'Coming Soon Countdown', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									</div>
									<div class="col-md-9">
										<input type="checkbox" class="regular-text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][loop][status]' ); ?>" <?php echo esc_attr( 'on' === $countdown_settings['loop']['status'] ? 'checked="checked"' : '' ); ?> >
										<small><?php esc_html_e( 'Show the coming soon countdown on loop pages', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></small>
									</div>
								</div>
							</div>
							<!-- Loop Coming Soon status -->
							<div class="loop-coming-soon-text-wrapper settings-group my-4 py-4 col-md-12">
								<div class="row">
									<div class="col-md-3">
										<h6><?php esc_html_e( 'Show Coming Soon Text', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									</div>
									<div class="col-md-9">
										<input type="checkbox" class="regular-text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][loop][text_status]' ); ?>" <?php echo esc_attr( 'on' === $countdown_settings['loop']['text_status'] ? 'checked="checked"' : '' ); ?> >
										<small><?php esc_html_e( 'Show the product coming soon text on loop pages', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></small>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="colors-wrapper col-12 my-2 p-3 bg-white shadow-sm">
						<h4><?php echo esc_html( 'Colors', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h4>
						<div class="row">
							<!-- Days -->
							<div class="border settings-group my-4 py-4 col-md-3">
								<h4><?php esc_html_e( 'Days', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h4>
								<div class="countgroup-wrapper subtitle">
									<!-- Title Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Title Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][days][title_color]' ); ?>" data-css="color" data-handle="rotor-group-heading" data-target="days" class="edit edit-days-title-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['days']['title_color'] ); ?>" >
											</label>
										</div>
									</div>
									<!-- Front Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Front Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][days][counter_front_color]' ); ?>" data-css="color" data-handle="rotor" data-target="days" class="edit edit-days-counter-front-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['days']['counter_front_color'] ); ?>" >
											</label>
										</div>
									</div>
									<!-- Back Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Back Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][days][counter_back_color]' ); ?>" data-css="background" data-handle="rotor" data-target="days" class="edit edit-days-counter-back-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['days']['counter_back_color'] ); ?>" >
											</label>
										</div>
									</div>
									<!-- Divider Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Divider Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][days][divider_color]' ); ?>" data-css="border-top-color" data-handle="rotor-divider" data-target="days" class="edit edit-days-divider-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['days']['divider_color'] ); ?>" >
											</label>
										</div>
									</div>
								</div>
							</div>
							<!-- Hours -->
							<div class="border settings-group my-4 py-4 col-md-3">
								<h4><?php esc_html_e( 'Hours', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h4>
								<div class="countgroup-wrapper subtitle">
									<!-- Title Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Title Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][hours][title_color]' ); ?>" data-css="color" data-handle="rotor-group-heading" data-target="hours" class="edit edit-hours-title-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['hours']['title_color'] ); ?>" >
											</label>
										</div>
									</div>
									<!-- Front Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Front Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][hours][counter_front_color]' ); ?>" data-css="color" data-handle="rotor" data-target="hours" class="edit edit-hours-counter-front-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['hours']['counter_front_color'] ); ?>" >
											</label>
										</div>
									</div>
									<!-- Back Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Back Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][hours][counter_back_color]' ); ?>" data-css="background" data-handle="rotor" data-target="hours" class="edit edit-hours-counter-back-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['hours']['counter_back_color'] ); ?>" >
											</label>
										</div>
									</div>
									<!-- Divider Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Divider Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][hours][divider_color]' ); ?>" data-css="border-top-color" data-handle="rotor-divider" data-target="hours" class="edit edit-hours-divider-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['hours']['divider_color'] ); ?>" >
											</label>
										</div>
									</div>
								</div>
							</div>
							<!-- Minutes -->
							<div class="border settings-group my-4 py-4 col-md-3">
								<h4><?php esc_html_e( 'Minutes', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h4>
								<div class="countgroup-wrapper subtitle">
									<!-- Title Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Title Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][minutes][title_color]' ); ?>" data-css="color" data-handle="rotor-group-heading" data-target="minutes" class="edit edit-minutes-title-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['minutes']['title_color'] ); ?>" >
											</label>
										</div>
									</div>
									<!-- Front Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Front Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][minutes][counter_front_color]' ); ?>" data-css="color" data-handle="rotor" data-target="minutes" class="edit edit-minutes-counter-front-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['minutes']['counter_front_color'] ); ?>" >
											</label>
										</div>
									</div>
									<!-- Back Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Back Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][minutes][counter_back_color]' ); ?>" data-css="background" data-handle="rotor" data-target="minutes" class="edit edit-minutes-counter-back-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['minutes']['counter_back_color'] ); ?>" >
											</label>
										</div>
									</div>
									<!-- Divider Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Divider Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][minutes][divider_color]' ); ?>" data-css="border-top-color" data-handle="rotor-divider" data-target="minutes" class="edit edit-minutes-divider-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['minutes']['divider_color'] ); ?>" >
											</label>
										</div>
									</div>
								</div>
							</div>
							<!-- Seconds -->
							<div class="border settings-group my-4 py-4 col-md-3">
								<h4><?php esc_html_e( 'Seconds', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h4>
								<div class="countgroup-wrapper subtitle">
									<!-- Title Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Title Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][seconds][title_color]' ); ?>" data-css="color" data-handle="rotor-group-heading" data-target="seconds" class="edit edit-seconds-title-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['seconds']['title_color'] ); ?>" >
											</label>
										</div>
									</div>
									<!-- Front Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Front Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][seconds][counter_front_color]' ); ?>" data-css="color" data-handle="rotor" data-target="seconds" class="edit edit-seconds-counter-front-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['seconds']['counter_front_color'] ); ?>" >
											</label>
										</div>
									</div>
									<!-- Back Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Back Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][seconds][counter_back_color]' ); ?>" data-css="background" data-handle="rotor" data-target="seconds" class="edit edit-seconds-counter-back-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['seconds']['counter_back_color'] ); ?>" >
											</label>
										</div>
									</div>
									<!-- Divider Color -->
									<div class="my-5 row countdown-rotor-color">
										<div class="col-4">
											<h6><?php esc_html_e( 'Divider Color', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
										</div>
										<div class="col-8">
											<label>
												<input type="text" name="<?php echo esc_attr( $plugin_info['name'] . '[countdown][seconds][divider_color]' ); ?>" data-css="border-top-color" data-handle="rotor-divider" data-target="seconds" class="edit edit-seconds-divider-color <?php echo esc_attr( $plugin_info['classes_prefix'] . '-color-input' ); ?>" value="<?php echo esc_attr( $countdown_settings['seconds']['divider_color'] ); ?>" >
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="<?php echo esc_attr( $plugin_info['name'] . '-countdown-settings-nonce' ); ?>" value="<?php echo esc_attr( wp_create_nonce( $plugin_info['name'] . '-countdown-settings-nonce' ) ); ?>">
				</div>
			</div>
			<div class="col-md-12 d-flex align-items-center justify-content-center bg-white shadow-sm">
				<div class="position-static preview-countdown-wrapper text-center">
					<!-- CountDown Here -->
					<?php Settings::get_countdown_preview( true ); ?>
				</div>
			</div>
		</div>
		<!--  -->
	</div>
</div>
<style type="text/css">
<?php Settings::get_countdown_styles( true ); ?>
</style>
<?php
$core->review_notice();
endif;
