<?php
/* Dashboard – Products (modal & list) */
if ( ! defined( 'ABSPATH' ) ) exit;

use Desymphony\Helpers\DS_Utils as Utils;

?>
<?php if ( ! Utils::is_admin_verified() || Utils::get_status() === 'Disabled') : ?>
    <?php
    wp_safe_redirect( home_url( '/wv-dashboard/' ) );
    exit;
    ?>
<?php endif; ?>
<div class="container container-1024 py-48">
	<div id="wv-products-list" class="">

    </div>
</div>

<!-- Modal -->
<div id="wv-product-modal" class="wv-modal" style="display:none;">
	<div class="wv-modal-backdrop"></div>
	<div class="container container-1024">
		<div class="wv-modal-content wv-card br-12 wv-bg-w shadow-lg">

			<form id="wv-product-form" method="post" enctype="multipart/form-data">

				<div class="wv-card-header p-24 d-flex justify-content-between align-items-center gap-16 border-bottom">
					<h4 id="wv-modal-title" class="m-0"><?php esc_html_e( 'Product Details', 'wv-addon' ); ?></h4>

					<div class="d-flex align-items-center wv-justify-end wv-gap-8">
						<button type="submit" class="wv-button wv-button-pill wv-button-sm wv-button-edit px-16 wv-btn-edit me-8">
							<?php esc_html_e( 'Save', 'wv-addon' ); ?>
						</button>
                        <button type="button" id="wv-modal-close" class="wv-button wv-button-pill wv-icon-button wv-button-sm wv-button-light-danger wv-btn-delete wv-px-8"><span class="wv wv_x-40-f fs-20"></span></button>
					</div>
				</div>

				<div class="wv-card-body py-12 px-24">

					<?php wp_nonce_field( 'wv_dashboard_nonce', 'security' ); ?>
					<input type="hidden" id="wv-product-id"   name="id"   value="">
					<input type="hidden" id="wv-product-type" name="type" value="wine">
					<input type="hidden" name="wv-user-id" value="<?php echo esc_attr( get_current_user_id() ); ?>">

					<div class="row">
						<!-- Image -->
                        <!-- Image (new cropper version) -->
                        <div class="col-lg-6">
                            <?php
                                $field_args = [
                                    'field_name'   => 'product-image',
                                    'field_id'     => 'wv-product-image-hidden',
                                    'current_url'  => '',                           // filled in by JS
                                    'label'        => __( 'Product image', 'wv-addon' ),
                                    'max_size_mb'  => 2,
                                    'profile_key'  => 'product',
                                    'aspect_ratio' => '9:12',
                                    'upload_action'=> 'wv_crop_upload',
                                    'placeholder'  => 'https://placehold.co/1024',                       // bottle icon
                                    'requirements' => __(
                                        'Center‑aligned photo of the whole bottle / pack on white (or no) background.',
                                        'wv-addon'
                                    ),
                                    /* These placeholders are replaced server‑side when saving the file
                                       (see DS_Media_Handler). “id” is injected later in JS once we know
                                       the product row ID. */
                                    'placeholders' => [
                                        'id' => '{{id}}',
                                    ],
                                ];
                                $partial_path = DS_THEME_DIR . '/inc/public/views/partials/form-fields/cropper-field.php';
                                if ( file_exists( $partial_path ) ) {
                                    $args = $field_args;
                                    include $partial_path;
                                } else {
                                    echo '<p class="wv-error">Cropper partial missing.</p>';
                                }
                            ?>
                        </div>

						<!-- Generic -->
						<div class="col-lg-6 mb-0">
							<div class="row mb-8">
								<div class="col-12">
									<div class="wv-input-group">
										<label for="product-title"><?php esc_html_e( 'Product name', 'wv-addon' ); ?> <span class="ds-required">*</span></label>
										<input type="text" id="product-title" name="title" class="wv-input" required>
									</div>
								</div>
							</div>

							<?php
							include DS_THEME_DIR . '/inc/public/views/partials/forms/product-wine-form.php';
							include DS_THEME_DIR . '/inc/public/views/partials/forms/product-spirits-form.php';
							include DS_THEME_DIR . '/inc/public/views/partials/forms/product-food-form.php';
							include DS_THEME_DIR . '/inc/public/views/partials/forms/product-other-form.php';
							?>
						</div>

                        <div class="col-12 ds-confirmation">
                            <div class="wv-input-group position-relative border-top border-bottom py-12 my-24" >
                                <label class="wv-custom-checkbox wv-custom-checkbox-small d-block">
                                    <input type="checkbox" checked disabled>
                                    <div class="wv-checkbox-card wv-checkbox-card-inline align-items-center justify-content-center w-100">
                                        <div class="wv-check"></div>
                                        <h6 class="fw-400 ps-8 fs-12">I hereby declare & responsibly confirm that this product, it's name & packaging are property of company under which I applied to exhibit at the 2025 fair.</h6>
                                    </div>
                                </label>
                            </div>
                        </div>
					</div>


                    <!-- THIS TO APPEAR ONLY IF WE ARE ADDING NEW PRODUCT -->
                    <!-- ----- -->
					<div id="ds-new-prod-btn" class="row d-none">
						<div class="col-12 wv-text-right">
							<button type="submit" class="wv-button w-100 wv-button-default">
								<?php esc_html_e( 'ADD PRODUCT', 'wv-addon' ); ?>
							</button>
						</div>
					</div>
                    <!-- ----- -->

				</div><!-- /.wv-card-body -->
			</form>

		</div>
	</div>
</div>
