<?php

/**
 * Block Name:  masterclasses
 *
 * @package WordPress
 * @subpackage Desymphony
 * @since 1.0.0
 */

$class = 'masterclasses';
if ( ! empty( $block['className'] ) ) {
    $class .= ' ' . esc_attr( $block['className'] );
}
if ( ! empty( $block['align'] ) ) {
    $class .= ' align' . esc_attr( $block['align'] );
}

?>
<style>
    .masterclasses {
        background: url('https://winevisionfair.com/wp-content/uploads/2025/06/DSK_MC_Bck.jpg') center center / cover no-repeat;
    }
    @media screen and (max-width: 768px) {
        .masterclasses {
            background: url('https://winevisionfair.com/wp-content/uploads/2025/06/MOB_MC_Bck.jpg') center center / cover no-repeat;
        }
    }
</style>
<section class="d-block position-relative wv-bg-w py-64 wv-section-box-shadow <?php echo esc_attr( $class ); ?>">
    <div class="container container-1024">
        <div class="row align-items-center justify-content-center">
            <div class="col-12">
                <h6 class="fw-600 ls-4 wv-color-r_70 mb-24">WINE MASTERCLASS SESSIONS</h6>
                <h1 class="fw-700 wv-color-w">A new class of mastery –<br /> learn from the true experts</h1>
            </div>
        </div>
    </div>
    <div class="d-none d-lg-block border-top border-bottom wv-bc-r_70 my-32"></div>
    
    <div class="container container-1024 d-none d-lg-block">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-12">
                <p class="fs-20 fw-600 wv-color-r_30">Through a program of wine masterclasses and workshops, seasoned experts,
                sommeliers, and chefs will guide visitors through the fascinating world of wine,
                spirits, and gastronomy. It’s an opportunity to learn firsthand from true experts!</p>
            </div>
        </div>
    </div>

    <div class="swiper wv-img-carousel py-48 py-lg-64">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MC_img_1.png"
                alt="Wine Vision MC image 1" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MC_img_2.png"
                alt="Wine Vision MC image 2" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MC_img_3.png"
                alt="Wine Vision MC image 3" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MC_img_4.png"
                alt="Wine Vision MC image 4" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MC_img_5.png"
                alt="Wine Vision MC image 5" loading="lazy">
            </div>

            <div class="swiper-slide">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/MC_img_6.png"
                alt="Wine Vision MC image 6" loading="lazy">
            </div>
        </div>
    </div>

    
    <div class="container container-1024 d-block d-lg-none">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-12">
                <p class="fs-20 fw-600 wv-color-r_30">Through a program of wine masterclasses and workshops, seasoned experts,
                sommeliers, and chefs will guide visitors through the fascinating world of wine,
                spirits, and gastronomy. It’s an opportunity to learn firsthand from true experts!</p>
                
                <div class="d-block border-top border-bottom wv-bc-r_70 my-24"></div>
            </div>
        </div>
    </div>
    

    <div class="container container-1024">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6">
                <p class="wv-color-w">Held on the second and third day of the fair, Masterclass
                    sessions strive to elucidate the art of crafting top-quality
                    wine, exploring the historical aspects of wine production
                    and the perspectives of this dynamic and evolving industry.
                    So far, the Wine Vision by Open Balkan fair has hosted over
                    60 experts who’ve led more than 100 masterclasses.</p>
            </div>
            <div class="col-lg-6">
                <p class="wv-color-w">We are truly honored to have achieved and maintained the
                    continuity of hosting renowned experts, sommeliers, and
                    wine connoisseurs from the region and the world. Their
                    participation in the fair allows them to share their vast
                    knowledge and experience with Masterclass attendees
                    from both professional and general spheres alike.</p>
            </div>
        </div>
    </div>
    
</section>


<?php
/* ------------------------------------------------------------
 * Lecturers section  – dynamic (CPT: wv_lecturer, Tax: wv_year)
 * ------------------------------------------------------------ */
$years = get_terms( [
	'taxonomy'   => 'wv_year',
	'hide_empty' => false,
	'orderby'    => 'name',   // ensures 2022 < 2023 < 2024 …
	'order'      => 'ASC',
] );

if ( empty( $years ) || is_wp_error( $years ) ) {
	return; // nothing to render
}
?>

<section class="section-lecturers py-32 py-lg-64 wv-bg-c_95">

	<div class="container text-center">
		<h2 class="mb-32 fw-h3 text-uppercase ls-4 h3 wv-color-w">Lecturers</h2>

		<!-- year tabs -->
		<ul class="nav nav-pills justify-content-center mb-24 gap-12" id="lecturersTab" role="tablist">
			<?php foreach ( $years as $idx => $year ) : ?>
				<li class="nav-item" role="presentation">
					<button class="nav-link br-32 py-12 px-24 fw-600 ls-4 lh-1 <?php echo ! $idx ? 'active' : ''; ?>"
						id="y<?php echo esc_attr( $year->slug ); ?>-tab"
						data-bs-toggle="pill"
						data-bs-target="#y<?php echo esc_attr( $year->slug ); ?>"
						type="button" role="tab"
						aria-controls="y<?php echo esc_attr( $year->slug ); ?>"
						aria-selected="<?php echo ! $idx ? 'true' : 'false'; ?>">
						<?php echo esc_html( $year->name ); ?>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<div class="d-none d-lg-block border-top border-bottom wv-bc-c_50 my-32"></div>

	<div class="container text-center">
		<div class="tab-content" id="lecturersTabContent">

			<?php foreach ( $years as $idx => $year ) :

				$q = new WP_Query( [
					'post_type'      => 'wv_lecturer',
					'posts_per_page' => -1,
					'orderby' => [
						'menu_order' => 'ASC',
						'title'      => 'ASC',  
					],
					'tax_query'      => [
						[
							'taxonomy' => 'wv_year',
							'field'    => 'term_id',
							'terms'    => $year->term_id,
						],
					],
				] ); ?>

				<div class="tab-pane fade <?php echo ! $idx ? 'show active' : ''; ?>"
					id="y<?php echo esc_attr( $year->slug ); ?>"
					role="tabpanel"
					aria-labelledby="y<?php echo esc_attr( $year->slug ); ?>-tab">

					<?php if ( $q->have_posts() ) : ?>
						<div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-12 text-center">
							<?php while ( $q->have_posts() ) : $q->the_post(); ?>
								<?php
								$country = get_post_meta( get_the_ID(), '_wv_lecturer_country', true );
								$img     = get_the_post_thumbnail_url( get_the_ID(), 'full' ) ?: 'https://placehold.co/300';
								?>
								<div class="col">
									<img src="<?php echo esc_url( $img ); ?>" class="rounded-circle w-100" alt="<?php the_title_attribute(); ?>">
									<h6 class="mt-12 mb-0 wv-color-r_70"><?php the_title(); ?></h6>
									<?php if ( $country ) : ?>
										<small class="wv-color-c_20 fs-12 lh-1 ls-3 text-uppercase"><?php echo esc_html( $country ); ?></small>
									<?php endif; ?>
								</div>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>
					<?php else : ?>
						<p class="wv-color-w">No lecturers for <?php echo esc_html( $year->name ); ?>.</p>
					<?php endif; ?>

				</div>
			<?php endforeach; ?>

		</div><!-- /.tab-content -->
	</div><!-- /.container -->
</section>


