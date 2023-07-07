

/* HOMEPAGE JQUERY FOR SWAP IMAGES */


//INNER SMALL IMAGES PARENT CLASS  --- shop-valantine-box
// BIG IMAGE PARENT CLASS ---  bannerleftimage

// custom-price-value
// add-to-cart-single

        
jQuery(document).ready(function(){


  //ADD CLASS ON SCROLL SINGLE PRODUCT PAGE
  jQuery('.social_share .elementor-widget-container').addClass('notifysticky');
  jQuery('body.single-product').addClass('notifysticky');

  //ADD/REMOVE CLASS ON SCROLL SINGLE PRODUCT PAGE
  jQuery(window).scroll(function()
  {
    
    if (jQuery(this).scrollTop() > 1025) {       
       jQuery('.social_share .elementor-widget-container').removeClass('notifysticky');
       jQuery('body.single-product').removeClass('notifysticky');

    } else {
       jQuery('.social_share .elementor-widget-container').addClass('notifysticky');
       jQuery('body.single-product').addClass('notifysticky');

    }

  });


//HOME - GET H1 TEXT AND STORE TO VARAIBLE
var MainText = jQuery('#change-text h1').text().replace(/\n|\r/g, "");  //Main Image Text

//HOME - GET 1ST SUB IMAGE BUTTON TEXT
var SubText1 = jQuery('#homebtn2 .elementor-button-text').text().replace(/\n|\r/g, "");  //second text

//HOME - GET 2ND SUB IMAGE BUTTON TEXT
var SubText2 = jQuery('#homebtn3 .elementor-button-text').text().replace(/\n|\r/g, ""); //third text


//ASSIGN TEXT TO THE ATTRIBUTE FOR FUTURE USE
jQuery('#first-image img').attr('data-textvalue',MainText);
jQuery('#second-image img').attr('data-textvalue',SubText1);
jQuery('#third-image img').attr('data-textvalue',SubText2);


//HOME - GET 1ST URL FROM SUB IMAGE 1
var SubURL2 = jQuery('#homebtn2 a.elementor-button-link').attr('href');

//HOME - GET 2ND URL FROM SUB IMAGE 2
var SubURL3 = jQuery('#homebtn3 a.elementor-button-link').attr('href');

//ASSIGN URL TO THE IMAGES ATTRIBUTES
jQuery('#first-image img').attr('data-url','https://www.adhoclondon.co.uk/product-category/valentines-day/');
jQuery('#second-image img').attr('data-url',SubURL2);
jQuery('#third-image img').attr('data-url',SubURL3);




//WHEN CLICK ON IMAGE FIRE THIS JQUERY
jQuery('.shop-valantine-box img').click(function(){
        var MainBanner = jQuery('.bannerleftimage img').attr('src');        
        var bannertext = jQuery('#change-text h1').text();
        var bannerbutton = jQuery('#btton-text span.elementor-button-text').text();
        var bannerlink = jQuery('#btton-text a.elementor-button-link').attr('href');
        //console.log(bannerlink)

       //image path 
        var SubImage = jQuery(this).attr('src');
        
        //first banner attibute
        var banner_text = jQuery('#first-image img').attr('data-textvalue');
        var Sub_text = jQuery(this).attr('data-textvalue');

        var banner_button = jQuery('#first-image img').attr('data-button');
        var Sub_button = jQuery(this).attr('data-button');

        var banner_button_link = jQuery('#first-image img').attr('data-url');

         
        var Sub_button_link = jQuery(this).attr('data-url');

        jQuery('#bannerbtn1 a.elementor-button-link').attr('href',Sub_button_link);
        jQuery('#bannerbtn1 .elementor-button-text').css('text-transform','uppercase');
        jQuery('#bannerbtn1 .elementor-button-text').text('SHOP ' + Sub_text);
        

        //image path swapping
        jQuery(this).attr('src',MainBanner);
        jQuery(this).attr('srcset',MainBanner);
        
        //banner attibute replace
        jQuery(this).attr('data-textvalue',banner_text);
        jQuery(this).attr('data-button',banner_button);
        jQuery(this).attr('data-url',banner_button_link);

        //CUSTOM TEXT CHANGE FOR SUB IMAGES BUTTON
        jQuery(this).parent().parent().next().find('.elementor-button-text').text(banner_text);



       // var Live_Banner_URL = jQuery('#first-image img').attr('data-url');
       

        // after swap chnage small image and attibute
        jQuery(".bannerleftimage img").fadeOut(500, function() {
            jQuery('.bannerleftimage img').attr('src',SubImage);
            jQuery('.bannerleftimage img').attr('srcset',SubImage);
        }).fadeIn(500);

        /*jQuery('.bannerleftimage img').attr('src',SubImage);
        jQuery('.bannerleftimage img').attr('srcset',SubImage);*/

        // first Banner attibute change
        jQuery('#first-image img').attr('data-textvalue',Sub_text);
        jQuery('#first-image img').attr('data-button',Sub_button);
        jQuery('#first-image img').attr('data-url',Sub_button_link);

        jQuery('#change-text h1').text(Sub_text);
        jQuery('#btton-text span.elementor-button-text').text(Sub_button);
        jQuery('#btton-text a.elementor-button-link').attr('href',Sub_button_link);
});


jQuery('.slider-for').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: true,
    asNavFor: '.slider-nav'
});
jQuery('.slider-nav').slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    //vertical:true,
    asNavFor: '.slider-for',
    dots: false,
    focusOnSelect: true,
    //verticalSwiping:true,
    responsive: [
    {
        breakpoint: 992,
        settings: {
          vertical: false,
        }
    },
    {
      breakpoint: 768,
      settings: {
        vertical: false,
      }
    },
    {
      breakpoint: 580,
      settings: {
        vertical: false,
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 380,
      settings: {
        vertical: false,
        slidesToShow: 2,
      }
    }
    ]
});


// testimonial jquery 

jQuery('.testimonial-slider').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 3000,
    arrows: false,
    dots: false
   

  // jQuery('.testimonial-slider').slick({
  //     autoplay: false,
  //     autoplaySpeed: 1000,
  //     speed: 600,
  //     draggable: true,
  //     //infinite: true,
  //     slidesToShow: 1,
  //     slidesToScroll: 1,
  //     arrows: false,
  //     dots: false
  });


});



function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
jQuery( document ).ready(function() {

// jQuery('.product-slider').owlCarousel({
//   loop: true,
//   autoplay: true,
//   items: 4,
//   nav: true,
//   autoplayHoverPause: true,
//   animateOut: 'slideOutUp',
//   animateIn: 'slideInUp'
// });
  if(jQuery(window).width() > 767){ 

   jQuery(".desk-slider").slick({
            //infinite: false,            
            slidesToShow: 1,
            vertical: true,             
            verticalSwiping: true,
            arrows: true,
            swipeToSlide: true,
            focusOnSelect: true,
        });
   }else{

  //   jQuery('.mob-slider .products').slick({
  //     infinite: true,
  //     slidesToShow: 1,
  //     slidesToScroll: 1
  // });
    
    
  }


jQuery("#button_dark img").click(function() {

    jQuery('body').toggleClass('dark');
    var act = jQuery('body').hasClass("dark");
    if(act){
         setCookie('ppkcookie','dark',7);
        }else{
            setCookie('ppkcookie','light',7);
        }
    }); 



//CLASS FOR ACTIVE COMMING SOON BADGE
jQuery(".coming-soon-badges:contains('yes')").addClass('active-badge');


//SINGLE PRODUCT PAGE - GET HIDDEN PRICE HTML
var price_HTMl = jQuery('.single-product .custom-price-value .elementor-widget-container').html().replace(/[\n\t]+/g,"");
//SINGLE PRODUCT PAGE - APPEND PRICE HTML TO ADD TO CART DIV
jQuery('.single-product .add-to-cart-single .elementor-widget-container').prepend(price_HTMl);

jQuery('.single-product .gpls-wcsamm-coming-soon-badge').attr('src', 'https://www.adhoclondon.co.uk/wp-content/uploads/2023/02/coming-soon.svg');


//SINGLE PRODUCT PAGE - SOLD OUT ICON
var sold_out_html = jQuery('.single-product .elementor-add-to-cart .out-of-stock').parent().html().replace(/[\n\t]+/g,"");
//SINGLE PRODUCT PAGE - SOLD OUT ICON MOVE
jQuery('.single-product .woocommerce-product-gallery').prepend(sold_out_html);




});




// Cursor Heart Image
// dots is an array of Dot objects,
// mouse is an object used to track the X and Y position
   // of the mouse, set with a mousemove event listener below
/*var dots = [],
    mouse = {
      x: 0,
      y: 0
    };*/

// The Dot object used to scaffold the dots
/*var Dot = function() {
  this.x = 0;
  this.y = 0;
  this.node = (function(){
    var n = document.createElement("div");
    n.className = "trail";
    document.body.appendChild(n);
    return n;
  }());
};*/
// The Dot.prototype.draw() method sets the position of 
  // the object's <div> node
/*Dot.prototype.draw = function() {
  this.node.style.left = this.x+15 + "px";
  this.node.style.top = this.y + "px";
};*/

// Creates the Dot objects, populates the dots array
/*for (var i = 0; i < 8; i++) {
  var d = new Dot();
  dots.push(d);
}*/

// This is the screen redraw function
/*function draw() {
  // Make sure the mouse position is set everytime
    // draw() is called.
  /*var x = mouse.x,
      y = mouse.y;
  
  // This loop is where all the 90s magic happens
  dots.forEach(function(dot, index, dots) {
    var nextDot = dots[index + 1] || dots[0];
    
    dot.x = x;
    dot.y = y;
    dot.draw();
    x += (nextDot.x - dot.x) * .8;
    y += (nextDot.y - dot.y) * .8;

  });
}

addEventListener("mousemove", function(event) {
  //event.preventDefault();
  mouse.x = event.pageX;
  mouse.y = event.pageY;
});

// animate() calls draw() then recursively calls itself
  // everytime the screen repaints via requestAnimationFrame().
function animate() {
  draw();
  requestAnimationFrame(animate);
}

// And get it started by calling animate().
animate();

// //Google Map
// Change the background color.
// const pinViewBackground = new google.maps.marker.PinView({
//   background: "#FBBC04 !important",
// });
// const markerViewBackground = new google.maps.marker.AdvancedMarkerView({
//   map,
//   position: { lat: 37.419, lng: -122.01 },
//   content: pinViewBackground.element,
// });*/



