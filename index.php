<?php
// Fallback template file.
// Typically, you'd have your loop or get_template_part calls here.

get_template_part( 'templates/header' ); ?> 

<main id="site-main">
    <?php if ( have_posts() ) : ?>
        <div class="d-block py-32 py-lg-48">

            <div class="container container-1024">
                <div class="row pb-32">
                <div class="col-12">
                    <h1 class="my-0 px-24 display-5 fw-600"><?php the_title(); ?></h1>
                </div>
                </div>
        </div>
        
                
        <div class="container">
            <?php while ( have_posts() ) : the_post();
                the_content();
            endwhile; ?>
        </div>
    <?php else : ?>
        <?php echo get_template_part( 'templates/404' ); ?>
    <?php endif; ?>
</main>

<?php get_template_part( 'templates/footer' ); ?>
