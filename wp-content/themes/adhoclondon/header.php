<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  
  <meta name="google-site-verification" content="dijDhzKildby9Nxg1hX64O0R9jZEibCw_u8wXKODKNw" />
  <meta name="google-site-verification" content="hX2ENyI3dYSHjc1mWYehOlRVE9bACYHzyXZOJmxuPu0" />
	
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php $viewport_content = apply_filters( 'hello_elementor_viewport_content', 'width=device-width, initial-scale=1' ); ?>
	<meta name="viewport" content="<?php echo esc_attr( $viewport_content ); ?>">
	<link rel="profile" href="https://gmpg.org/xfn/11">
  <link rel="stylesheet" type="text/css" href="https://use.typekit.net/amm0iio.css">
	<?php wp_head(); ?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-NNKM2BW');</script>
<!-- End Google Tag Manager -->
  
  <?php if(get_field('mouse_effect_option','option') == 'yes'): ?>

	<script type="text/javascript">
            // <![CDATA[
				var colours=new Array('#f00', '#f06', '#f0f', '#f6f', '#f39', '#f9c'); // colours of the hearts
				var minisize=16; // smallest size of hearts in pixels
				var maxisize=28; // biggest size of hearts in pixels
				var hearts=66; // maximum number of hearts on screen
				var over_or_under="over"; // set to "over" for hearts to always be on top, or "under" to allow them to float behind other objects

            
            /*****************************
            *JavaScript Love Heart Cursor*
            *  (c)2013+ mf2fm web-design *
            *   http://www.mf2fm.com/rv  *
            *  DON'T EDIT BELOW THIS BOX *
            *****************************/
            var x=ox=400;
			var y=oy=300;
			var swide=800;
			var shigh=600;
			var sleft=sdown=0;
			var herz=new Array();
			var herzx=new Array();
			var herzy=new Array();
			var herzs=new Array();
			var kiss=false;
            
            if (typeof('addRVLoadEvent')!='function') function addRVLoadEvent(funky) {
              var oldonload=window.onload;
              if (typeof(oldonload)!='function') window.onload=funky;
              else window.onload=function() {
                if (oldonload) oldonload();
                funky();
              }
            }
            
            addRVLoadEvent(mwah);
            
            function mwah() { if (document.getElementById) {
              var i, heart;
              for (i=0; i<hearts; i++) {
                heart=createDiv("auto", "auto");
                heart.style.visibility="hidden";
                heart.style.zIndex=(over_or_under=="over")?"1001":"0";
                heart.style.color=colours[i%colours.length];
                heart.style.pointerEvents="none";
                if (navigator.appName=="Microsoft Internet Explorer") heart.style.filter="alpha(opacity=75)";
                else heart.style.opacity=0.75;
                heart.appendChild(document.createTextNode(String.fromCharCode(9829)));
                document.body.appendChild(heart);
                herz[i]=heart;
                herzy[i]=false;
              }
              set_scroll();
              set_width();
              herzle();
            }}
            
            function herzle() {
              var c;
              if (Math.abs(x-ox)>1 || Math.abs(y-oy)>1) {
                ox=x;
                oy=y;
                for (c=0; c<hearts; c++) if (herzy[c]===false) {
                  herz[c].firstChild.nodeValue=String.fromCharCode(9829);
                  herz[c].style.left=(herzx[c]=x-minisize/2)+"px";
                  herz[c].style.top=(herzy[c]=y-minisize)+"px";
                  herz[c].style.fontSize=minisize+"px";
                  herz[c].style.fontWeight='normal';
                  herz[c].style.visibility='visible';
                  herzs[c]=minisize;
                  break;
                }
              }
              for (c=0; c<hearts; c++) if (herzy[c]!==false) blow_me_a_kiss(c);
              setTimeout("herzle()", 40);
            }
            
            document.onmousedown=pucker;
            document.onmouseup=function(){clearTimeout(kiss);};
            
            function pucker() {
              ox=-1;
              oy=-1;
              kiss=setTimeout('pucker()', 100);
            }
            
            function blow_me_a_kiss(i) {
              herzy[i]-=herzs[i]/minisize+i%2;
              herzx[i]+=(i%5-2)/5;
              if (herzy[i]<sdown-herzs[i] || herzx[i]<sleft-herzs[i] || herzx[i]>sleft+swide-herzs[i]) {
                herz[i].style.visibility="hidden";
                herzy[i]=false;
              }
              else if (herzs[i]>minisize+2 && Math.random()<.5/hearts) break_my_heart(i);
              else {
                if (Math.random()<maxisize/herzy[i] && herzs[i]<maxisize) herz[i].style.fontSize=(++herzs[i])+"px";
                herz[i].style.top=herzy[i]+"px";
                herz[i].style.left=herzx[i]+"px";
              }
            }
            
            function break_my_heart(i) {
              var t;
              herz[i].firstChild.nodeValue=String.fromCharCode(9676);
              herz[i].style.fontWeight='bold';
              herzy[i]=false;
              for (t=herzs[i]; t<=maxisize; t++) setTimeout('herz['+i+'].style.fontSize="'+t+'px"', 60*(t-herzs[i]));
              setTimeout('herz['+i+'].style.visibility="hidden";', 60*(t-herzs[i]));
            }
            
            document.onmousemove=mouse;
            function mouse(e) {
              if (e) {
                y=e.pageY;
                x=e.pageX;
              }
              else {
                set_scroll();
                y=event.y+sdown;
                x=event.x+sleft;
              }
            }
            
            window.onresize=set_width;
            function set_width() {
              var sw_min=999999;
              var sh_min=999999;
              if (document.documentElement && document.documentElement.clientWidth) {
                if (document.documentElement.clientWidth>0) sw_min=document.documentElement.clientWidth;
                if (document.documentElement.clientHeight>0) sh_min=document.documentElement.clientHeight;
              }
              if (typeof(self.innerWidth)=='number' && self.innerWidth) {
                if (self.innerWidth>0 && self.innerWidth<sw_min) sw_min=self.innerWidth;
                if (self.innerHeight>0 && self.innerHeight<sh_min) sh_min=self.innerHeight;
              }
              if (document.body.clientWidth) {
                if (document.body.clientWidth>0 && document.body.clientWidth<sw_min) sw_min=document.body.clientWidth;
                if (document.body.clientHeight>0 && document.body.clientHeight<sh_min) sh_min=document.body.clientHeight;
              }
              if (sw_min==999999 || sh_min==999999) {
                sw_min=800;
                sh_min=600;
              }
              swide=sw_min;
              shigh=sh_min;
            }
            
            window.onscroll=set_scroll;
            function set_scroll() {
              if (typeof(self.pageYOffset)=='number') {
                sdown=self.pageYOffset;
                sleft=self.pageXOffset;
              }
              else if (document.body && (document.body.scrollTop || document.body.scrollLeft)) {
                sdown=document.body.scrollTop;
                sleft=document.body.scrollLeft;
              }
              else if (document.documentElement && (document.documentElement.scrollTop || document.documentElement.scrollLeft)) {
                sleft=document.documentElement.scrollLeft;
                sdown=document.documentElement.scrollTop;
              }
              else {
                sdown=0;
                sleft=0;
              }
            }
            
            function createDiv(height, width) {
              var div=document.createElement("div");
              div.style.position="absolute";
              div.style.height=height;
              div.style.width=width;
              div.style.overflow="hidden";
              div.style.backgroundColor="transparent";
              return (div);
            }
            // ]]>
            </script>

          <?php endif; ?>
</head>
<body <?php body_class(); ?>>
	<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NNKM2BW"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php hello_elementor_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content">
	<?php esc_html_e( 'Skip to content', 'hello-elementor' ); ?></a>

<?php
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
	if ( did_action( 'elementor/loaded' ) && hello_header_footer_experiment_active() ) {
		get_template_part( 'template-parts/dynamic-header' );
	} else {
		get_template_part( 'template-parts/header' );
	}
}
