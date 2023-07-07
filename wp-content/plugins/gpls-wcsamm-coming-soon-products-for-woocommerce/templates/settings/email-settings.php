<?php
use GPLSCorePro\GPLS_PLUGIN_WCSAMM\Settings;
// TODO: To be used later.
?>
<div class="<?php echo esc_attr( $plugin_info['classes_prefix'] . '-countdown-settings-wrapper' ); ?>">
	<div class="container-fluid">
		<!-- Colors -->
		<div class="row border p-3 colors">
			<div class="col-12">
				<div class="settings-list row">
					<div class="col-12 my-3 p-3">
						<div class="container-fluid border">
							<!-- Email Template -->
							<div class="email-template-select-wrapper settings-group my-4 py-4 col-md-12">
								<div class="row">
									<div class="col-md-3">
										<h6><?php esc_html_e( 'Email Template', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									</div>
									<div class="col-md-9">
										<select type="select" name="<?php echo esc_attr( $plugin_info['name'] . '[email][template]' ); ?>">
											<?php
											if ( is_array( $email_templates ) ) :
												foreach ( $email_templates as $template_name => $template_arr ) :
													?>
												<option <?php echo esc_attr( $template_name === $email_settings['template'] ? 'selected' : '' ); ?> value="<?php echo esc_attr( $template_name ); ?>"><?php echo esc_html( $template_arr['label'] ); ?></option>
													<?php
												endforeach;
											endif;
											?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="<?php echo esc_attr( $plugin_info['name'] . '-email-settings-nonce' ); ?>" value="<?php echo esc_attr( wp_create_nonce( $plugin_info['name'] . '-email-settings-nonce' ) ); ?>">
				</div>
			</div>
		</div>
	</div>
</div>
