<?php
/**
 * Template Name: 404 Page
 */
get_template_part( 'templates/header' );
?>

<style>
  #wv-wrap::before {
    background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_winevisionfair_Bck.jpg) !important;
  }
  @media screen and (max-width: 768px) {
    #wv-wrap::before {
      background-image: url(https://winevisionfair.com/wp-content/uploads/2025/06/MOB_winevisionfair_Bck.jpg) !important;
    }
  }
  .wv-divider {
    width: 8rem;
    height: 1px;
    margin: 2rem auto;
    background: var(--wv-c);
  }
  #wv-main {
    padding: 0 !important;
  }
</style>

<div id="wv-wrap" class="py-0 d-flex align-items-center justify-content-center">
  <section class="position-relative">
    <div class="container container-768 py-48">
      <div class="row">
        <div class="col-12 text-center">
          <h1 class="my-0 lh-1 fw-600 pb-48 display-5">404 - Page Not Found</h1>

          <p class="fs-20 mb-0">
            <span class="fw-600">Oops! The page you are looking for does not exist.</span><br class="d-none d-md-block">
            It might have been moved, deleted, or the URL might be incorrect.<br class="d-none d-md-block">
            Please check the URL or return to the homepage.
          </p>

          <div class="wv-divider"></div>

          <a href="/" class="wv-button wv-button-pill mt-12">Go to Homepage</a>
        </div>
      </div>
    </div>
  </section>
</div>

<?php
get_template_part( 'templates/footer' );
