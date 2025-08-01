<?php
/**
 * Product‑Wine form (modal, right column)
 *
 * @package Wv_Addon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------- helper data ---------- */
$wine_categories = [
	'still'      => [ 'Red wine', 'White wine', 'Rosé', 'Orange wine' ],
	'sparkling'  => [ 'Champagne', 'Prosecco', 'Pét‑Nat', 'Crémant', 'Cava', 'Sekt', 'Other sparkling' ],
	'fortified'  => [ 'Bermet', 'Vermouth', 'Sherry', 'Madeira', 'Marsala', 'Port', 'Other fortified' ],
	'sweet'      => [ 'Straw wine', 'Ice wine', 'Sauternes', 'Tokaji Aszú', 'Vin Santo', 'Late harvest', 'Other sweet' ],
];

$wine_group_labels = [
	'still'     => __( 'Still wines',       'wv-addon' ),
	'sparkling' => __( 'Sparkling wines',   'wv-addon' ),
	'fortified' => __( 'Fortified wines',   'wv-addon' ),
	'sweet'     => __( 'Dessert / sweet',   'wv-addon' ),
];

$production_ranges = [
	'0-10000'       => '1 – 10 000',
	'10000-50000'   => '10 000 – 50 000',
	'50000-250000'  => '50 000 – 250 000',
	'250000-500000' => '250 000 – 500 000',
	'500000+'       => '500 000+',
];
?>

<div class="wv-wine-fields">
	<div class="row gy-8">

		<!-- Product category* -->
		<div class="col-12">
			<div class="wv-input-group">
				<label for="wine-category"><?php esc_html_e( 'Product category', 'wv-addon' ); ?><span class="ds-required">*</span></label>
				<select id="wine-category" name="category" class="wv-input" required>
					<option value="" disabled selected>— <?php esc_html_e( 'Select category', 'wv-addon' ); ?> —</option>
					<?php foreach ( $wine_categories as $group => $opts ) : ?>
						<optgroup label="<?php echo esc_attr( $wine_group_labels[ $group ] ); ?>">
							<?php foreach ( $opts as $o ) : ?>
								<option value="<?php echo esc_attr( sanitize_title( $o ) ); ?>">
									<?php echo esc_html( $o ); ?>
								</option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<!-- Wine region* -->
		<div class="col-12">
			<div class="wv-input-group">
				<label for="wine-region"><?php esc_html_e( 'Wine region', 'wv-addon' ); ?><span class="ds-required">*</span></label>
				<input type="text" id="wine-region" name="region" class="wv-input" required>
			</div>
		</div>

		<!-- Variety* -->
		<div class="col-12">
			<div class="wv-input-group">
				<label for="wine-variety"><?php esc_html_e( 'Variety', 'wv-addon' ); ?><span class="ds-required">*</span></label>
				<input type="text" id="wine-variety" name="variety" class="wv-input" required>
			</div>
		</div>

		<!-- Vintage*, Volume (ML)*, Annual production (L)* -->
		<div class="col-6 col-lg-3">
			<div class="wv-input-group">
				<label for="wine-vintage"><?php esc_html_e( 'Vintage', 'wv-addon' ); ?><span class="ds-required">*</span></label>
				<input type="number" id="wine-vintage" name="vintage_year"
				       class="wv-input" min="1900" max="<?php echo esc_attr( date( 'Y' ) ); ?>" required>
			</div>
		</div>
		<div class="col-6 col-lg-3">
			<div class="wv-input-group">
				<label for="wine-volume"><?php esc_html_e( 'Volume (ML)', 'wv-addon' ); ?><span class="ds-required">*</span></label>
				<input type="number" id="wine-volume" name="volume_ml"
				       class="wv-input" step="0.01" min="0" placeholder="0.75" required>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="wv-input-group">
				<label for="wine-production"><?php esc_html_e( 'Annual production (L)', 'wv-addon' ); ?></label>
				<select id="wine-production" name="annual_production_l" class="wv-input" required>
					<option value="" disabled selected>—</option>
					<?php foreach ( $production_ranges as $val => $label ) : ?>
						<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<!-- Alcohol, Sugar, Acidity, In‑stock -->
		<div class="col-6 col-lg-3">
			<div class="wv-input-group">
				<label for="wine-alcohol"><?php esc_html_e( 'Alcohol (%)', 'wv-addon' ); ?></label>
				<input type="number" id="wine-alcohol" name="alcohol_pct"
				       class="wv-input" step="1" min="0" max="100" required>
			</div>
		</div>
		<div class="col-6 col-lg-3">
			<div class="wv-input-group">
				<label for="wine-sugar"><?php esc_html_e( 'Sugar (%)', 'wv-addon' ); ?></label>
				<input type="number" id="wine-sugar" name="sugar_pct"
				       class="wv-input" step="1" min="0" max="100" required>
			</div>
		</div>
		<div class="col-6 col-lg-3">
			<div class="wv-input-group">
				<label for="wine-acidity"><?php esc_html_e( 'Acidity (%)', 'wv-addon' ); ?></label>
				<input type="number" id="wine-acidity" name="acidity_pct"
				       class="wv-input" step="1" min="0" max="100" required>
			</div>
		</div>
		<div class="col-6 col-lg-3">
			<div class="wv-input-group">
				<label for="wine-stock"><?php esc_html_e( 'In stock (L)', 'wv-addon' ); ?></label>
				<input type="number" id="wine-stock" name="current_stock_l"
				       class="wv-input" step="1" min="0">
			</div>
		</div>

		<!-- Trophy toggle -->
		<div class="col-12">
			<div class="alert wv-bg-sand wv-color-d py-8 px-12 mb-8 br-4 fs-14 d-none">
				<?php esc_html_e( 'Submit product for 2025 Open Balkan Wine Trophy', 'wv-addon' ); ?>
			</div>
			<div class="form-check mb-16 d-flex align-items-center justify-content-between p-12 mt-12 wv-bg-c_50 br-8">
				<label class="form-check-label me-8 wv-color-w" for="wine-trophy"><?php esc_html_e( 'Submit product for 2025 Open Balkan Wine Trophy', 'wv-addon' ); ?></label>
				<input class="form-check-input" type="checkbox" id="wine-trophy" name="submit_for_trophy" value="1">
			</div>
		</div>

	</div><!-- /.row -->
</div>
