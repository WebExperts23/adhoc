<?php
if ( $core->is_active( true ) ) :
	?>
<div class="<?php echo esc_attr( $plugin_info['classes_prefix'] . '-badge-settings-wrapper' ); ?> bg-white shadow-sm">
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				<div class="settings-list">
					<div class="notice notice-warning notice-alt">
						<p><?php esc_html_e( 'If you experience an issue regarding the badge styles / position in frontend, It could be due to a conflict with your theme styles. Please', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?><a style="margin:0px 5px;" target="_blank" href="https://grandplugins.com/contact-us/"><?php esc_html_e( 'Contact Us', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></a><?php esc_html_e( 'with the theme name, screenshots or a test link in order to fix it', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></p>
					</div>
					<!-- Enable | Disable the badge -->
					<div class="my-5 row badge-status">
						<div class="col-3">
							<h6><?php esc_html_e( 'Badge Status', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
						</div>
						<div class="col-9">
							<label for="badge-status">
								<input type="checkbox" name="<?php echo esc_attr( $plugin_info['name'] . '[badge][badge_status]' ); ?>" class="edit edit-badge-status" <?php echo esc_attr( 'on' === $badge_settings['badge_status'] ? 'checked' : '' ); ?> >
								<?php esc_html_e( 'Enable the Badge', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?>
							</label>
						</div>
					</div>
					<!-- Badge Icon -->
					<div class="my-5 row badge-icon-select mb-3">
						<div class="col-3">
							<h6><?php esc_html_e( 'Badge Icon', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
						</div>
						<div class="col-9">
							<div class="row p-3 mb-4 border position-relative">
								<div class="col">
									<input type="file" id="custom-badge-icon-uploader" class="badge-icon-uploader" value="<?php echo esc_attr( 'Upload' ); ?>" >
								</div>
								<div class="col">
									<div class="badge-icons-toggler">
										<button class="badge-icons-toggle button-primary"><?php esc_html_e( 'Available Icons', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></button>
									</div>
								</div>
								<div class="loader d-none position-absolute w-100 h-100 left-0 top-0">
									<div class="loader-wrapper position-relative">
										<img class="loader-icon" src="<?php echo esc_url( admin_url( 'images/spinner-2x.gif' ) ); ?>" alt="spinner" >
									</div>
								</div>
								<div class="w-100 badge-upload-message message message-error mt-3 d-none">
									<p class="message-body"></p>
								</div>
							</div>

							<div class="badge-icons-wrapper row collapse">
								<?php foreach ( $available_badges as $badge ) : ?>
								<div class="badge-icon-element col border shadow-sm px-3 py-1">
									<input <?php echo esc_attr( $badge['name'] === $badge_settings['badge_icon'] ? 'checked' : '' ); ?> type="radio" value="<?php echo esc_attr( $badge['name'] ); ?>" name="<?php echo esc_attr( $plugin_info['name'] . '[badge][badge_icon]' ); ?>" class="edit edit-badge-icon-radio d-block mx-auto my-3">
									<img width="75" height="75" class="d-block mx-auto pb-2" src="<?php echo esc_url( $badge['url'] ); ?>" alt="coming-soon-badge-icon">
								</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					<!-- Badge Width - Height -->
					<div class="row my-5">
						<div class="col-3">
							<h6><?php esc_html_e( 'Dimension', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
						</div>
						<div class="col-9">
							<div class="row">
								<div class="col-md-6 my-2">
									<h6 class="col-12 mb-3"><?php esc_html_e( 'Width', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									<input type="number" value="<?php echo esc_attr( $badge_settings['badge_width'] ); ?>" name="<?php echo esc_attr( $plugin_info['name'] . '[badge][badge_width]' ); ?>" class="edit edit-badge-icon-width">&nbsp;<?php echo esc_html( 'px' ); ?>
								</div>
								<div class="col-md-6 my-2">
									<h6 class="col-12 mb-3"><?php esc_html_e( 'Height', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									<input type="number" value="<?php echo esc_attr( $badge_settings['badge_height'] ); ?>" name="<?php echo esc_attr( $plugin_info['name'] . '[badge][badge_height]' ); ?>" class="edit edit-badge-icon-height">&nbsp;<?php echo esc_html( 'px' ); ?>
								</div>
							</div>
						</div>
					</div>
					<!-- Badge Padding -->
					<div class="row my-5">
						<div class="col-3">
							<h6 class="col-12 mb-3"><?php esc_html_e( 'Padding', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
						</div>
						<div class="col-9">
							<div class="row">
								<div class="col-md-6 my-2">
									<h6 class="col-12 mb-3"><?php esc_html_e( 'Left', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									<input type="number" name="<?php echo esc_attr( $plugin_info['name'] . '[badge][badge_left]' ); ?>" value="<?php echo esc_attr( $badge_settings['badge_left'] ); ?>" min="0" max="100" class="edit edit-badge-padding-left">&nbsp;<?php echo esc_html( 'px' ); ?>
								</div>
								<div class="col-md-6 my-2">
									<h6 class="col-12 mb-3"><?php esc_html_e( 'Top', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
									<input type="number" name="<?php echo esc_attr( $plugin_info['name'] . '[badge][badge_top]' ); ?>" value="<?php echo esc_attr( $badge_settings['badge_top'] ); ?>" min="0" max="100" class="edit edit-badge-padding-top">&nbsp;<?php echo esc_html( 'px' ); ?>
								</div>
							</div>
						</div>
					</div>
					<!-- Badge Rotation -->
					<div class="row my-5">
						<div class="col-3">
							<h6 class="col-12 mb-3"><?php esc_html_e( 'Angle', 'gpls-wcsamm-coming-soon-for-woocommerce' ); ?></h6>
						</div>
						<div class="col-9">
							<input min="0" max="360" type="number" value="<?php echo esc_attr( $badge_settings['badge_angle'] ); ?>" class="text-center mx-auto edit edit-badge-angle" name="<?php echo esc_attr( $plugin_info['name'] . '[badge][badge_angle]' ); ?>" >
						</div>
					</div>
					<input type="hidden" name="<?php echo esc_attr( $plugin_info['name'] . '-badge-settings-nonce' ); ?>" value="<?php echo esc_attr( wp_create_nonce( $plugin_info['name'] . '-badge-settings-nonce' ) ); ?>">

				</div>
			</div>
			<div class="col d-flex align-items-center justify-content-center">
				<div class="preview-img-wrapper text-center">
					<!-- Product Gallery Here -->
					<div class="preview-img d-inline-block position-relative">
						<img class="border" src="<?php echo esc_url( $preview_url ); ?>" width="350" height="350" />
						<img src="#" class="hidden preview-img-badge position-absolute" >
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$core->review_notice();
endif;
