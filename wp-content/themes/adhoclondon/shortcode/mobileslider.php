<?php 
// MOBILE - GET VALANTINES PRODUCTS FROM CATEGORY START



function wpcat_valMobilesbycategory($atts) {

    $wc_query_mobile = new WP_Query( 
        array('post_type' => 'product', 
              'post_status' => 'publish',
              'posts_per_page' => -1, 
              'tax_query' => array(
                  array(
                      'taxonomy'      => 'product_cat',
                      'field' => 'term_id', //This is optional, as it defaults to 'term_id'
                      'terms'         => $atts['id'], //Valentine category product ID
                      'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
                  )
              )
             ) 
    ); 


$string_mobile = '';
$string_mobile .= '<div class="product-slider">';
if ( $wc_query_mobile->have_posts() ) {

    $string_mobile .= '<ul class="category-slider mob-slider ">';
        $string_mobile .=  '<li class="owl-nav pro-item">';
            $string_mobile .= '<ul class="products elementor-grid columns-ulmt">';
                $count = 0;
                $j=1;
                while ( $wc_query_mobile->have_posts() ) {

                         $wc_query_mobile->the_post();           
               
                       // $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );           
                        $product = wc_get_product( get_the_ID());


                        $image_id  = $product->get_image_id();
                        $image_url = wp_get_attachment_image_url( $image_id, 'full' );


                            $string_mobile .= ' <li class="product type-product post-'.get_the_ID().' status-publish first instock product_cat-halloweenheadwear product_cat-wigs has-post-thumbnail taxable shipping-taxable purchasable product-type-'.$product->get_type().' ">';

                                    $string_mobile .= '<a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';

                                    $string_mobile .= ' <img width="350" height="500" src="'.$image_url.'" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" decoding="async" loading="lazy">';


                                    $string_mobile .= '<h2 class="woocommerce-loop-product__title">'. $product->get_name() . '</h2>';  
                                    $string_mobile .=  $product->get_price_html();

                                    $string_mobile .= '</a>';


                                    $string_mobile .= '<a href="'.get_the_permalink().'" data-quantity="1" class="button wp-element-button product_type_'.$product->get_type().' add_to_cart_button ajax_add_to_cart" data-product_id="'.get_the_ID().'" data-product_sku="'.$product->get_sku().'" aria-label="Add '. $product->get_name() . ' to your cart" rel="nofollow">LOVE TO VIEW</a>';


                            $string_mobile .= '</li>';
                           
                        }
                          wp_reset_postdata(); 
                        $string_mobile .= '</ul>';

                        $string_mobile .= '</li>';
                
                    $string_mobile .= '</ul>';


                } else {

                // no posts found
            }

        

$string_mobile .= '</div>'; 
return $string_mobile;

}
add_shortcode('MobilevalentinesProducts', 'wpcat_valMobilesbycategory');




// MOBILE - GET VALANTINES PRODUCTS FROM FIrst CATEGORY END

// Mobile Secound category shortcode
function wpcat_valMobilesbycategory_secound($atts) {

    $wc_query_mobile = new WP_Query( 
        array('post_type' => 'product', 
              'post_status' => 'publish',
              'posts_per_page' => -1, 
              'tax_query' => array(
                  array(
                      'taxonomy'      => 'product_cat',
                      'field' => 'term_id', //This is optional, as it defaults to 'term_id'
                      'terms'         => $atts['id'], //Valentine category product ID
                      'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
                  )
              )
             ) 
    ); 


$string_mobile = '';
$string_mobile .= '<div class="product-slider secound-mobile">';
if ( $wc_query_mobile->have_posts() ) {


    
    $string_mobile .= '<ul class="category-slider mob-slider ">';


    $string_mobile .=  '<li class="owl-nav pro-item">';
    $string_mobile .= '<ul class="products elementor-grid columns-ulmt">';

    $count = 0;
    $j=1;
    while ( $wc_query_mobile->have_posts() ) {

             $wc_query_mobile->the_post();           
   
           // $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );           
            $product = wc_get_product( get_the_ID());


            $image_id  = $product->get_image_id();
            $image_url = wp_get_attachment_image_url( $image_id, 'full' );


                $string_mobile .= ' <li class="product type-product post-'.get_the_ID().' status-publish first instock product_cat-halloweenheadwear product_cat-wigs has-post-thumbnail taxable shipping-taxable purchasable product-type-'.$product->get_type().' ">';

                        $string_mobile .= '<a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';

                        $string_mobile .= ' <img width="350" height="500" src="'.$image_url.'" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" decoding="async" loading="lazy">';


                        $string_mobile .= '<h2 class="woocommerce-loop-product__title">'. $product->get_name() . '</h2>';  
                        $string_mobile .=  $product->get_price_html();

                        $string_mobile .= '</a>';


                        $string_mobile .= '<a href="'.get_the_permalink().'" data-quantity="1" class="button wp-element-button product_type_'.$product->get_type().' add_to_cart_button ajax_add_to_cart" data-product_id="'.get_the_ID().'" data-product_sku="'.$product->get_sku().'" aria-label="Add '. $product->get_name() . ' to your cart" rel="nofollow">LOVE TO VIEW</a>';


                $string_mobile .= '</li>';
             
            }
              wp_reset_postdata(); 
            $string_mobile .= '</ul>';
            $string_mobile .= '</li>';
            $string_mobile .= '</ul>';


    } else {

    // no posts found
}


$string_mobile .= '</div>'; 
return $string_mobile;



}
add_shortcode('MobilevalentinesProductssecound', 'wpcat_valMobilesbycategory_secound');
?>