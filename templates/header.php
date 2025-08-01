<?php
// templates/header.php
use Desymphony\Helpers\DS_Utils as Utils;

$user         = wp_get_current_user();
$extra_class  = '';
$shadow_class = 'wv-section-box-shadow';

if ( ! empty( $user->ID ) && ! empty( $user->roles ) ) {
	if ( in_array( 'exhibitor', $user->roles, true ) ) {
		$extra_class = 'wv-exhibitor';
	} elseif ( in_array( 'buyer', $user->roles, true ) ) {
		$extra_class = 'wv-buyer';
	} elseif ( in_array( 'visitor', $user->roles, true ) ) {
		$extra_class = 'wv-visitor';
	}
}

/* add decline flag regardless of role ------------------------------- */
if ( Utils::get_status() === 'Disabled' ) {
	$extra_class .= ( $extra_class ? ' ' : '' ) . 'wv-declined';
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php echo esc_attr( $extra_class ); ?>">


   <head>
      <meta charset="<?php bloginfo('charset'); ?>">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <?php wp_head(); ?>

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-BBHCHPHV1J"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-BBHCHPHV1J');
        </script>

      
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-TSZTV55V');</script> 
        <!-- End Google Tag Manager -->

   </head>

<body <?php body_class( esc_attr( $extra_class . ' ' . $shadow_class ) ); ?>>

        <div class="global-spinner-overlay" id="globalSpinner">
            <div class="spinner"></div>
        </div>

      <header id="wv-main-header" class="wv-header fixed-top <?php echo esc_attr( $shadow_class ); ?>">

      
        <!-- Prime Header -->
        <nav id="wv-main-nav" class="navbar navbar-light bg-white py-0">
          <div class="container">
              <!-- Logo -->
              <a class="navbar-brand fw-700 text-uppercase fs-32" href="<?php echo esc_url( home_url() ); ?>">
                <img src="/wp-content/uploads/2025/05/wv_header_logo.svg" alt="<?php echo esc_html( get_bloginfo( 'name' ) ); ?>" style="
                        max-width: 200px;
                        margin-top: -3px;
                        margin-bottom: -3px;
                    " class="d-none d-lg-block">

                <?php if ( Utils::is_exhibitor() ) : ?>               
                
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/07/wv_logo-ex-app.svg" alt="<?php echo esc_html( get_bloginfo( 'name' ) ); ?>" style="
                            max-width: 40px;
                        " class="d-block d-lg-none py-4">
                <?php elseif ( Utils::is_buyer() ) : ?>
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/07/wv_logo-buy.svg" alt="<?php echo esc_html( get_bloginfo( 'name' ) ); ?>" style="
                            max-width: 40px;
                        " class="d-block d-lg-none py-4">
                <?php elseif ( Utils::is_visitor() ) : ?>
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/07/wv_logo-pro-v.svg" alt="<?php echo esc_html( get_bloginfo( 'name' ) ); ?>" style="
                            max-width: 40px;
                        " class="d-block d-lg-none py-4">
                <?php else : ?>
                    <img src="https://winevisionfair.com/wp-content/uploads/2025/07/wv_logo-official.svg" alt="<?php echo esc_html( get_bloginfo( 'name' ) ); ?>" style="
                            max-width: 40px;
                        " class="d-block d-lg-none py-4">
                <?php endif; ?>
              </a>
              <!-- Centered Nav Links -->
              <ul class="navbar-nav flex-row mx-auto d-none d-lg-flex">
                <li class="nav-item px-16"><a class="nav-link fw-500" href="/2025-concept/">WVOB 2025</a></li>
                <li class="nav-item px-16"><a class="nav-link fw-500" href="/register">Apply for 2025</a></li>
                <li class="nav-item px-16"><a class="nav-link fw-500" href="/news">Latest news</a></li>
              </ul>
              <!-- My Account + Toggler -->
              <div class="d-flex align-items-center">
                <?php if ( is_user_logged_in() ) : ?>
                    <!-- shown only to logged-in users -->
                    <a href="/wv-dashboard/"
                    class="wv-button wv-button-default wv-button-pill wv-button-sm me-8 d-none d-lg-flex align-items-center px-8">
                        <i class="wv wv_account-a me-4 fs-20" style="margin:-4px"></i>
                        <?php esc_html_e( 'My account', 'desymphony-wine-vision' ); ?>
                    </a>

                    <a  class="wv-button wv-button-sm wv-icon-button wv-button-pill shadow-none p-0 me-8 wv-button-default d-flex d-lg-none"
                        href="#"
                        role="button"
                        data-bs-toggle="collapse" 
                        data-bs-target="#wv-dashboard-nav"
                        aria-controls="wv-dashboard-nav">
                        <i class="wv wv_account-a fs-20"></i>
                    </a>
                <?php else : ?>
                    <!-- shown only to guests -->
                    <a href="/login/"
                    class="wv-button wv-button-grey wv-button-pill wv-button-sm me-8 d-none d-lg-flex align-items-center px-8">
                        <i class="wv wv_account-a fs-20 me-4" style="margin:-4px"></i>
                        <?php esc_html_e( 'Log in', 'desymphony-wine-vision' ); ?>
                    </a>
                    <a href="/login/"
                    class="wv-button wv-button-sm wv-icon-button wv-button-pill shadow-none p-0 me-8 wv-button-grey d-flex d-lg-none">
                        <i class="wv wv_account-a fs-20" ></i>
                    </a>
                <?php endif; ?>

                <!-- burger menu (always visible) -->
                <a href="#"
                class="navbar-toggler wv-button wv-button-sm wv-icon-button wv-button-pill wv-button-grey shadow-none p-0"
                data-bs-toggle="collapse"
                data-bs-target="#wv-main-menu"
                aria-controls="wv-main-menu"
                aria-expanded="false"
                aria-label="<?php esc_attr_e( 'Toggle navigation', 'desymphony-wine-vision' ); ?>">
                    <i class="wv wv_burger fs-30">
                        <span class="path1 opacity-0"></span><span class="path2 wv-button-grey"></span>
                    </i>
                </a>
            </div>

          </div>
          <!-- Mega-menu (collapsed by default) -->
          <div class="mega-menu w-100 position-absolute py-48 collapse wv-color-w" id="wv-main-menu">
              <div class="container px-24 px-lg-128">
                  <div class="row">
                    <div class="col-6 col-lg-3">
                        <h6 class="text-uppercase fw-500 ls-3 fs-16 wv-color-w wv-color-ww border-bottom wv-bc-c_80 pb-12">2025 Fair</h6>
                        <ul class="list-unstyled fs-16 opacity-75">
                            <li><a href="/2025-fair-concept/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">2025 venue concept</a></li>
                            <li><a href="/register/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Exhibit at the 2025 fair</a></li>
                            <li><a href="/register/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Trade at the 2025 fair</a></li>
                            <li><a href="/register/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Visit 2025 fair</a></li>
                        </ul>
                        </div>
                        <div class="col-6 col-lg-3">
                        <h6 class="text-uppercase fw-500 ls-3 fs-16 wv-color-w wv-color-ww border-bottom wv-bc-c_80 pb-12">Activities</h6>
                        <ul class="list-unstyled fs-16 opacity-75">
                            <li><a href="/competitions/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Competitions</a></li>
                            <li><a href="/masterclasses/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Masterclasses</a></li>
                            <li><a href="/b2b-sessions/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">B2B Sessions</a></li>
                            <li><a href="/guided-tours/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Guided tours</a></li>
                        </ul>
                        </div>
                        <div class="col-6 col-lg-3">
                        <h6 class="text-uppercase fw-500 ls-3 fs-16 wv-color-w wv-color-ww border-bottom wv-bc-c_80 pb-12">Media</h6>
                        <ul class="list-unstyled fs-16 opacity-75">
                            <li><a href="/news/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Latest news</a></li>
                            <li><a href="/open-podcast/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Open Podcast</a></li>
                            <li><a href="/gallery/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Gallery</a></li>
                            <li><a href="/partners/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Partners</a></li>
                        </ul>
                        </div>
                        <div class="col-6 col-lg-3">
                        <h6 class="text-uppercase fw-500 ls-3 fs-16 wv-color-w wv-color-ww border-bottom wv-bc-c_80 pb-12">Wine Vision</h6>
                        <ul class="list-unstyled fs-16 opacity-75">
                            <li><a href="/about-fair/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">About Fair</a></li>
                            <li><a href="/awards/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Awards</a></li>
                            <li><a href="/contact/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Contact</a></li>
                            <li><a href="/support/" class="wv-color-c_10 fs-14 text-decoration-none d-block mb-8 fw-300">Support</a></li>
                        </ul>
                    </div>
                  </div>

                    <!-- Social + Search -->
                    <div class="row py-32">

                        <div class="col-12 col-lg-5 mb-24 mb-lg-0">
                            <!-- search bar -->
                            <form  class="ds-search-field w-100" role="search" action="/" method="get">
                                <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 ps-8 fs-24 position-absolute start-0 top-50 translate-middle-y d-block">
                                    <span class="wv wv_search-60-i"><span class="path1"></span><span class="path2"></span></span>
                                </span>

                                <input type="search" name="s"
                                        class="form-control wv-bg-c_90 text-white ps-48 br-32 wv-bc-c_80"
                                        placeholder="Search" aria-label="Search">

                                </div>
                            </form>

                        </div>
                        <div class="col-3 col-lg-3 offset-lg-1">
                            <a href="/wv-dashboard/" class="wv-button wv-button-c2 wv-button-border wv-button-pill me-8 d-inlnie-flex align-items-center px-12 fw-400 py-8 wv-bc-c_80">
                                <i class="wv wv_announce me-4 fs-24" style="margin:-3px -4px"><span class="path1"></span><span class="path2"></span></i>Announcements</a>
                        </div>
                        <div class="col-9 col-lg-3">
                            <div class="wv-social-dark d-flex gap-8 gap-lg-16 align-items-center justify-content-end justify-content-lg-end">
                                <a class="wv-button wv-button-sm wv-button-pill wv-button-c_70 wv-icon-button p-0" href="https://www.instagram.com/winevisionbyopenbalkan/?hl=en" target="_blank">
                                    <i class="wv wv_ig fs-32"><span class="path1 opacity-0"></span><span class="path2"></span></i>
                                </a>
                                <a class="wv-button wv-button-sm wv-button-pill wv-button-c_70 wv-icon-button p-0" href="https://www.youtube.com/channel/UCX96ALnODATOdJjyPEHWGvQ" target="_blank">
                                    <i class="wv wv_yt fs-32"><span class="path1 opacity-0"></span><span class="path2"></span></i>
                                </a>
                                <a class="wv-button wv-button-sm wv-button-pill wv-button-c_70 wv-icon-button p-0" href="https://www.tiktok.com/tag/winevisionbyopenbalkan" target="_blank">
                                    <i class="wv wv_tt fs-32"><span class="path1 opacity-0"></span><span class="path2"></span></i>
                                </a>
                                <a class="wv-button wv-button-sm wv-button-pill wv-button-c_70 wv-icon-button p-0" href="https://www.facebook.com/winevision.openbalkan/" target="_blank">
                                    <i class="wv wv_fb fs-32"><span class="path1 opacity-0"></span><span class="path2"></span></i>
                                </a>
                                <a class="wv-button wv-button-sm wv-button-pill wv-button-c_70 wv-icon-button p-0" href="https://www.linkedin.com/posts/wine-vision-open-balkan_attention-wine-enthusiasts-and-industry-professionals-activity-7184662058548817920-tt2z" target="_blank">
                                    <i class="wv wv_ldn fs-32"><span class="path1 opacity-0"></span><span class="path2"></span></i>
                                </a>
                            </div>
                        </div>
                    
                    </div>
                   
                  

              </div>
          </div>
          <!-- End Mega-menu -->
 
          

        </nav>
        <!-- End Prime Header -->
        <?php if ( is_user_logged_in() ) : ?>
            <?php require DS_THEME_DIR . '/templates/header/wv-profile-main.php'; ?>
        <?php endif; ?>
        

        <!-- Secondary Header -->

        <?php if ( is_page_template( 'templates/page-auth.php' ) ) :

            require DS_THEME_DIR . '/templates/header/wv-auth-progress.php'; 
            
        elseif ( is_page_template( 'templates/page-profile.php' ) && !is_page( 'wv-application' ) ) :     

            ?>
            <style>
                @media screen and (min-width: 992px) {
                    #wv-dashboard-nav {
                        display: block !important;
                    }
                }
            </style>
            <?php

        elseif ( is_page_template( 'templates/page-profile.php' ) && is_page( 'wv-application' ) ) :     

            require DS_THEME_DIR . '/templates/header/wv-application.php'; 

        elseif ( is_page_template( 'templates/page-legal.php' ) ) :     

            require DS_THEME_DIR . '/templates/header/wv-legal-header.php'; 

        endif; 
        
        
        ?>
        
        <!-- End Secondary Header -->

        

      </header>
      <div id="wv-main">