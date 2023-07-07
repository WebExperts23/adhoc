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
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array(  ),date("l jS \of F Y h:i:s A") );
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
add_filter( 'woocommerce_product_upsells_products_heading', 'bbloomer_translate_may_also_like' ); 
function bbloomer_translate_may_also_like() {
   return 'COMPLEMENTARY PRODUCTS';
}
//HOME PAGE REDIRECT TO HOME PAGE START
function adhock_london_shop_redirect() {
    if( is_shop() ){
        wp_redirect( home_url() ); // Assign custom internal page here
        exit();
    }
}
add_action( 'template_redirect', 'adhock_london_shop_redirect' );
//HOME PAGE REDIRECT TO HOME PAGE END



function custom_text_strings( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Out of stock' :
            $translated_text = 'Sorry, Out of stock';
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'custom_text_strings', 20, 3 );

//ADD CUSTOM POST TYPE FOR TESTIMONIAL START



include_once 'shortcode/cpt.php';

include_once 'shortcode/cart_item_name.php';
 include_once 'shortcode/valentine_products.php';

include_once 'shortcode/testimonialsslider.php';
include_once 'shortcode/homepageslider.php';
include_once 'shortcode/mobileslider.php'; 

function allow_api_requests( $permission_callback ) {
    global $wp_version;

    if ( version_compare( $wp_version, '4.7', '>=' ) ) {
        return true;
    } else {
        return $permission_callback;
    }
}
add_filter( 'rest_authentication_errors', 'allow_api_requests' );


//ADD ACF OPTION PAGE START APRIL 2023

if( function_exists('acf_add_options_page') ) {
    
    acf_add_options_page(array(
        'page_title'    => 'MOUSE HOVER EFFECT SETTING',
        'menu_title'    => 'Mouse Hover Settings',
        'menu_slug'     => 'mouse-hover-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
    
     
}


//ADD ACF OPTION PAGE END 2023

add_filter('rest_enabled', '_return_false');
add_filter('rest_jsonp_enabled', '_return_false');