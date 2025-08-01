<?php
/**
 * Product‑Spirits form (modal, right column)
 *
 * @package Wv_Addon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------- helper data ---------- */
$spirits_categories = [
	'rakija'        => [ 'Plum', 'Quince', 'Apricot', 'Pear', 'Grape', 'Apple', 'Cherry', 'Raspberry', 'Other rakija' ],
	'aromatized'    => [ 'Fruit liqueur', 'Herbal liqueur', 'Klekovača', 'Walnut', 'Travarica', 'Honey', 'Other aromatized' ],
	'other_spirit'  => [ 'Gin', 'Vodka', 'Whiskey', 'Brandy', 'Rum', 'Other spirits' ],
];

$group_labels = [
	'rakija'       => __( 'Rakija',        'wv-addon' ),
	'aromatized'   => __( 'Aromatized',    'wv-addon' ),
	'other_spirit' => __( 'Other spirits', 'wv-addon' ),
];

$production_ranges = [
	'0-10000'       => '1 – 10 000',
	'10000-50000'   => '10 000 – 50 000',
	'50000-250000'  => '50 000 – 250 000',
	'250000-500000' => '250 000 – 500 000',
	'500000+'       => '500 000+',
];

$aging_options = [
	'steel'     => __( 'Stainless steel', 'wv-addon' ),
	'oak'       => __( 'Oak barrel',      'wv-addon' ),
	'glass'     => __( 'Glass',           'wv-addon' ),
	'amphora'   => __( 'Amphora',         'wv-addon' ),
	'other'     => __( 'Other',           'wv-addon' ),
];
?>

<div class="wv-spirits-fields">
	<div class="row gy-8">

		<!-- Product category* -->
		<div class="col-12">
			<div class="wv-input-group">
				<label for="spirits-category"><?php esc_html_e( 'Product category', 'wv-addon' ); ?><span class="ds-required">*</span></label>
				<select id="spirits-category" name="category" class="wv-input" required>
					<option value="" disabled selected>— <?php esc_html_e( 'Select category', 'wv-addon' ); ?> —</option>
					<?php foreach ( $spirits_categories as $group => $opts ) : ?>
						<optgroup label="<?php echo esc_attr( $group_labels[ $group ] ); ?>">
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

		<!-- Country of origin* -->
		<div class="col-12">
			<div class="wv-input-group">
				<label for="spirits-country"><?php esc_html_e( 'Country of origin', 'wv-addon' ); ?><span class="ds-required">*</span></label>
				<input type="text" id="spirits-country" name="country" class="wv-input" required>
			</div>
		</div>

		<!-- Aging process* -->
		<div class="col-12">
			<div class="wv-input-group">
				<label for="spirits-aging"><?php esc_html_e( 'Aging process', 'wv-addon' ); ?><span class="ds-required">*</span></label>
				<select id="spirits-aging" name="aging_process" class="wv-input" required>
					<option value="" disabled selected>—</option>
					<?php foreach ( $aging_options as $val => $label ) : ?>
						<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<!-- Vintage, Volume, Annual production -->
		<div class="col-6 col-lg-3">
			<div class="wv-input-group">
				<label for="spirits-year"><?php esc_html_e( 'Year', 'wv-addon' ); ?><span class="ds-required">*</span></label>
				<input type="number" id="spirits-year" name="vintage_year"
				       class="wv-input" min="1900" max="<?php echo esc_attr( date( 'Y' ) ); ?>" required>
			</div>
		</div>
		<div class="col-6 col-lg-3">
			<div class="wv-input-group">
				<label for="spirits-volume"><?php esc_html_e( 'Volume (ML)', 'wv-addon' ); ?><span class="ds-required">*</span></label>
				<input type="number" id="spirits-volume" name="volume_ml"
				       class="wv-input" step="0.01" min="0" placeholder="0.75" required>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="wv-input-group">
				<label for="spirits-production"><?php esc_html_e( 'Annual production (L)', 'wv-addon' ); ?><span class="ds-required">*</span></label>
				<select id="spirits-production" name="annual_production_l" class="wv-input" required>
					<option value="" disabled selected>—</option>
					<?php foreach ( $production_ranges as $val => $label ) : ?>
						<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<!-- Alcohol, Sugar, Stock -->
		<div class="col-6 col-lg-3">
			<div class="wv-input-group">
				<label for="spirits-alcohol"><?php esc_html_e( 'Alcohol (%)', 'wv-addon' ); ?></label>
				<input type="number" id="spirits-alcohol" name="alcohol_pct"
				       class="wv-input" step="0.1" min="0" required>
			</div>
		</div>
		<div class="col-6 col-lg-3">
			<div class="wv-input-group">
				<label for="spirits-sugar"><?php esc_html_e( 'Sugar (%)', 'wv-addon' ); ?></label>
				<input type="number" id="spirits-sugar" name="sugar_pct"
				       class="wv-input" step="0.1" min="0" required>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="wv-input-group">
				<label for="spirits-stock"><?php esc_html_e( 'Currently in stock (L)', 'wv-addon' ); ?></label>
				<input type="number" id="spirits-stock" name="current_stock_l"
				       class="wv-input" step="0.01" min="0">
			</div>
		</div>

		<!-- Trophy toggle -->
		<div class="col-12">
			<div class="form-check mb-16 d-flex align-items-center justify-content-between p-12 mt-12 wv-bg-c_50 br-8">
				<label class="form-check-label me-8 wv-color-w" for="spirits-trophy">
					<?php esc_html_e( 'Submit product for 2025 Open Balkan Rakija Trophy', 'wv-addon' ); ?>
				</label>
				<input class="form-check-input" type="checkbox" id="spirits-trophy" name="submit_for_trophy" value="1">
			</div>
		</div>

	</div><!-- /.row -->
</div>
