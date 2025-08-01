<?php
/**
 * Block: ds-open-podcast-carousel (cleaned-up year handling)
 */

$uid      = $block['id'] ?? uniqid( 'ds_podcast_' );
$modal_id = "{$uid}_modal";

/* ---------------------------------------------------------------------------
 * Optional additional classes from the editor
 * -------------------------------------------------------------------------*/
$class = '';
if ( ! empty( $block['className'] ) ) { $class .= ' ' . esc_attr( $block['className'] ); }

/* ---------------------------------------------------------------------------
 * Basic block fields
 * -------------------------------------------------------------------------*/
$title       = get_field( 'title',       $block['id'] ) ?: __( 'Open Podcast', 'desymphony' );
$title_short = get_field( 'title_short', $block['id'] ) ?: $title;

/* ---------------------------------------------------------------------------
 * Year filter – works for 0 / single / multiple selections
 * -------------------------------------------------------------------------*/
$yrs_raw   = get_field( 'year', $block['id'] );   // could be ID / term object / array / 0
$year_ids  = [];

if ( $yrs_raw ) {
	// always deal with an array
	$yrs_raw = is_array( $yrs_raw ) ? $yrs_raw : [ $yrs_raw ];

	foreach ( $yrs_raw as $y ) {
		if ( is_numeric( $y ) ) {
			$year_ids[] = (int) $y;
		} elseif ( is_object( $y ) ) {              // WP_Term
			$year_ids[] = (int) $y->term_id;
		} elseif ( is_array( $y ) && isset( $y['term_id'] ) ) {
			$year_ids[] = (int) $y['term_id'];
		}
	}
	$year_ids = array_filter( array_unique( $year_ids ) );
}

/* ---------------------------------------------------------------------------
 * Query – latest 8 when no year is picked, otherwise everything for that year
 * -------------------------------------------------------------------------*/
$args = [
	'post_type'           => 'wv_podcast',
	'posts_per_page'      => $year_ids ? -1 : 8,
	'orderby'             => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
	'no_found_rows'       => true,
	'ignore_sticky_posts' => true,
];

if ( $year_ids ) {
	$args['tax_query'] = [
		[
			'taxonomy'         => 'wv_year',
			'field'            => 'term_id',
			'terms'            => $year_ids,
			'include_children' => false,
		],
	];
}

$q = new WP_Query( $args );
if ( ! $q->have_posts() ) { return; }
?>
<section id="<?php echo esc_attr( $uid ); ?>"
		 class="wv-section-box-shadow pt-48 pb-12 wv-bg-w <?php echo esc_attr( $class ); ?>">

	<!-- heading & “Watch all” -->
	<div class="container container-1024 pb-24 d-flex align-items-center justify-content-between">
		<h3 class="h1 fw-700 m-0 d-none d-lg-block"><?php echo esc_html( $title ); ?></h3>
		<h3 class="h1 fw-700 m-0 d-lg-none"><?php echo esc_html( $title_short ); ?></h3>

		<a href="https://www.youtube.com/@openpodcast8059"
		   class="wv-button wv-button-pill wv-button-red fw-500 ls-2"
		   target="_blank" rel="noopener noreferrer">Watch all</a>
	</div>

	<!-- swiper carousel -->
	<div class="container-fluid px-0">
		<div class="swiper wv-h-podcast-carousel">
			<div class="swiper-wrapper">
				<?php
				while ( $q->have_posts() ) :
					$q->the_post();
					$thumb    = get_the_post_thumbnail_url( get_the_ID(), 'large' );
					$video_id = get_post_meta( get_the_ID(), '_wv_podcast_video_id', true );
					?>
					<div class="swiper-slide">						
							<div class="bg-image aspect-ratio-16-9 br-12"
								 style="background-image:url('<?php echo esc_url( $thumb ); ?>')">
                                 <a href="#"
                                    class="ds-podcast-slide d-block text-decoration-none"
                                    data-yt="<?php echo esc_attr( $video_id ); ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#<?php echo esc_attr( $modal_id ); ?>">
                                            <span class="position-absolute top-50 start-50 translate-middle fs-2 wv-color-w">
                                                <span class="wv wv_yt fs-64"><span class="path1"></span><span class="path2"></span></span>
                                            </span>
                                            
                                    </a>
							</div>
						<div class="pt-24 slide-content">
							<h3 class="mt-0 mb-24 fw-600 pe-48"><?php the_title(); ?></h3>
							<p class="pe-32"><?php echo wp_trim_words( wp_strip_all_tags( get_the_content() ), 30, '…' ); ?></p>
						</div>
					</div>
				<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</div>

	<!-- Bootstrap modal (unique per block) -->
	<div class="modal fade" id="<?php echo esc_attr( $modal_id ); ?>" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-xl">
			<div class="modal-content bg-transparent border-0">
				<button type="button" class="btn-close position-absolute end-0 top-0 m-3"
						data-bs-dismiss="modal" aria-label="Close"></button>
				<div class="ratio ratio-16x9">
					<iframe src="" allow="autoplay; encrypted-media" allowfullscreen></iframe>
				</div>
			</div>
		</div>
	</div>

	<!-- per-block JS (no direct bootstrap reference => no “bootstrap is undefined”) -->
	<script>
	(() => {
		const root    = document.getElementById('<?php echo esc_js( $uid ); ?>');
		if (!root) return;

		const modalEl = root.querySelector('#<?php echo esc_js( $modal_id ); ?>');
		const iframe  = modalEl.querySelector('iframe');

		let busy = false;

		/* 1. Set video src just before Bootstrap shows the modal.
		      Works for original & Swiper-cloned slides (event delegation) */
		root.addEventListener('click', evt => {
			const trigger = evt.target.closest('.ds-podcast-slide');
			if (!trigger || !root.contains(trigger) || busy) return;

			const vid = (trigger.dataset.yt || '').trim();
			if (!vid) return;

			iframe.src =
				`https://www.youtube.com/embed/${vid}?autoplay=1&rel=0&showinfo=0`;
			busy = true;                    // lock until modal closes
		});

		/* 2. When modal finishes hiding → stop playback & unlock */
		modalEl.addEventListener('hidden.bs.modal', () => {
			iframe.src = '';
			busy = false;
		});
	})();
	</script>
</section>
