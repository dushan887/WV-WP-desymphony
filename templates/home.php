<?php
/**
 * News Page
 */
get_template_part( 'templates/header' );
require DS_THEME_DIR . '/templates/blocks/ds-news-ptb.php';
?>


<?php if ( have_posts() ) : ?>
    <div class="container container-1024 py-32">    
        <div class="row g-12">
            <?php
            $index = 0;
            while ( have_posts() ) : the_post();

            $featuredImageUrl = has_post_thumbnail() 
                ? get_the_post_thumbnail_url(get_the_ID(), 'full') 
                : 'https://placehold.co/1024.jpg';
            $title = get_the_title();
            if ( mb_strlen( $title ) > 100 ) {
                $title = mb_substr( $title, 0, 100 ) . '...';
            }

                // First post: large, always col-lg-8
                if ( 0 === $index ) : ?>
                    <div class="col-12 col-lg-8">
                        <article class="news-card news-card-featured">
                            <a class="d-block" href="<?php the_permalink(); ?>">
                                <div class="ds-square-card br-8" style="background-image:url(<?php echo esc_url( $featuredImageUrl ); ?>);">
                                    <div class="news-meta">
                                        <span class="news-date"><?php echo get_the_date( 'd/m/Y' ); ?></span>
                                        <span class="news-cat">#<?php echo esc_html( get_the_category()[0]->slug ); ?></span>
                                    </div>
                                    <div class="news-title">
                                        <h3 class="my-0"><?php echo $title; ?></h3>
                                    </div>                                        
                                </div>
                            </a>
                        </article>
                    </div>
                <?php
                else:
                    // Other posts: col-lg-4, choose full HTML per "view" setting
                    $view = get_post_meta( get_the_ID(), '_secondary_display_option', true ) ?: 'view1';

                ?>
                    <div class="col-6 col-lg-4">
                        <?php if ( 'view3' === $view ) : ?>
                            <!-- View 3: dark circle -->
                            <article class="news-card news-card-view3">
                                <a class="d-block" href="<?php the_permalink(); ?>">
                                    <div class="ds-square-card br-8" style="background-image:url(<?php echo esc_url( $featuredImageUrl ); ?>);">
                                        <div class="news-meta">
                                            <span class="news-date"><?php echo get_the_date( 'd/m/Y' ); ?></span>
                                            <span class="news-cat">#<?php echo esc_html( get_the_category()[0]->slug ); ?></span>
                                        </div>
                                        <div class="news-title">
                                            <h3 class="my-0"><?php echo $title; ?></h3>
                                        </div>                                        
                                    </div>
                                </a>
                            </article>

                        <?php elseif ( 'view2' === $view ) : ?>
                            <!-- View 2: light circle -->
                            <article class="news-card news-card-view2">
                                <a class="d-block" href="<?php the_permalink(); ?>">
                                    <div class="ds-square-card br-8" style="background-image:url(<?php echo esc_url( $featuredImageUrl ); ?>);">
                                        <div class="news-meta">
                                            <span class="news-date"><?php echo get_the_date( 'd/m/Y' ); ?></span>
                                            <span class="news-cat">#<?php echo esc_html( get_the_category()[0]->slug ); ?></span>
                                        </div>
                                        <div class="news-title">
                                            <h3 class="my-0"><?php echo $title; ?></h3>
                                        </div>                                        
                                    </div>
                                </a>
                            </article>

                        <?php else : ?>
                            <!-- View 1: white square -->
                            <article class="news-card news-card-view1">
                                <a class="d-block" href="<?php the_permalink(); ?>">
                                    <div class="ds-square-card br-8 bg-white">
                                        <div class="news-image" style="background-image:url(<?php echo esc_url( $featuredImageUrl ); ?>);"></div>                                          
                                        <div class="news-meta">
                                            <span class="news-date"><?php echo get_the_date( 'd/m/Y' ); ?></span>
                                            <span class="news-cat">#<?php echo esc_html( get_the_category()[0]->slug ); ?></span>
                                        </div>
                                        <div class="news-title">
                                            <h3 class="my-0"><?php echo $title; ?></h3>
                                        </div>                                        
                                    </div>
                                </a>
                            </article>
                        <?php endif; ?>
                    </div>
                <?php
                endif;

                $index++;
            endwhile;
            ?>
        </div>
    </div>
    <!-- Pagination -->
    <div class="ds-pagination py-32 border-top">
        <div class="d-flex align-items-center justify-content-center">
            <?php
            the_posts_pagination( [
                'mid_size'  => 1,
                'prev_text' => __( '<span class="wv wv_point-40-o"></span>', 'desymphony' ),
                'next_text' => __( '<span class="wv wv_point-40-o"></span>', 'desymphony' ),
            ] );
            ?>
        </div>
    </div>
<?php endif; ?>

    
</div>

<?php
get_template_part( 'templates/footer' );
