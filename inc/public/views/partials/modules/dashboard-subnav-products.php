<?php
/**
 * Dashboard – Products sub‑nav
 *
 * @package Wv_Addon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Desymphony\Helpers\DS_Utils as Utils;

$exhibitor_type = Utils::get_exhibitor_field(); // returns 'Wine', 'Spirits', 'Food', 
$user_category = Utils::get_user_category(); 
$has_products = Utils::exhibitor_has_products();
$nav_state    = $has_products ? 'wv-has-products' : 'wv-no-products';

?>
<style>
	.wv_add .path1:before { color: var(--wv-w); }
	.wv_add .path2:before { color: var(--wv-v); }
	.wv-custom-checkbox .wv-check::before { border: 1px solid var(--wv-c_10); }

	<?php if ( in_array( $exhibitor_type, [ 'Wine', 'Spirits' ], true ) && $user_category !== 'Other' ) : ?>
	#wv-product-nav.wv-no-products #wv-add-product-dropdown-menu {
			position: relative;
			top: auto;
			left: auto;
			right: auto;
			bottom: auto;
			margin: auto;
			border: 0 !important;
			width: 100%;
			display: block !important;
			border-radius: 0 !important;
	}
	#wv-product-nav.wv-no-products .wv-prodcts-count,
	#wv-product-nav.wv-no-products #wv-add-product-dropdown-btn {
		display: none !important
	}
	#wv-product-nav.wv-no-products .wv-add-product-controls {
		width: 100%;
	}
	#wv-product-nav.wv-no-products .wv-product-nav-title {
		display: block !important
	}
	<?php endif; ?>
</style>

<div id="wv-product-nav" class="d-block position-relative shadow-lg z-50 wv-bg-w <?php echo esc_attr( $nav_state ); ?>">
	<section class="wv-product-nav-title wv-bg-w py-32 border-bottom wv-border-c-50 d-none">
		<div class="container">
			<div class="row">
				<div class="col-12 text-center d-flex align-items-center justify-content-center">
					<h1 class="fs-20 text-uppercase my-0 ls-3 lh-1 fw-600 wv-color-c">
						SELECT PRODUCT CATEGORY
					</h1>									
				</div>
			</div>
		</div>
	</section>
	<div class="container container-1024 py-24">
		<div class="row">
			<div class="col-12 d-flex align-items-center justify-content-between">

				<!-- counter -->
				<div class="wv-color-c wv-prodcts-count">
					<strong id="wv-products-count"></strong>
					<?php esc_html_e( 'Products added', 'wv-addon' ); ?>
				</div>

				<!-- Add‑product control -->
				<div class="position-relative wv-add-product-controls">

					<?php if ( in_array( $exhibitor_type, [ 'Wine', 'Spirits' ], true ) && $user_category !== 'Other' &&  $user_category !== 'Wine Equipment' &&  $user_category !== 'Distillation Equipment' ) : ?>

						<button id="wv-add-product-dropdown-btn"
								class="wv-button wv-button-default wv-button-pill wv-button-sm d-none d-lg-flex align-items-center px-8 fs-14">
							<?php esc_html_e( 'Add product', 'wv-addon' ); ?>
							&nbsp;<i class="wv wv_add ms-4 fs-24" style="margin:-4px">
								<span class="path1"></span><span class="path2"></span>
							</i>
						</button>						

						<div id="wv-add-product-dropdown-menu" class="wv-dropdown-menu" style="display:none;">
							<?php require get_template_directory() . '/inc/public/views/partials/modules/helper-product-select.php'; ?>
						</div>

					<?php elseif ( $exhibitor_type === 'Food'  ) : ?>

						<button id="wv-add-food-btn"
								class="wv-button wv-button-default wv-button-pill wv-button-sm d-flex align-items-center px-8 fs-14">
							<?php esc_html_e( 'Add food product', 'wv-addon' ); ?>
							&nbsp;<i class="wv wv_add ms-4 fs-24"><span class="path1"></span><span class="path2"></span></i>
						</button>

					<?php elseif ( $exhibitor_type !== 'Food' && ( $user_category === 'Wine Equipment' || $user_category === 'Distillation Equipment' || $user_category !== 'Other')  ) : ?>

						<button id="wv-add-other-btn"
								class="wv-button wv-button-default wv-button-pill wv-button-sm d-flex align-items-center px-8 fs-14">
							<?php esc_html_e( 'Add product', 'wv-addon' ); ?>
							&nbsp;<i class="wv wv_add ms-4 fs-24"><span class="path1"></span><span class="path2"></span></i>
						</button>

					<?php else : /* Other */ ?>

						<button id="wv-add-other-btn"
								class="wv-button wv-button-default wv-button-pill wv-button-sm d-flex align-items-center px-8 fs-14">
							<?php esc_html_e( 'Add product', 'wv-addon' ); ?>
							&nbsp;<i class="wv wv_add ms-4 fs-24"><span class="path1"></span><span class="path2"></span></i>
						</button>

					<?php endif; ?>



				</div><!-- /.position-relative -->
			</div>
		</div>
	</div>
</div>
