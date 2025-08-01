<?php
/**
 * Product‑Other form (modal, right column)
 *
 * @package Wv_Addon
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wv-other-fields">
	<div class="row gy-8">

		<!-- Product category* -->
		<input type="hidden" id="other-category" name="category" class="wv-input" required variant="other">

		<!-- Product description* (≤500 chars) -->
		<div class="col-12">
			<div class="wv-input-group position-relative">
				<label class="d-flex justify-content-between" for="other-desc">
					<span><?php esc_html_e( 'Product description', 'wv-addon' ); ?><span class="ds-required">*</span></span>
					<small class="fs-12 opacity-75"><?php esc_html_e( 'Up to 500 characters', 'wv-addon' ); ?></small>
				</label>
				<textarea id="other-desc"
				          name="description"
				          class="wv-input"
				          rows="8"
				          maxlength="500"
				          required></textarea>
			</div>
		</div>

	</div><!-- /.row -->
</div>
