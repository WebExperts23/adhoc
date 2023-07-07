.product-link {
	text-decoration: none;
}
.product-image {
	border-radius: 50%;
}

#header_wrapper h1{
	text-align: center;
}

.head-holder {
	text-align: center;
	font-size: 20px;
	line-height: 30px;
}

.product-title {
	font-size: 35px;
	line-height: 1.2em;
}

.img-holder {
	margin: 30px auto;
	text-align: center;
}

<?php
$base = get_option( 'woocommerce_email_base_color' );
$bg   = get_option( 'woocommerce_email_body_background_color' );
?>
.cta {
	color: <?php echo esc_attr( $bg ); ?>;
	background: <?php echo esc_attr( $base ); ?>;
	padding: 10px 20px;
	max-width: 200px;
	text-decoration: none;
	font-weight: bolder;
}

.cta-holder {
	margin: 30px auto;
	display: table;
	text-align: center;
	font-weight: bolder;
}
