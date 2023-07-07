<?php defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php

/**
 * Email Body.
 */
if ( $email_body ) {
	echo wp_kses_post( wpautop( wptexturize( $email_body ) ) );
}


do_action( 'woocommerce_email_footer', $email );
