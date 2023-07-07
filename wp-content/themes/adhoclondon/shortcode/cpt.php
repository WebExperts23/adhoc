<?php 
// Our custom post type function
function ADHOCK_CPT_TESTIMONIOL() {
  
    register_post_type( 'adhock_testimonial',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Testimonial Adhock' ),
                'singular_name' => __( 'Testimonial Adhock' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'adhock_testimonial'),
            'show_in_rest' => true,
            'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
  
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'ADHOCK_CPT_TESTIMONIOL' );
?>