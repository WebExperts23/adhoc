<?php


// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array(  ) );
        wp_enqueue_style( 'font_css', 'https://use.typekit.net/amm0iio.css',  array(), '1.0.0', true );
        wp_enqueue_script( 'custom_js', trailingslashit( get_stylesheet_directory_uri() ).'js/custom.js',  array(), '1.0.0', true );
        //wp_enqueue_script( 'custom_map_js', 'http://maps.googleapis.com/maps/api/js?sensor=false',  array(), '1.0.0', true );

        wp_enqueue_style( 'slick-style', '//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.min.css');
        wp_enqueue_script( 'slick-js', '//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.js', array('jquery'), '1.0.0', true );
            }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION
// Change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_add_to_cart_button_text_single' ); 
function woocommerce_add_to_cart_button_text_single() {
    return __( 'LOVE TO BUY', 'woocommerce' ); 
}

// Change add to cart text on product archives page
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_add_to_cart_button_text_archives' );  
function woocommerce_add_to_cart_button_text_archives() {
    return __( 'LOVE TO VIEW', 'woocommerce' );
}

function cc_mime_types($mimes){
    // New allowed mime types.
  $mimes['svg'] = 'image/svg+xml';
  $mimes['svgz'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );


// Short Code for Store Page
function store_page_shortcode() {
    $output = '';

    $args = array(
        'taxonomy' => 'product_cat',
        'hide_if_empty' => 1
    );
    $categories = get_terms( $args );
    
    foreach ( $categories as $category ) {
        $category_name = $category->name;
        $category_id = $category->term_id;

        // Display the category name
        $output .= '<div class="title_link"><div class="title_woocommerce_custom"><h2>' . esc_html( $category_name ) . '</h2></div>';
        

        // Set up the query to retrieve the products in the category
        $product_args = array(
            'post_type' => 'product',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category_id
                )
            ),
            'posts_per_page' => 6
        );
        $product_query = new WP_Query( $product_args );

        // Start the product list
        $output .= '<ul class="categorylist">';

        // Loop through the products and display them
        if ( $product_query->have_posts() ) {
            while ( $product_query->have_posts() ) {
                $product_query->the_post();
                ob_start();
                wc_get_template_part( 'content', 'product' );
                $output .= ob_get_clean();
            }
            wp_reset_postdata();
        }

        // Close the product list
        $output .= '</ul>';
        
         $output .= '<div class="link_woocommerce_custom"><a href="'.get_term_link( $category_id, 'product_cat' ).'" class="button"> View All</a></div></div>';
        
    }

    return $output;
}
add_shortcode( 'store_page', 'store_page_shortcode' );


add_filter( 'body_class', function( $classes ) {
    $alert = isset( $_COOKIE['ppkcookie'] ) ? $_COOKIE['ppkcookie'] : 'light';
    return array_merge( $classes, array( $alert ) );
} );

add_filter('woocommerce_product_related_products_heading',function(){

    return 'Complementary Products';
 
 });

add_action( 'woocommerce_after_quantity_input_field', 'bbloomer_display_quantity_plus' );
  
function bbloomer_display_quantity_plus() {
   echo '<button type="button" class="plus">+</button>';
}
  
add_action( 'woocommerce_before_quantity_input_field', 'bbloomer_display_quantity_minus' );
  
function bbloomer_display_quantity_minus() {
   echo '<button type="button" class="minus">-</button>';
}
  

// -------------
// SVG Permision 

// function cc_mime_types($mimes) {
//     // New allowed mime types.
//   $mimes['svg'] = 'image/svg+xml';
//   $mimes['svgz'] = 'image/svg+xml';
//   return $mimes;
// }
// add_filter( 'upload_mimes', 'my_custom_mime_types' );

// -------------
// 2. Trigger update quantity script
  
add_action( 'wp_footer', 'bbloomer_add_cart_quantity_plus_minus' );
  
function bbloomer_add_cart_quantity_plus_minus() {
 
   if ( ! is_product() && ! is_cart() ) return;
    
   wc_enqueue_js( "   
           
      $(document).on( 'click', 'button.plus, button.minus', function() {
  
         var qty = $( this ).parent( '.quantity' ).find( '.qty' );
         var val = parseFloat(qty.val());
         var max = parseFloat(qty.attr( 'max' ));
         var min = parseFloat(qty.attr( 'min' ));
         var step = parseFloat(qty.attr( 'step' ));
 
         if ( $( this ).is( '.plus' ) ) {
            if ( max && ( max <= val ) ) {
               qty.val( max ).change();
            } else {
               qty.val( val + step ).change();
            }
         } else {
            if ( min && ( min >= val ) ) {
               qty.val( min ).change();
            } else if ( val > 1 ) {
               qty.val( val - step ).change();
            }
         }
 
      });
        
   " );

}

//GET VALANTINES PRODUCTS FROM CATEGORY START

function wpcat_ValtenDesktopsbycategory($atts) {

      $wc_query = new WP_Query( 
            array('post_type' => 'product', 
                  'post_status' => 'publish',
                  'posts_per_page' => -1, 
                  'tax_query'      =>   array(
					  array(
						  'taxonomy'      => 'product_cat',
						  'field' => 'term_id', //This is optional, as it defaults to 'term_id'
						  'terms'         => 4919, ////Valentine category product ID
						  'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
					  )
				  )
            ) 
        ); 
	$string = '';
	$string .= '<div class="product-slider">';

    if ( $wc_query->have_posts() ) {

       // $string .= '<div class="product-slider">';
        $string .= '<ul class="category-slider desk-slider left-slider-category">';
        $count = 0;
        $j=1;
        while ( $wc_query->have_posts() ) {

                 $wc_query->the_post();
               

                if( ($count % 2 == 0) ) {    


                $string .=  '<li class="owl-nav pro-item">';

                    $string .= '<ul class="products elementor-grid columns-2">';

                }

       
                        //$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );           
                        $product = wc_get_product( get_the_ID());

                        
                        $image_id  = $product->get_image_id();
                        $image_url = wp_get_attachment_image_url( $image_id, 'full' );



                            $string .= ' <li class="product type-product post-'.get_the_ID().' status-publish first instock product_cat has-post-thumbnail taxable shipping-taxable purchasable product-type-'.$product->get_type().' ">';

                                    $string .= '<a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';

                                    $string .= ' <img width="350" height="500" src="'.$image_url.'" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" decoding="async" loading="lazy">';


                                    $string .= '<h2 class="woocommerce-loop-product__title">'. $product->get_name() . '</h2>';  
                                    $string .=  $product->get_price_html();

                                    $string .= '</a>';


                                    $string .= '<a href=" '.get_permalink( get_the_ID() ).' " data-quantity="1" class="button wp-element-button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.get_the_ID().'" data-product_sku="'.$product->get_sku().'" aria-label="Add '. $product->get_name() . ' to your cart" rel="nofollow">LOVE TO VIEW</a>';


                            $string .= '</li>';



                if( ($j % 2 == 0) ) {    
                            
                        $string .= '</ul>';

                    $string .= '</li>';

                   // $count = 1;

                }

                $j++;
                $count++;
        }

        $string .= '</ul>';
    } else {}


    $wc_query_cat = new WP_Query( 
            array('post_type' => 'product', 
                  'post_status' => 'publish',
                  'posts_per_page' => -1, 
                  'tax_query'      =>   array(
                                            array(
                                                'taxonomy'      => 'product_cat',
                                                'field' => 'term_id', //This is optional, as it defaults to 'term_id'
                                                'terms'         => 4920, ////Valentine category product ID
                                                'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
                                            )
                                       )
            ) 
        );



    //$string .= '<div class="product-slider">';
   

    if ( $wc_query_cat->have_posts() ) {
         $string .= '<ul class="category-slider desk-slider right-slider-category">';
        $count = 0;
        $j=1;
        while ( $wc_query_cat->have_posts() ) {

             $wc_query_cat->the_post();
           

            if( ($count % 2 == 0) ) {    


                $string .=  '<li class="owl-nav pro-item">';

                $string .= '<ul class="products elementor-grid columns-2">';

            }

   
                    //$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );           
                    $product = wc_get_product( get_the_ID());

                    
                    $image_id  = $product->get_image_id();
                    $image_url = wp_get_attachment_image_url( $image_id, 'full' );



                    $string .= ' <li class="product type-product post-'.get_the_ID().' status-publish first instock product_cat has-post-thumbnail taxable shipping-taxable purchasable product-type-'.$product->get_type().' " data-cat"valentine-gift">';

                            $string .= '<a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';

                            $string .= ' <img width="350" height="500" src="'.$image_url.'" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" decoding="async" loading="lazy">';


                            $string .= '<h2 class="woocommerce-loop-product__title">'. $product->get_name() . '</h2>';  
                            $string .=  $product->get_price_html();

                            $string .= '</a>';


                            $string .= '<a href=" '.get_permalink( get_the_ID() ).'" data-quantity="1" class="button wp-element-button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.get_the_ID().'" data-product_sku="'.$product->get_sku().'" aria-label="Add '. $product->get_name() . ' to your cart" rel="nofollow">LOVE TO VIEW</a>';


                    $string .= '</li>';



            if( ($j % 2 == 0) ) {    
                        
                    $string .= '</ul>';

                $string .= '</li>';

               // $count = 1;

            }

            $j++;
            $count++;
            }

        $string .= '</ul>';
    } else {}


$string .= '</div>';

return $string;


}
add_shortcode('valentinesProducts', 'wpcat_ValtenDesktopsbycategory');



//GET VALANTINES PRODUCTS FROM CATEGORY END

// MOBILE - GET VALANTINES PRODUCTS FROM CATEGORY START



function wpcat_valMobilesbycategory() {

	$wc_query_mobile = new WP_Query( 
		array('post_type' => 'product', 
			  'post_status' => 'publish',
			  'posts_per_page' => -1, 
			  'tax_query' => array(
				  array(
					  'taxonomy'      => 'product_cat',
					  'field' => 'term_id', //This is optional, as it defaults to 'term_id'
					  'terms'         => 4919, //Valentine category product ID
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


                                    $string_mobile .= '<a href="'.get_permalink( get_the_ID() ).'" data-quantity="1" class="button wp-element-button product_type_'.$product->get_type().' add_to_cart_button ajax_add_to_cart" data-product_id="'.get_the_ID().'" data-product_sku="'.$product->get_sku().'" aria-label="Add '. $product->get_name() . ' to your cart" rel="nofollow">LOVE TO VIEW</a>';


                            $string_mobile .= '</li>';

                        }


                } else {

                // no posts found
            }

            $string_mobile .= '</ul>';

        $string_mobile .= '</li>';

    $string_mobile .= '</ul>';

$string_mobile .= '</div>'; 
return $string_mobile;

}
add_shortcode('MobilevalentinesProducts', 'wpcat_valMobilesbycategory');




// MOBILE - GET VALANTINES PRODUCTS FROM FIrst CATEGORY END

// Mobile Secound category shortcode
function wpcat_valMobilesbycategory_secound() {

    $wc_query_mobile = new WP_Query( 
        array('post_type' => 'product', 
              'post_status' => 'publish',
              'posts_per_page' => -1, 
              'tax_query' => array(
                  array(
                      'taxonomy'      => 'product_cat',
                      'field' => 'term_id', //This is optional, as it defaults to 'term_id'
                      'terms'         => 4920, //Valentine category product ID
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


                        $string_mobile .= '<a href="'.get_permalink( get_the_ID() ).'" data-quantity="1" class="button wp-element-button product_type_'.$product->get_type().' add_to_cart_button ajax_add_to_cart" data-product_id="'.get_the_ID().'" data-product_sku="'.$product->get_sku().'" aria-label="Add '. $product->get_name() . ' to your cart" rel="nofollow">LOVE TO VIEW</a>';


                $string_mobile .= '</li>';

            }


    } else {

    // no posts found
}

$string_mobile .= '</ul>';

$string_mobile .= '</li>';

$string_mobile .= '</ul>';
$string_mobile .= '</div>'; 
return $string_mobile;



}
add_shortcode('MobilevalentinesProductssecound', 'wpcat_valMobilesbycategory_secound');


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


//SHORTCODE FOR VALENTINE PRODUCTS

// Short Code for Store Page
function valentine_products_shortcode($atts) {

    //GET CATEGORY NAME FROM SHORTCODE
    $Cat_nameis = $atts['categoryis'];

    //GET CATEGORY ARRAY USING CATEGORY NAME
    $term_ids = get_term_by('name', $Cat_nameis , 'product_cat');

    //EXTRACT ID FROM CATEGORY ARRAY
    $cat_ID = $term_ids->term_id;

    //CONDITION IF ID IS EMPTY THEN DEFAULT ID IS ALLOCATED
    if( !empty( $cat_ID ) && ( $cat_ID != 0 ) ):
              $Cat_IDis = $cat_ID;
    else:
              $Cat_IDis = 4919;
    endif;

    //GET PARENT CATEGORY NAME - USE IN LAST LOOP
    $Parent_cat_Name =  get_term( $Cat_IDis, 'product_cat' );

    $output = '';
    
    //GET ALL CHILDREN CATEGORY FOR THIS CATEGORY
    $val_categories = get_terms('product_cat',array('child_of' => $Cat_IDis)); 

    
    //LOOP THE CATEGORY 
    foreach ( $val_categories as $category ) {
        $category_name = $category->name;
        $category_id = $category->term_id;

        // Display the category name
        $output .= '<div class="title_link Shop-pro"><div class="title_woocommerce_custom"><h2>' . esc_html( $category_name ) . '</h2></div>';
        

        // Set up the query to retrieve the products in the category
        $product_args = array(
            'post_type' => 'product',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category_id
                )
            ),
            'posts_per_page' => 6
        );
        $product_query = new WP_Query( $product_args );

        // Start the product list
        $output .= '<ul class="categorylist">';

        // Loop through the products and display them
        if ( $product_query->have_posts() ) {
            while ( $product_query->have_posts() ) {
                $product_query->the_post();
                ob_start();
                wc_get_template_part( 'content', 'product' );
                $output .= ob_get_clean();
            }
            wp_reset_postdata();
        }

        // Close the product list
        $output .= '</ul>';
        
         $output .= '<div class="link_woocommerce_custom"><a href="'.get_term_link( $category_id, 'product_cat' ).'" class="button"> View All</a></div></div>';
        
    } //END OF FOREACH LOOP



    //GET THE PARENT CATEGORY DATA START
    
           /* $output .= '<div class="title_link Shop-pro"><div class="title_woocommerce_custom"><h2> '. $Parent_cat_Name->name .' </h2></div>';
            update_post_meta ( $Cat_IDis, '_cate_valprod', $Cat_IDis );

            // Set up the query to retrieve the products in the category
               $product_args = array(
                'post_type' => 'product',
                'tax_query' => array(
                array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $Cat_IDis
                )
                ),
                'posts_per_page' => 6
                );
                $product_query_prnt = new WP_Query( $product_args );

            // Start the product list
            $output .= '<ul class="categorylist">';

            // Loop through the products and display them
                if ( $product_query_prnt->have_posts() ) {
                while ( $product_query_prnt->have_posts() ) {
                $product_query_prnt->the_post();
                ob_start();
                wc_get_template_part( 'content', 'product' );
                $output .= ob_get_clean();
                }
                wp_reset_postdata();
                }

            // Close the product list
                $output .= '</ul>';

                $output .= '<div class="link_woocommerce_custom"><a href="'.get_term_link( $Cat_IDis, 'product_cat' ).'" class="button"> View All</a></div></div>';*/

    //GET THE PARENT CATEGORY DATA END

    return $output;
}
add_shortcode( 'valentine_products', 'valentine_products_shortcode' );


