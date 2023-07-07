<?php 
function wpcat_ValtenDesktopsbycategory_left($atts){
 
  
 // echo $atts['id'];
    $wc_query = new WP_Query( 
          array('post_type' => 'product', 
                'post_status' => 'publish',
                'posts_per_page' => -1, 
                'tax_query'      =>   array(
                    array(
                        'taxonomy'      => 'product_cat',
                        'field'         => 'term_id', //This is optional, as it defaults to 'term_id'
                        'terms'         => $atts['id'], ////Valentine category product ID
                        'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
                    )
                )
          ) 
      ); 
  
  $string_left = '';

  $string_left .= '<div class="product-slider">';
           $string_left .= '<ul class="category-slider desk-slider left-slider-category">';
              if ( $wc_query->have_posts() ) {   
                  $count = 0;
                  $j=1;
                  while ( $wc_query->have_posts() ) {
                           $wc_query->the_post();
                             
                          if( ($count % 2 == 0) ) {  
                            $string_left .=  '<li class="owl-nav pro-item">';  
                              $string_left .= '<ul class="products elementor-grid columns-2">';
                          }
                                  $product = wc_get_product( get_the_ID());
                                  $image_id  = $product->get_image_id();
                                  $image_url = wp_get_attachment_image_url( $image_id, 'full' );
                                    $string_left .= '<li class="product type-product post-'.get_the_ID().' status-publish first instock product_cat has-post-thumbnail taxable shipping-taxable purchasable product-type-'.$product->get_type().' ">';
                                    $string_left .= '<a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
                                    $string_left .= '<img width="350" height="500" src="'.$image_url.'" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" decoding="async" loading="lazy"/>';
                                    $string_left .= '<h2 class="woocommerce-loop-product__title">'. $product->get_name() . '</h2>';  
                                    $string_left .=  '<span>'.$product->get_price_html().'</span>';
                                    $string_left .= '</a>';
                                    $string_left .= '<a href="'.get_the_permalink().'" data-quantity="1" class="button wp-element-button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.get_the_ID().'" data-product_sku="'.$product->get_sku().'" aria-label="Add '. $product->get_name() . ' to your cart" rel="nofollow">LOVE TO VIEW</a>';
                                    $string_left .= '</li>';
                          if( ($j % 2 == 0) ) {    
                              $string_left .= '</ul>';
                              $string_left .= '</li>';
                          }
                    
                        $count++;
                        $j++;
                  }  
                wp_reset_postdata();
              }
           $string_left .= '</ul>';
           $string_left .= '</div>';
return $string_left;
}
add_shortcode('valentinesProducts_left', 'wpcat_ValtenDesktopsbycategory_left');

function wpcat_ValtenDesktopsbycategory_right($atts){

    $wc_query_cat = new WP_Query( 
        array('post_type' => 'product', 
              'post_status' => 'publish',
              'posts_per_page' => -1, 
              'tax_query'      =>   array(
                        array(
                            'taxonomy'      => 'product_cat',
                            'field'         => 'term_id', //This is optional, as it defaults to 'term_id'
                            'terms'         => $atts['id'], ////Valentine category product ID
                            'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
                        )
                   )
        ) 
    );
$string_right = '';
$string_right .= '<div class="product-slider">';
$string_right .= '<ul class="category-slider desk-slider right-slider-category">';
  if ( $wc_query_cat->have_posts() ) {   
      $counter = 0;
      $jj=1;
      while ( $wc_query_cat->have_posts() ) {
           $wc_query_cat->the_post();
         
              if( ($counter % 2 == 0) ) {    
                $string_right .=  '<li class="owl-nav pro-item">';
                  $string_right .= '<ul class="products elementor-grid columns-2">';
              }
                  $product = wc_get_product( get_the_ID());
                  $image_id  = $product->get_image_id();
                  $image_url = wp_get_attachment_image_url( $image_id, 'full' );
                    $string_right .= ' <li class="product type-product post-'.get_the_ID().' status-publish first instock product_cat has-post-thumbnail taxable shipping-taxable purchasable product-type-'.$product->get_type().' " data-cat"valentine-gift">';
                    $string_right .= '<a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
                    $string_right .= ' <img width="350" height="500" src="'.$image_url.'" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" decoding="async" loading="lazy">';
                    $string_right .= '<h2 class="woocommerce-loop-product__title">'. $product->get_name() . '</h2>';  
                    $string_right .=  '<span>'.$product->get_price_html().'</span>';
                    $string_right .= '</a>';
                    $string_right .= '<a href="'.get_the_permalink().'" data-quantity="1" class="button wp-element-button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.get_the_ID().'" data-product_sku="'.$product->get_sku().'" aria-label="Add '. $product->get_name() . ' to your cart" rel="nofollow">LOVE TO VIEW</a>';
                    $string_right .= '</li>';
          if( ($jj % 2 == 0) ) {    
                  $string_right .= '</ul>';
                  $string_right .= '</li>';
          }
        
          $counter++;
          $jj++;
      }  
       wp_reset_postdata(); 
  }
  $string_right .= '</ul>';
  $string_right .= '</div>';
  return $string_right;
}
add_shortcode('valentinesProducts_right', 'wpcat_ValtenDesktopsbycategory_right');

