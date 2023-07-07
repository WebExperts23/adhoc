<?php 

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
              $Cat_IDis = 4932;
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

    return $output;
}
add_shortcode( 'valentine_products', 'valentine_products_shortcode' );

?>