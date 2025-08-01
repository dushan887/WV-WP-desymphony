<?php
/**
 * Block: wv-award-cards
 *
 * @package Desymphony
 * @subpackage Desymphony
 */

$class = 'wv-award-cards';
if ( ! empty( $block['className'] ) ) { $class .= ' ' . esc_attr( $block['className'] ); }
if ( ! empty( $block['align'] )     ) { $class .= ' align' . esc_attr( $block['align'] ); }

/* ---------------------------------------------------------------------------
 * ACF “type” field → Wine | Rakija | Food
 * ------------------------------------------------------------------------ */
$type = get_field( 'type' );                 // e.g. "Wine" / "Rakija" / "Food"
if ( ! $type ) { return; }                   // nothing chosen → nothing to render

$type  = strtolower( (string) get_field( 'type' ) );   // wine | rakija | food
$bg    = [
	'wine'   => 'wv-bg-w_100',
	'rakija' => 'wv-bg-s_100',
	'food'   => 'wv-bg-f_100',
];
$bg_gradient = [
    'wine'   => 'linear-gradient(to bottom, var(--wv-w_100) 80%, var(--wv-c) 100%)',
    'rakija' => 'linear-gradient(to bottom, var(--wv-s_100) 80%, var(--wv-c) 100%)',
    'food'   => 'linear-gradient(to bottom, var(--wv-f_100) 80%, var(--wv-c) 100%)',
];
$color = [
    'wine'   => 'var(--wv-w_100)',
    'rakija' => 'var(--wv-s_100)',
    'food'   => 'var(--wv-f_100)',
];
/* static nav map --------------------------------------------------------- */
$nav = [
	'wine'   => [ 'url' => '/awards/wine-trophy/',        'label' => 'Wine<br class="d-block d-sm-none">Trophy' ],
	'rakija' => [ 'url' => '/awards/rakija-trophy/',      'label' => 'Rakija<br class="d-block d-sm-none">Trophy' ],
	'food'   => [ 'url' => '/awards/culinary-challenge/', 'label' => 'Culinary<br class="d-block d-sm-none">Challenge' ],
];

/* ---------------------------------------------------------------------------
 * All years (we’ll still show tabs even if no posts for some years)
 * ------------------------------------------------------------------------ */
$years = get_terms( [
	'taxonomy'   => 'wv_year',
	'hide_empty' => false,
	'orderby'    => 'name',
	'order'      => 'ASC',
] );

if ( empty( $years ) || is_wp_error( $years ) ) { return; }

$default_year = '2024';
$default_slug = null;
foreach ( $years as $y ) {
	if ( $y->name === $default_year || $y->slug === $default_year ) {
		$default_slug = $y->slug;
		break;
	}
}
?>

<style>
.nav-pills.wv-nav-pills-border-w .nav-link.active,
.nav-pills.wv-nav-pills-border-w .nav-link:hover { color: <?php echo esc_attr( $color[ $type ] ); ?>; }
</style>

<!-- static top nav (Wine Trophy / Rakija Trophy / Culinary Challenge) -->
<section class="d-block pt-24 position-relative wv-award-cards-nav <?php echo esc_attr( $class . ' type-' . $type ); ?>">
	<div class="container container-768">
		<div class="row justify-content-center gx-8">
			<?php foreach ( $nav as $slug => $item ) :

				$is_current  = ( $slug === $type );
				$link_class  = $is_current
					? $bg[ $slug ] . ' wv-color-w pointer-events-none'   // active with colour
					: 'wv-bg-w wv-color-c_90 wv-opacity-hover-9';        // inactive

				?>
				<div class="col-4 my-0">
					<a href="<?php echo esc_url( $item['url'] ); ?>"
					   class="d-block br-8 br-b-0 text-center p-12 <?php echo esc_attr( $link_class ); ?>">
						<?php echo $item['label']; ?>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="wv-award-cards-content wv-bg-c pb-32 <?php echo esc_attr( $class ); ?>" style="min-height: 100vh;">
	<div class="container-fluid text-center py-16" style="background: <?php echo esc_attr( $bg_gradient[ $type ] ); ?>;">

		<!-- year tabs -->
		<ul class="nav nav-pills justify-content-center gap-12 wv-nav-pills-border-w" id="awardsTab" role="tablist">
			<?php foreach ( $years as $y ) :
				$is_active = ( $y->slug === $default_slug ) || ( ! $default_slug && $y === reset( $years ) ); ?>
				<li class="nav-item" role="presentation">
					<button class="nav-link br-32 py-12 px-24 fw-600 ls-4 lh-1 wv-button-border <?php echo $is_active ? 'active' : ''; ?>"
						id="y<?php echo esc_attr( $y->slug ); ?>-tab"
						data-bs-toggle="pill"
						data-bs-target="#y<?php echo esc_attr( $y->slug ); ?>"
						type="button" role="tab"
						aria-controls="y<?php echo esc_attr( $y->slug ); ?>"
						aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>">
						<?php echo esc_html( $y->name ); ?>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>


	<div class="container text-center py-32">
		<div class="tab-content" id="awardsTabContent">
			<?php foreach ( $years as $y ) :
				$is_active = ( $y->slug === $default_slug ) || ( ! $default_slug && $y === reset( $years ) );

				$q = new WP_Query( [
					'post_type'      => 'wv_award',
					'posts_per_page' => -1,
					'orderby'        => 'menu_order',
					'orderby' => [
						'menu_order' => 'ASC',
						'title'      => 'ASC',  
					],
					'tax_query'      => [
						[
							'taxonomy' => 'wv_year',
							'field'    => 'term_id',
							'terms'    => $y->term_id,
						],
					],
					'meta_query'     => [
						[
							'key'     => '_wv_award_type',
							'value'   => $type,
							'compare' => '=',
						],
					],
				] ); ?>

				<div class="tab-pane fade <?php echo $is_active ? 'show active' : ''; ?>"
					id="y<?php echo esc_attr( $y->slug ); ?>"
					role="tabpanel"
					aria-labelledby="y<?php echo esc_attr( $y->slug ); ?>-tab">

					<?php if ( $q->have_posts() ) : ?>
						<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-12 text-center">
							<?php while ( $q->have_posts() ) : $q->the_post(); ?>
								<?php
								$front = wp_get_attachment_image_url( get_post_meta( get_the_ID(), '_wv_award_front_image_id', true ), 'full' ) ?: 'https://placehold.co/300';
								$back  = wp_get_attachment_image_url( get_post_meta( get_the_ID(), '_wv_award_back_image_id',  true ), 'full' ) ?: '';
								?>
								<div class="col">
									<div class="d-block position-relative br-12 overflow-hidden ">
                                        <img src="<?php echo esc_url( $front ); ?>" class="w-100" alt="<?php the_title_attribute(); ?>">
                                        <?php if ( $back ) : ?>
                                            <img src="<?php echo esc_url( $back ); ?>" class="position-absolute top-0 start-0 w-100 h-100" alt="">
                                        <?php endif; ?>
                                    </div>
								</div>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>
					<?php else : ?>
						<p class="wv-color-w"><?php printf( esc_html__( 'No %1$s awards for %2$s.', DS_THEME_TEXTDOMAIN ), esc_html( $type ), esc_html( $y->name ) ); ?></p>
					<?php endif; ?>
                    

				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
