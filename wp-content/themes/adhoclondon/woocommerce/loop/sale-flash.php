<?php
/**
 * Product loop sale flash
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/sale-flash.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

?>
<?php if ( $product->is_on_sale() ) : ?>

	<?php echo apply_filters( 'woocommerce_sale_flash', '<div class="jet-woo-product-badge jet-woo-product-badge__sale">' . esc_html__( 'Sale!', 'woocommerce' ) . '</div>', $post, $product ); ?>

	<?php
endif;


if ( !is_product() ){
 if ( $product->get_stock_status() == 'outofstock' ) :
         echo '<p class="stock out-of-stock">Sorry, Out of stock</p>';
 endif;
}

$product_uniq_id = $product->get_id();


$meta_value = get_post_meta($product_uniq_id,'gpls-wcsamm-coming-soon-for-woocommerce-status',true);

if ( !is_product() ){
 if ( $meta_value == 'yes' ) :
        echo '<div class="coming-soon-badges">';
         echo '<span class="jet-listing-dynamic-field__content">yes</span>';
         echo '</div>';
 endif;
}

 

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
