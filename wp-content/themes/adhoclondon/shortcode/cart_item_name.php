<?php 
add_filter( 'woocommerce_cart_item_name', 'ts_product_image_on_checkout', 10, 3 );
 
function ts_product_image_on_checkout( $name, $cart_item, $cart_item_key ) {
     
    // Return if not checkout page
    if ( ! is_checkout() ) {
        return $name;
    }
     
    // Get product object /
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
 
    // Get product thumbnail /
    $thumbnail = $_product->get_image();
 
    // Add wrapper to image and add some css /
    $image = '<div class="ts-product-image" style="width: 52px; height: 45px; display: inline-block; padding-right: 7px; vertical-align: middle;">'
                . $thumbnail .
            '</div>'; 
 
    // Prepend image to name and return it /
    return $image . $name;
}
?>