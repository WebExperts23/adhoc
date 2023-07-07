<?php 
function ADHOCKLONDON_FRONT_TESTIMONIOL() {

    $the_query = new WP_Query( array('posts_per_page' => -1,'post_type' => 'adhock_testimonial' ) ); 
    
    if ( $the_query->have_posts() ) {
    
    
        $Testimoniol_adhock .= '<div class="testimonial">';
        $Testimoniol_adhock .= '<div class="container">';
        $Testimoniol_adhock .= ' <div class="testimonial__inner">';
        $Testimoniol_adhock .= '<div class="testimonial-slider">';
    
            
            while ( $the_query->have_posts() ) {
    
    
                     $the_query->the_post();
    
                    $testimonial_Image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
                    $testimonial_Title = $the_query->post_title;
                    $testimonial_Content = apply_filters('the_content', $the_query->post_content);
    
                        
                        $Testimoniol_adhock .= '<div class="testimonial-slide">';
                        $Testimoniol_adhock .= '<div class="testimonial_box">';
    
    
    
                                $Testimoniol_adhock .= '<div class="testimonial_box-inner">';
                                $Testimoniol_adhock .= '<div class="testimonial_box-top">';
    
                                        $Testimoniol_adhock .= '<div class="testimonial_box-img">';
                                        $Testimoniol_adhock .= '<img src="'.$testimonial_Image[0].'" alt="profile">';
                                        $Testimoniol_adhock .= '</div>';
    
                                        $Testimoniol_adhock .= '<div class="testimonial_box-text">';
                                        $Testimoniol_adhock .= '<p>'.get_the_content().'</p>';
                                        $Testimoniol_adhock .= '</div>';
    
    
    
                                $Testimoniol_adhock .= '</div>';
                                $Testimoniol_adhock .= '</div>';
    
    
                        $Testimoniol_adhock .= '</div>';
                        $Testimoniol_adhock .= '</div>';
                        
                    
                    }

                        wp_reset_postdata();
    
    
    
        $Testimoniol_adhock .= '</div>';
        $Testimoniol_adhock .= '</div>';
        $Testimoniol_adhock .= '</div>';
        $Testimoniol_adhock .= '</div>';
    
    
        } else {
    
        // no posts found
    }
    
    
    return $Testimoniol_adhock;
    

    
    }
    add_shortcode('adhock_testimoniol', 'ADHOCKLONDON_FRONT_TESTIMONIOL');
?>