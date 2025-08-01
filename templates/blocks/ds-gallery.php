<?php
/**
 * Block: ds-gallery  (multi-instance safe)
 */

$uid       = $block['id'] ?? uniqid( 'ds_gallery_' );
$modal_id  = $uid . '_modal';
$carousel  = $uid . '_carousel';

$class  = 'ds-gallery';
$class .= ! empty( $block['className'] ) ? ' ' . esc_attr( $block['className'] ) : '';
$class .= ! empty( $block['align'] )     ? ' align' . esc_attr( $block['align'] ) : '';

$title   = get_field( 'title' ) ?: __( 'Gallery', 'desymphony' );
$gallery = get_field( 'gallery' );            
if ( empty( $gallery ) ) { return; }
?>
<style>
	.carousel-control-prev {
		background: linear-gradient( to right, var(--wv-c), transparent);
	}
	.carousel-control-next {
		background: linear-gradient( to left, var(--wv-c), transparent);
	}
</style>
<section id="<?php echo esc_attr( $uid ); ?>" class="pt-32 pb-12 wv-bg-w <?php echo esc_attr( $class ); ?>">
	<div class="container container-1024 pb-24 d-flex align-items-center justify-content-between">
		<h3 class="h1 fw-700 m-0"><?php echo esc_html( $title ); ?></h3>

		<!-- now wired straight to the modal -->
		<a href="#"
		   class="wv-button wv-button-pill wv-button-grey fw-500 ls-2 js-ds-gallery-view"
		   data-bs-toggle="modal"
		   data-bs-target="#<?php echo esc_attr( $modal_id ); ?>">
			View
		</a>
	</div>

	<div class="swiper wv-img-carousel">
		<div class="swiper-wrapper">
			<?php foreach ( $gallery as $i => $img ) :
				$thumb = $img['sizes']['large'] ?? $img['url'];
				$full  = $img['url'];
				$alt   = $img['alt'] ?: '';
			?>
				<div class="swiper-slide">
					<a href="<?php echo esc_url( $full ); ?>"
					   class="ds-gallery-item"
					   data-index="<?php echo esc_attr( $i ); ?>"
					   data-bs-toggle="modal"
					   data-bs-target="#<?php echo esc_attr( $modal_id ); ?>">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<!-- lightbox ----------------------------------------------------------- -->
	<div class="modal fade" id="<?php echo esc_attr( $modal_id ); ?>" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-xl">
			<div class="modal-content border-0 bg-transparent">
				<button type="button" class="btn-close position-absolute end-0 top-0 m-3"
						data-bs-dismiss="modal" aria-label="Close"></button>

				<div id="<?php echo esc_attr( $carousel ); ?>"
					 class="carousel slide" data-bs-interval="false" data-bs-touch="true">
					<div class="carousel-inner">
						<?php foreach ( $gallery as $i => $img ) : ?>
							<div class="carousel-item<?php echo $i === 0 ? ' active' : ''; ?>">
								<img class="ds-gallery-fit d-block mx-auto"
									 src="<?php echo esc_url( $img['url'] ); ?>"
									 alt="<?php echo esc_attr( $img['alt'] ?: '' ); ?>">
							</div>
						<?php endforeach; ?>
					</div>
					<button class="carousel-control-prev" type="button"
							data-bs-target="#<?php echo esc_attr( $carousel ); ?>" data-bs-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Prev</span>
					</button>
					<button class="carousel-control-next" type="button"
							data-bs-target="#<?php echo esc_attr( $carousel ); ?>" data-bs-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Next</span>
					</button>
				</div>
			</div>
		</div>
	</div>

	<script>
	(() => {
		const root       = document.getElementById('<?php echo esc_js( $uid ); ?>');
		if (!root) return;

		const modalEl    = root.querySelector('#<?php echo esc_js( $modal_id ); ?>');
		const carouselEl = root.querySelector('#<?php echo esc_js( $carousel ); ?>');
		const carousel   = bootstrap.Carousel.getOrCreateInstance(carouselEl, { interval: false });

		/* thumbs sync → slide */
		root.querySelectorAll('.ds-gallery-item').forEach(item => {
			item.addEventListener('click', () => {
				carousel.to(parseInt(item.dataset.index, 10) || 0);
			});
		});

		/* “View” button always opens at slide 0 */
		root.querySelector('.js-ds-gallery-view')?.addEventListener('click', () => {
			carousel.to(0);
		});
	})();
	</script>
</section>

