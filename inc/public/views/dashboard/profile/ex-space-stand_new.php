<?php
/**
 * Dashboard → Exhibitor  ▸  Exhibition Space / Stand
 * ------------------------------------------------------------------
 * • Works for Head, Solo and Co‑Exhibitors
 * • Single source of truth = DS_Stand_Assign
 * • Falls back to order history when the stand has not
 *   yet been “assigned” to anyone (fresh purchase case)
 *
 * July 2025
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

use Desymphony\Dashboard\DS_Stand_Assign;
use Desymphony\Database\DS_CoEx_Repository;
use Desymphony\Helpers\DS_Utils as Utils;

/* -------------------------------------------------------------------------
 * 0. Helper – ALL purchased stand product‑IDs for one user
 * ---------------------------------------------------------------------- */
if ( ! function_exists( 'ds_purchased_stand_ids' ) ) {
	function ds_purchased_stand_ids( int $uid ): array {
		$out = [];
		$orders = wc_get_orders( [
			'customer_id' => $uid,
			'status'      => [ 'wc-completed', 'wc-processing', 'wc-on-hold' ],
			'limit'       => -1,
		] );
		foreach ( $orders as $o ) {
			foreach ( $o->get_items() as $it ) {
				$p = $it->get_product();
				if ( $p && has_term( 'stand', 'product_cat', $p->get_id() ) ) {
					$out[] = (int) $p->get_id();
				}
			}
		}
		return array_unique( $out );
	}
}

/* -------------------------------------------------------------------------
 * 0‑bis. Helper – robust hall / size extractor (meta *or* attributes)
 * ---------------------------------------------------------------------- */
if ( ! function_exists( 'ds_extract_stand_info' ) ) {
	function ds_extract_stand_info( int $pid ): array {

		$hall = get_post_meta( $pid, 'wv_hall_only',  true );
		$no   = get_post_meta( $pid, 'wv_stand_no',   true );
		$size = get_post_meta( $pid, 'wv_stand_size', true );

		if ( ! $hall || ! $size ) {
			$prod = wc_get_product( $pid );
			if ( $prod ) {
				foreach ( $prod->get_attributes() as $key => $attr ) {
					$opts = $attr->get_options();
					if ( ! $opts ) continue;

					if ( ! $hall && $key === 'pa_hall' ) {
						$hall = reset( $opts );
					}
					if ( ! $size && in_array( $key, [ 'pa_stand-size', 'pa_stand_size' ], true ) ) {
						$size = reset( $opts );
					}
				}
			}
		}

		return [
			'hall' => trim( (string) $hall ),
			'no'   => trim( (string) $no   ?: '???' ),
			'size' => trim( (string) $size ?: '?'   ),
		];
	}
}

/* -------------------------------------------------------------------------
 * 1.  Context & roles
 * ---------------------------------------------------------------------- */
$current_id = get_current_user_id();
$role_model = Utils::get_exhibitor_participation( $current_id );       // Head / Solo / Co‑Exhibitor
$is_owner   = in_array( $role_model, [ 'Head Exhibitor', 'Solo Exhibitor' ], true );

/* -------------------------------------------------------------------------
 * 2.  Which stand product‑IDs do I occupy?
 * ---------------------------------------------------------------------- */
$owner_ids    = $is_owner
	? array_unique( array_merge(
		DS_Stand_Assign::stands_for_user( $current_id ),   // already assigned to self
		ds_purchased_stand_ids( $current_id )              // but maybe not assigned yet
	) )
	: [];

$assigned_ids = !$is_owner ? DS_Stand_Assign::stands_for_user( $current_id ) : [];

$all_ids      = array_unique( array_merge( $owner_ids, $assigned_ids ) );

/* -------------------------------------------------------------------------
 * 3.  Group by hall   → $user_halls[ '2C' ][ ] = [ no,size,pid ]
 * ---------------------------------------------------------------------- */
$user_halls = [];
foreach ( $all_ids as $pid ) {
	$info = ds_extract_stand_info( $pid );
	if ( ! $info['hall'] ) continue;
	$user_halls[ $info['hall'] ][] = [
		'no'   => $info['no'],
		'size' => $info['size'],
		'pid'  => $pid,
	];
}
ksort( $user_halls, SORT_NATURAL );

$first_hall = $user_halls ? array_key_first( $user_halls ) : '';
$total_rows = array_sum( array_map( 'count', $user_halls ) );
$first_row  = $first_hall ? $user_halls[ $first_hall ][0] : [];

/* -------------------------------------------------------------------------
 * 4.  Co‑exhibitor list (for assignment dropdown – owners only)
 * ---------------------------------------------------------------------- */
$co_users = [];
if ( $is_owner ) {
	$repo = new DS_CoEx_Repository();
	$inv  = $repo->get_invites_by_exhibitor( $current_id );

	$me = get_userdata( $current_id );
	$co_users[] = [
		'id'          => $current_id,
		'name'        => trim( "$me->first_name $me->last_name" ) ?: $me->display_name,
		'is_head'     => true,
		'accepted'    => true,
		'assigned_to' => '',
	];

	foreach ( $inv as $row ) {
		$co_user  = $row->co_id ? get_userdata( $row->co_id ) : null;
		$co_users[] = [
			'id'          => (int) $row->co_id,
			'name'        => $co_user ? trim( "$co_user->first_name $co_user->last_name" ) ?: $co_user->display_name : $row->coemail,
			'is_head'     => false,
			'accepted'    => $row->status === 'accepted',
			'assigned_to' => $row->stand_code ?? '',
		];
	}
}

/* -------------------------------------------------------------------------
 * 5.  Localise for standassign.js
 * ---------------------------------------------------------------------- */
wp_localize_script( 'desymphony-stand-assign', 'wvUserStands', [ 'byHall' => $user_halls ] );
wp_localize_script( 'desymphony-stand-assign', 'wvStandUsers', $co_users );

/* -------------------------------------------------------------------------
 * 6.  May the visitor assign stands?
 * ---------------------------------------------------------------------- */
$may_assign = $is_owner && (
	$role_model === 'Head Exhibitor' ||
	(
		$role_model === 'Solo Exhibitor' &&
		array_filter( $owner_ids, function ( $pid ) {
			$sz = (int) get_post_meta( $pid, 'wv_stand_size', true );
			return in_array( $sz, [ 24, 49 ], true );
		})
	)
);



/* -------------------------------------------------------------------------
 * 7.  MARK‑UP – OVERVIEW
 * ---------------------------------------------------------------------- */
?>
<div class="container container-1024">
	<section class="d-block pt-24">
		<div class="row"><div class="col-12">
			<div class="wv-card br-12 wv-bg-w">
				<div class="wv-card-header p-24" style="border-bottom:2px solid #eee;">
					<h4 class="m-0 fs-20 fw-600 ls-3">OVERVIEW</h4>
				</div>

				<div class="wv-card-body p-24">
					<div class="row g-12 mb-24">

						<!-- Hall selector -->
						<div class="col-6 col-lg-3">
							<select id="ds-hall-select" class="w-100" <?= $user_halls ? '' : 'disabled'; ?>>
								<option value="">Select Hall</option>
								<?php foreach ( $user_halls as $h => $rows ) : ?>
									<option value="<?= esc_attr( $h ); ?>" <?= selected( $h, $first_hall ); ?>>
										Hall <?= esc_html( $h ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<!-- Totals -->
						<div class="col-6 col-lg-3">
							<div class="ds-stand-info-box br-4 d-flex justify-content-between">
								<span>Total stands</span><span><?= (int) $total_rows; ?></span>
							</div>
						</div>

						<!-- Numbers -->
						<div class="col-6 col-lg-3">
							<div class="ds-stand-info-box br-4 d-flex justify-content-between wv-bg-c_70">
								<span class="wv-color-w">My stand</span>
								<span id="my-stand-number" class="wv-color-w">
									<?= $total_rows
											? implode( ', ', array_map( fn($r)=>$r['no'], array_merge(...array_values($user_halls)) ) )
											: '–'; ?>
								</span>
							</div>
						</div>

						<!-- Size -->
						<div class="col-6 col-lg-3">
							<div class="ds-stand-info-box br-4 d-flex justify-content-between wv-bg-o">
								<span class="wv-color-w">My stand size</span>
								<span id="my-stand-size" class="wv-color-o me-4">
									<?= esc_html( $first_row['size'] ?? '–' ); ?> m<sup>2</sup>
								</span>
							</div>
						</div>

					</div><!-- /.row -->

					<!-- SVG placeholder -->
					<div class="hall hall-svg-container mb-16 wv-bg-c_10 br-4 ds-hall-root">
						<div class="container container-1024 pb-24 d-flex justify-content-center">
							<div id="hall-content" class="w-100" data-hall-slug="<?= esc_attr( $first_hall ); ?>"></div>
						</div>
					</div>

					<?php if ( $may_assign ) : ?>
						<?php /* control strip (standassign.js handles behaviour) */ ?>
						<div class="row g-12 align-items-end">

							<div class="col-12 col-lg-4">
								<div id="selected-stand-box"
								     class="ds-stand-info-box br-4 wv-bg-w wv-bc-v d-none">
									<strong>Stand <span id="selected-stand-number">–</span></strong>
									<span><span id="selected-stand-size">0</span> m²</span>
								</div>
							</div>

							<div class="col-12 col-lg-4">
								<div id="ds-assign-select" class="selectBox ds-stand-info-box br-4 wv-bc-v">
									<div class="selectBox__value fw-600">Select stand user</div>
									<div class="dropdown-menu"></div>
								</div>
							</div>

							<div class="col-12 col-lg-4">
								<button id="wv-assign-stand" class="wv-button br-4 d-none">Assign stand</button>
								<button id="wv-remove-stand" class="wv-button wv-button-red br-4 d-none">Free up stand</button>
							</div>
						</div>
					<?php endif; ?>

				</div><!-- /.card-body -->
			</div>
		</div></div>
	</section>

	<?php
	/* ---------------------------------------------------------------------
	 * 8. SPECIFICATIONS
	 * ------------------------------------------------------------------ */
	$specs    = include DS_THEME_DIR . '/inc/public/views/stands/stands.php';
	$img_base = '/wp-content/themes/desymphony/src/images/stands/';
	?>

	<section class="d-block pt-24">
		<div class="row"><div class="col-12">
			<div class="wv-card br-12 wv-bg-w">
				<div class="wv-card-header p-24" style="border-bottom:2px solid #eee;">
					<h4 class="m-0 fs-20 fw-600 ls-3">SPECIFICATIONS</h4>
				</div>

				<div class="wv-card-body p-24">
					<?php foreach ( $all_ids as $pid ) :

              
						$info = ds_extract_stand_info( $pid );
						if ( ! $info['hall'] ) continue;
                  

						$size_key = preg_match( '/^\d+$/', $info['size'] ) ? $info['size'] . 'm2' : 'custom';
                  echo '<pre>';
                  var_dump( $info['size'] );
                  var_dump( $info['hall'] );
                  echo '</pre>';
                  // $size_key = '12m2_Hall_3A';
						$spec     = $specs[ $size_key ] ?? null;
                  
						if ( ! $spec ) continue;
					?>
						<div class="ds-stand-nav mb-48">
							<h5 class="fw-600 mb-12">
								Hall <?= esc_html( $info['hall'] ); ?> – <?= esc_html( $info['size'] ); ?> m²
							</h5>

							<?php if ( $size_key === 'custom' ) : ?>
								<div class="row g-12">
									<div class="col-lg-6 mb-3 mb-lg-0">
										<?php if ( ! empty( $spec['blueprint_img'] ) ) : ?>
											<img src="<?= esc_url( $img_base . $spec['blueprint_img'] ); ?>"
											     class="img-fluid br-8" alt="Blueprint">
										<?php endif; ?>
									</div>
									<div class="col-lg-6">
										<p><?= esc_html__( 'Custom raw space – our production team will contact you for a bespoke build.', 'wv-addon' ); ?></p>
									</div>
								</div>
							<?php else : ?>
								<div class="row g-12">
									<div class="col-lg-6 mb-4 mb-lg-0">
										<?php if ( ! empty( $spec['branding_img'] ) ) : ?>
											<img src="<?= esc_url( $img_base . $spec['branding_img'] ); ?>"
											     class="img-fluid br-8" alt="Stand">
										<?php endif; ?>
									</div>
									<div class="col-lg-6">
										<ul class="list-unstyled mb-0">
											<?php foreach ( $spec['included'] as $item ) : ?>
												<li class="mb-2">
													<?= esc_html( $item['label'] ); ?>
													<?php if ( $item['qty'] ) : ?>
														&ndash; <strong><?= (int) $item['qty']; ?></strong>
													<?php endif; ?>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>

					<?php if ( empty( $all_ids ) ) : ?>
						<p class="text-muted mb-0"><?= esc_html__( 'No stands to show yet.', 'wv-addon' ); ?></p>
					<?php endif; ?>
				</div><!-- /.card-body -->
			</div>
		</div></div>
	</section>
</div>
