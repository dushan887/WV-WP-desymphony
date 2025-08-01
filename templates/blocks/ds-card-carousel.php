<?php

/**
 * Block Name:  ds-card-carousel
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'ds-card-carousel';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}
if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

// Grab the ACF relationship field (array of WP_Post objects, in order)
$cards = get_field( 'cards' );
$card_section_title = get_field( 'card_section_title' );
?>
<style>
  /* .link-back{opacity:0;transition:opacity .25s;} */
.swiper-slide:hover .wv-card-link-back{opacity:1 !important;}
.swiper-slide:hover .wv-card-link-front{opacity:0 !important;}
</style>


<section class="wv-home-card-carousel pt-48 <?php echo esc_attr( $class ); ?>" style="background: var(--wv-c_5);">
  <div class="container container-1024 pb-24">
    <div class="d-flex align-items-center justify-content-between">
      <?php if ( $card_section_title ): ?>
        <h3 class="fw-700 ls-4"><?php echo esc_html( $card_section_title ); ?></h3>
      <?php endif; ?>
    </div>
  </div>

  <?php if ( $cards ): ?>
  <div class="container-fluid overflow-hidden pb-64">
    <div class="d-block ds-w-100-256">
      <div class="swiper-container wv-card-carousel position-relative">
        <div class="swiper-wrapper">
            <?php foreach ( $cards as $card ):
            
                // front/back image IDs
                $front_id   = get_post_meta( $card->ID, '_wv_card_front_image_id', true );
                $back_id    = get_post_meta( $card->ID, '_wv_card_back_image_id',  true );
                // URLs (full size)
                $front_url  = $front_id ? wp_get_attachment_image_url( $front_id, 'full' ) : '';
                $back_url   = $back_id  ? wp_get_attachment_image_url( $back_id,  'full' ) : '';

                $link_front_id  = get_post_meta( $card->ID, '_wv_card_link_front_image_id', true );
                $link_back_id   = get_post_meta( $card->ID, '_wv_card_link_back_image_id',  true );
                $link_front_url = $link_front_id ? wp_get_attachment_image_url( $link_front_id, 'full' ) : '';
                $link_back_url  = $link_back_id  ? wp_get_attachment_image_url( $link_back_id,  'full' ) : '';
                // link URL & target
                $card_url    = get_post_meta( $card->ID, '_wv_card_url',       true );
                $card_target = get_post_meta( $card->ID, '_wv_card_target',  true ) ?: '_blank';
            ?>
                <div class="swiper-slide br-12 overflow-hidden bg-image"
                    style="background-image: url('<?php echo esc_url( $front_url ); ?>')">
                <?php if ( $card_url ): ?>
                    <a href="<?php echo esc_url( $card_url ); ?>"
                    target="<?php echo esc_attr( $card_target ); ?>">
                <?php endif; ?>

                    <?php if ( $back_url ): ?>
                    <img src="<?php echo esc_url( $back_url ); ?>"
                        alt="<?php echo esc_attr( get_the_title( $card ) ); ?>"
                        class="img-fluid">
                    <?php endif; ?>

                <?php if ( $card_url ): ?>

                    <div class="wv-card-link-front position-absolute top-0 start-0 w-100 h-100">
                      <img src="<?php echo esc_url( $link_front_url ); ?>" alt="" class="img-fluid w-100 h-100 object-fit-cover">
                    </div>
                    <div class="wv-card-link-back position-absolute top-0 start-0 w-100 h-100" style="opacity:0;">
                      <img src="<?php echo esc_url( $link_back_url ); ?>" alt="" class="img-fluid w-100 h-100 object-fit-cover">
                    </div>
                    </a>
                <?php endif; ?>
                </div>
            <?php endforeach; ?>

        </div>        
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</section>
