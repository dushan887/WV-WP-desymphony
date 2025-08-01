<?php
/**
 * General tab ( #wv‑general )
 * Called from the main dashboard/profile view.
 *
 * @var array $args  (optional) – when the parent uses get_template_part().
 */
defined( 'ABSPATH' ) || exit;

use Desymphony\Helpers\DS_Utils as Utils;

// Get the current user ID.
$user_id = get_current_user_id();

// Retrieve user meta values.
$participation_model  = get_user_meta( $user_id, 'wv_participationModel', true );
$user_category        = get_user_meta( $user_id, 'wv_userCategory', true );
$profile_picture      = get_user_meta( $user_id, 'wv_user-avatar', true );
if (!empty( $profile_picture ) ) {
    $profile_picture = str_replace('-200.jpg', '-400.jpg', $profile_picture);
}
$profile_logo      = get_user_meta( $user_id, 'wv_user-logo', true );
if (!empty( $profile_logo ) ) {
    $profile_logo = str_replace('-200.jpg', '-400.jpg', $profile_logo);
}
$company_description  = get_user_meta( $user_id, 'wv_companyDescription', true );

// Additional meta for Company Information.
$company_name         = get_user_meta( $user_id, 'wv_company_name', true );
$company_pobRegion    = get_user_meta( $user_id, 'wv_company_pobRegion', true );
$company_country      = get_user_meta( $user_id, 'wv_company_country', true );
$company_email        = get_user_meta( $user_id, 'wv_company_email', true );
$company_city         = get_user_meta( $user_id, 'wv_company_city', true );
$company_website      = get_user_meta( $user_id, 'wv_company_website', true );
$company_address      = get_user_meta( $user_id, 'wv_company_address', true );
$annual_production    = get_user_meta( $user_id, 'wv_annual_production', true );
$current_stock        = get_user_meta( $user_id, 'wv_current_stock', true );

// var_dump( '<pre>', get_user_meta( $user_id), '</pre>' ); 

// Retrieve user meta values for Company Financial Information.
$company_id_registry_number  = get_user_meta( $user_id, 'wv_company_idRegistryNumber', true );
$company_vat_registry_number = get_user_meta( $user_id, 'wv_company_vatRegistryNumber', true );
$company_iban                = get_user_meta( $user_id, 'wv_company_iban', true );
$company_foreign_bank        = get_user_meta( $user_id, 'wv_company_foreignBank', true );
$company_domestic_bank       = get_user_meta( $user_id, 'wv_company_domesticBank', true );
$company_foreign_account_number = get_user_meta( $user_id, 'wv_company_foreignAccountNumber', true );
$company_domestic_account_number = get_user_meta( $user_id, 'wv_company_domesticAccountNumber', true );
$company_foreign_swift       = get_user_meta( $user_id, 'wv_company_foreignSwift', true );
$company_domestic_swift      = get_user_meta( $user_id, 'wv_company_domesticSwift', true );

// Retrieve user meta values for Representative Credentials.
$rep_first_name    = get_user_meta( $user_id, 'wv_firstName', true );
$rep_last_name     = get_user_meta( $user_id, 'wv_lastName', true );
$rep_nationality   = get_user_meta( $user_id, 'wv_nationality', true );
$rep_email         = get_user_meta( $user_id, 'wv_email', true );
$rep_occupation    = get_user_meta( $user_id, 'wv_professionalOccupation', true );
$rep_experience    = get_user_meta( $user_id, 'wv_yearsOfExperience', true );
$rep_position      = get_user_meta( $user_id, 'wv_positionInCompany', true );
$rep_contact       = get_user_meta( $user_id, 'wv_contactTelephone', true );
$rep_whatsapp      = get_user_meta( $user_id, 'wv_exhibitor_rep_whatsapp', true );
$rep_viber         = get_user_meta( $user_id, 'wv_exhibitor_rep_viber', true );

// Format the registration date.
$registered_date = get_userdata( $user_id )->user_registered;
$registered_date = date_i18n( 'F jS, Y', strtotime( $registered_date ) );

// For Status – for now we use a static label.
$status = !empty(get_user_meta( $user_id, 'wv_status', true )) ? get_user_meta( $user_id, 'wv_status', true ) : 'Pending';
?>

 <!-- Account Info Row -->
<div class="wv-block px-24 pt-32 pb-16 br-12 br-t-0 wv-bg-v wv-user-bg-g">
    <div class="row justify-content-between wv-align-items-center">
        <div class="col-6 col-lg-3 pb-16 pb-lg-0">                    
            <span class="d-block fs-14 ls-3 wv-color-c_10 wv-color-ww"><?php esc_html_e( 'PARTICIPATION MODEL', DS_THEME_TEXTDOMAIN ); ?></span>
            <?php if ( Utils::is_buyer() ) : ?>
                <span class="d-block fs-18 wv-fw-500 wv-color-w">Professional Buyer</span>
            <?php else : ?>
                <span class="d-block fs-18 wv-fw-500 wv-color-w"><?php echo esc_html( $participation_model ); ?></span>
            <?php endif; ?>
        </div>
        <div class="col-6 col-lg-3 pb-16 pb-lg-0">
            <?php if (Utils::get_visitor_participation() !== 'Public Visitor' ) : ?>
            <span class="d-block fs-14 ls-3 wv-color-c_10 wv-color-ww"><?php esc_html_e( 'CATEGORY', DS_THEME_TEXTDOMAIN ); ?></span>
            <span class="d-block fs-18 wv-fw-500 wv-color-w"><?php echo esc_html( $user_category ); ?></span>
            <?php endif; ?>
        </div>
        <div class="col-6 col-lg-3 pb-16 pb-lg-0">
            <span class="d-block fs-14 ls-3 wv-color-c_10 wv-color-ww"><?php esc_html_e( 'REGISTERED', DS_THEME_TEXTDOMAIN ); ?></span>
            <span class="d-block fs-18 wv-fw-500 wv-color-w"><?php echo esc_html( $registered_date ); ?></span>
        </div>
        <div class="col-6 col-lg-3 pb-16 pb-lg-0">
            <span class="d-block fs-14 ls-3 wv-color-c_10 wv-color-ww"><?php esc_html_e( 'STATUS', DS_THEME_TEXTDOMAIN ); ?></span>
            <span class="d-block fs-18 wv-fw-500 wv-color-w"><?php echo esc_html( $status ); ?></span>
        </div>
    </div>
</div>


<?php if ( Utils::has_wvhb_support() ) : ?>

<?php
	$cat_raw = Utils::wvhb_support_category();
	$cat_key = strtoupper( preg_replace( '/[^IV]+/i', '', $cat_raw ) ); // I, II, III, IV keys

	$defs = [
		'IV' => [
			'benefits' => [
				'Round-trip airplane ticket',
				'Transfer (Airport – Hotel – Hotel – Fair)',
				'Hotel accommodation',
				'Fair admission fee',
			],
			'attend'  => 15,
			'request' => 20,
		],
		'III' => [
			'benefits' => [
				'Round-trip airplane ticket',
				'Transfer (Airport – Hotel – Hotel – Fair)',
				'Fair admission fee',
			],
			'attend'  => 12,
			'request' => 15,
		],
		'II' => [
			'benefits' => [
				'Hotel accommodation',
				'Transfer (Hotel – Fair)',
				'Fair admission fee',
			],
			'attend'  => 9,
			'request' => 12,
		],
		'I' => [
			'benefits' => [
				'Fair admission fee',
			],
			'attend'  => 5,
			'request' => 10,
		],
	];

	$cat_def = $defs[ $cat_key ] ?? null;
	if ( ! $cat_def ) { return; } // safety
?>
<!-- COMPANY INFORMATION Section -->
<form id="wv-company-info-cat" class="wv-form-section" method="post">
	<div class="row mt-12">
		<div class="col-12">
			<div class="wv-card wv-flex-column br-12 wv-bg-w">
				<div class="wv-card-header p-24 d-flex justify-content-between align-items-center border-bottom">
					<h4 class="m-0 fs-20 fw-600 ls-4 lh-1-5">
						<?php esc_html_e( 'WINE VISION HOSTED BUYERS PROGRAM', DS_THEME_TEXTDOMAIN ); ?>
					</h4>
				</div>

				<div class="wv-card-body p-24">
					<div class="row">
						<!-- Granted category -->
						<div class="col-lg-6">
							<div class="wv-input-group">
								<label class="d-block my-8">Category granted</label>
								<div class="d-block wv-bg-c_5 p-24 br-12 text-center">
									<img src="https://winevisionfair.com/wp-content/uploads/2025/06/Hosted_Buyers_logo.png" class="img-fluid" alt="">
									<div class="d-flex align-items-center justify-content-center wv-bg-g br-8 p-12 wv-color-w wv-color-ww text-uppercase fs-16">
										<strong><?php echo esc_html( $cat_raw ); ?></strong>
										<span class="ls-4 ms-8">SUPPORT GRANTED</span>
										<span class="wv wv_check-70-sq fs-20 wv-i-wg ms-8"><span class="path1"></span><span class="path2"></span></span>
									</div>
								</div>
							</div>
						</div>

						<!-- Benefits & obligations -->
						<div class="col-lg-6">
							<div class="wv-input-group">
								<label class="d-block my-8">Category benefits and obligations</label>

								<!-- BENEFITS -->
								<div class="d-block wv-bg-c_5 px-12 py-4 fs-14 br-12 mb-12">
									<div class="d-flex align-items-center justify-content-between py-8 border-bottom wv-bc-c_20">
										<strong class="ls-3">BENEFITS</strong>
										<span class="ls-3 ms-4 wv-color-c_50">COVERING EXPENSES FOR</span>
									</div>
									<?php foreach ( $cat_def['benefits'] as $idx => $benefit ) : ?>
										<div class="d-flex align-items-center justify-content-between py-8<?php echo $idx + 1 < count( $cat_def['benefits'] ) ? ' border-bottom wv-bc-c_20' : ''; ?>">
											<span class="wv-color-c_50"><?php echo esc_html( $benefit ); ?></span>
											<span class="wv wv_check-70-sq fs-16 wv-i-gw ms-4"><span class="path1"></span><span class="path2"></span></span>
										</div>
									<?php endforeach; ?>
								</div>

								<!-- OBLIGATIONS -->
								<div class="d-block wv-bg-c_90 px-12 py-4 fs-14 br-12">
									<div class="d-flex align-items-center justify-content-between py-8 border-bottom wv-bc-c_80">
										<strong class="ls-3 wv-color-c_20">BENEFITS</strong>
										<span class="ls-3 ms-4 wv-color-c_20">B2B MEETINGS ENGAGEMENT</span>
									</div>
									<div class="d-flex align-items-center justify-content-between py-8 border-bottom wv-bc-c_80">
										<span class="wv-color-c_20">Number of confirmed meetings attended</span>
										<span class="wv-bg-c_5 ms-4 fs-14 br-32 fw-600 d-flex align-items-center justify-content-center lh-1" style="width: 24px; height: 24px;">
											<?php echo esc_html( $cat_def['attend'] ); ?>
										</span>
									</div>
									<div class="d-flex align-items-center justify-content-between py-8">
										<span class="wv-color-c_20">Minimum of meeting requests sent</span>
										<span class="wv-bg-c_5 ms-4 fs-14 br-32 fw-600 d-flex align-items-center justify-content-center lh-1" style="width: 24px; height: 24px;">
											<?php echo esc_html( $cat_def['request'] ); ?>
										</span>
									</div>
								</div>

							</div> <!-- /input-group -->
						</div> <!-- /col-lg-6 -->
					</div> <!-- /row -->
				</div> <!-- /card-body -->
			</div> <!-- /card -->
		</div> <!-- /col-12 -->
	</div> <!-- /row -->
</form>

<?php endif; ?>


<?php if (Utils::get_visitor_participation() !== 'Public Visitor' ) : ?>
<!-- COMPANY INFORMATION Section -->
<form id="wv-company-information-form" class="wv-form-section" method="post">
    <div class="row mt-12">
        <div class="col-12">
            <div class="wv-card wv-flex-column br-12 wv-bg-w">
                <div class="wv-card-header p-24 d-flex justify-content-between align-items-center border-bottom">
                    <h4 class="m-0 fs-20 fw-600 ls-4 lh-1-5"><?php esc_html_e( 'COMPANY INFORMATION', DS_THEME_TEXTDOMAIN ); ?></h4>
                    <!-- Initial button text is "Edit" -->

                    <?php if ( Utils::is_admin_verified() ) : ?>
                        <button type="button" class="wv-button wv-button-pill wv-button-sm wv-button-edit edit-toggle"><?php esc_html_e( 'Edit', DS_THEME_TEXTDOMAIN ); ?></button>
                    <?php endif; ?>
                </div>
                <div class="wv-card-body p-24">
                    <div class="row">
                        <!-- Company logo column -->
                        <div class="col-lg-3 col-edit-lg-6 col-view-lg-3">
                            <div class="wv-input-group">
                                <label class="d-block my-8">Company logo</label>

                                <img src="<?php echo esc_url( $profile_logo ); ?>"
                                    alt=""
                                    class="img-fluid wv-company-logo br-8 border border-4 wv-bc-c_20 wv-view-only">

                                <div class="wv-edit-only">
                                    <?php
                                    $field_args = [
                                        'field_name'   => 'wv_user-logo',
                                        'field_id'     => 'wv_user-logo-hidden',
                                        'current_url'  => $profile_logo,
                                        'label'        => __('Company Logo', DS_THEME_TEXTDOMAIN),
                                        'max_size_mb'  => 2,
                                        'profile_key'  => 'company_logo',
                                        'aspect_ratio' => '1:1',
                                        'upload_action'=> 'wv_crop_upload',
                                        'placeholders' => [ 'user-id' => get_current_user_id() ],
                                    ];
                                    $partial_path = DS_THEME_DIR . '/inc/public/views/partials/form-fields/cropper-field.php';
                                    $args = $field_args;
                                    file_exists( $partial_path ) && include $partial_path;
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9 col-edit-lg-6 col-view-lg-9">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_companyDescription"><?php esc_html_e( 'Company description', DS_THEME_TEXTDOMAIN ); ?></label>
                                <textarea id="wv_companyDescription" name="wv_companyDescription" rows="12" maxlength="700" disabled><?php echo esc_textarea( $company_description ); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End Card -->
        </div> <!-- End Column -->
    </div>
</form>
<?php endif; ?>
                            

<?php if (Utils::get_visitor_participation() !== 'Public Visitor' ) : ?>
<!-- COMPANY CREDENTIALS Section -->
<form id="wv-company-credentials-form" class="wv-form-section" method="post">

    <div class="row mt-12">
        <div class="col-12">
            <div class="wv-card wv-flex-column br-12 wv-bg-w">
                <div class="wv-card-header p-24 d-flex justify-content-between align-items-center border-bottom">
                    <h4 class="m-0 fs-20 fw-600 ls-4 lh-1-5"><?php esc_html_e( 'COMPANY CREDENTIALS', DS_THEME_TEXTDOMAIN ); ?></h4>
                    <?php if ( Utils::is_admin_verified() ) : ?>
                        <button type="button" class="wv-button wv-button-pill wv-button-sm wv-button-edit edit-toggle"><?php esc_html_e( 'Edit', DS_THEME_TEXTDOMAIN ); ?></button>
                    <?php endif; ?>
                </div>
                <div class="wv-card-body p-24">
                    <div class="row">
                        <!-- Company Full Name -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_name"><?php esc_html_e( 'Official company name in English', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input class="wv-w-100" type="text" id="wv_company_name" name="wv_company_name" value="<?php echo esc_attr( $company_name ); ?>" required disabled>
                            </div>
                        </div>
                        <!-- P.O.B. / Area / Municipality / Region -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_pobRegion"><?php esc_html_e( 'P.O.B. / area / municipality / region', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input class="wv-w-100" type="text" id="wv_company_pobRegion" name="wv_company_pobRegion" value="<?php echo esc_attr( $company_pobRegion ); ?>" <?php echo Utils::is_exhibitor() ? 'required' : ''; ?> disabled>
                            </div>
                        </div>
                        <!-- Country of Residence -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_country">
                                    <?php esc_html_e( 'Country of residence', DS_THEME_TEXTDOMAIN ); ?>
                                </label>

                                <select class="wv-w-100 form-select"
                                        id="wv_company_country"
                                        name="wv_company_country"
                                        required>
                                    <?php
                                    $countries = include DS_THEME_DIR . '/inc/public/views/partials/countries.php';
                                    $countries = is_array( $countries ) ? $countries : [];

                                    foreach ( $countries as $country ) : ?>
                                        <option value="<?php echo esc_attr( $country ); ?>"
                                                <?php selected( $company_country, $country ); ?>>
                                            <?php echo esc_html( $country ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- E-mail Address -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_email"><?php esc_html_e( 'E-mail address', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input class="wv-w-100" type="email" id="wv_company_email" name="wv_company_email" value="<?php echo esc_attr( $company_email ); ?>" required disabled>
                            </div>
                        </div>
                        <!-- City of Residence -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_city"><?php esc_html_e( 'City of residence', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input class="wv-w-100" type="text" id="wv_company_city" name="wv_company_city" value="<?php echo esc_attr( $company_city ); ?>" required disabled>
                            </div>
                        </div>
                        <!-- Website -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_website"><?php esc_html_e( 'Website', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input class="wv-w-100" type="text" id="wv_company_website" name="wv_company_website" value="<?php echo esc_attr( $company_website ); ?>" disabled>
                            </div>
                        </div>
                        <!-- Address -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_address"><?php esc_html_e( 'Address (street and number)', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input class="wv-w-100" type="text" id="wv_company_address" name="wv_company_address" value="<?php echo esc_attr( $company_address ); ?>" <?php echo Utils::is_exhibitor() ? 'required' : ''; ?> disabled>
                            </div>
                        </div>

                        <?php if ( Utils::is_exhibitor() && in_array( Utils::get_exhibitor_category(), [ 'Winemaker', 'Winemaker & Distiller' ], true ) ) : ?>

                        <!-- Annual Production and Current Stock -->
                        <div class="col-lg-3">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_annualProductionLiters">
                                    <?php esc_html_e( 'Annual production (Bottles)', DS_THEME_TEXTDOMAIN ); ?>
                                </label>

                                <select class="wv-w-100 form-select"
                                        id="wv_annualProductionLiters"
                                        name="wv_annualProductionLiters"
                                        required>
                                    <?php
                                    $production_ranges = [
                                        '0-10000'       => '1 – 10 000',
                                        '10000-50000'   => '10 000 – 50 000',
                                        '50000-250000'  => '50 000 – 250 000',
                                        '250000-500000' => '250 000 – 500 000',
                                        '500000+'       => '500 000+',
                                    ];

                                    foreach ( $production_ranges as $val => $label ) : ?>
                                        <option value="<?php echo esc_attr( $val ); ?>"
                                            <?php selected( $annual_production, $val ); ?>>
                                            <?php echo esc_html( $label ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_currentStockLiters"><?php esc_html_e( 'Currently in stock (Bottles)', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input class="wv-w-100" type="text" id="wv_currentStockLiters" name="wv_currentStockLiters" value="<?php echo esc_attr( $current_stock ); ?>" disabled>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div> <!-- End Row -->

                    <?php if ( Utils::is_exhibitor() ) : ?>
                    <div class="row mt-24">
                        <!-- ID Registry Number -->
                        <div class="col-lg-3">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_idRegistryNumber"><?php esc_html_e( 'ID registry number', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_company_idRegistryNumber" name="wv_company_idRegistryNumber" value="<?php echo esc_attr( $company_id_registry_number ); ?>" required disabled>
                            </div>
                        </div>
                        <!-- VAT Registry Number -->
                        <div class="col-lg-3">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_vatRegistryNumber"><?php esc_html_e( 'VAT registry number', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_company_vatRegistryNumber" name="wv_company_vatRegistryNumber" value="<?php echo esc_attr( $company_vat_registry_number ); ?>" required disabled>
                            </div>
                        </div>
                        <!-- IBAN -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_iban"><?php esc_html_e( 'IBAN', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_company_iban" name="wv_company_iban" value="<?php echo esc_attr( $company_iban ); ?>" required disabled>
                            </div>
                        </div>
                        <!-- Foreign Exchange Correspondent Bank -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_foreignBank"><?php esc_html_e( 'Foreign exchange correspondent bank', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_company_foreignBank" name="wv_company_foreignBank" value="<?php echo esc_attr( $company_foreign_bank ); ?>" required disabled>
                            </div>
                        </div>
                        <!-- Domestic Exchange Bank -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_domesticBank"><?php esc_html_e( 'Domestic exchange bank', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_company_domesticBank" name="wv_company_domesticBank" value="<?php echo esc_attr( $company_domestic_bank ); ?>" required disabled>
                            </div>
                        </div>
                        <!-- Foreign Exchange Account Number -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_foreignAccountNumber"><?php esc_html_e( 'Foreign exchange account number', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_company_foreignAccountNumber" name="wv_company_foreignAccountNumber" value="<?php echo esc_attr( $company_foreign_account_number ); ?>" required disabled>
                            </div>
                        </div>
                        <!-- Domestic Exchange Account Number -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_domesticAccountNumber"><?php esc_html_e( 'Domestic exchange account number', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_company_domesticAccountNumber" name="wv_company_domesticAccountNumber" value="<?php echo esc_attr( $company_domestic_account_number ); ?>" required disabled>
                            </div>
                        </div>
                        <!-- Foreign Exchange Swift Code -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_foreignSwift"><?php esc_html_e( 'Foreign exchange swift code', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_company_foreignSwift" name="wv_company_foreignSwift" value="<?php echo esc_attr( $company_foreign_swift ); ?>" required disabled>
                            </div>
                        </div>
                        <!-- Beneficiary Swift Code -->
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_company_domesticSwift"><?php esc_html_e( 'Beneficiary swift code', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_company_domesticSwift" name="wv_company_domesticSwift" value="<?php echo esc_attr( $company_domestic_swift ); ?>" required disabled>
                            </div>
                        </div>
                    </div> <!-- End Row -->
                    <?php endif; // End check for exhibitor participation ?>
                    <div class="row mt-24">
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_socInstagram"><?php esc_html_e( 'Instagram Profile', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_socInstagram" name="wv_socInstagram" value="<?php echo esc_attr( get_user_meta( $user_id, 'wv_socInstagram', true ) ); ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_socLinkedin"><?php esc_html_e( 'LinkedIn Profile', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_socLinkedin" name="wv_socLinkedin" value="<?php echo esc_attr( get_user_meta( $user_id, 'wv_socLinkedin', true ) ); ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_socFacebook"><?php esc_html_e( 'Facebook Profile', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_socFacebook" name="wv_socFacebook" value="<?php echo esc_attr( get_user_meta( $user_id, 'wv_socFacebook', true ) ); ?>" disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <label class="d-block my-8" for="wv_socX"><?php esc_html_e( 'X Profile', DS_THEME_TEXTDOMAIN ); ?></label>
                                <input type="text" id="wv_socX" name="wv_socX" value="<?php echo esc_attr( get_user_meta( $user_id, 'wv_socX', true ) ); ?>" disabled>
                            </div>
                        </div>
                        
                    </div> <!-- End Row -->
                </div>
            </div> <!-- End Card -->
        </div>
    </div>


</form>
<?php endif; // End check for participation model ?>

<!-- REPRESENTATIVE CREDENTIALS Section -->
<form id="wv-representative-credentials-form" class="wv-form-section" method="post">
    <div class="row mt-12">
        <div class="col-12">
            <div class="wv-card wv-flex-column br-12 wv-bg-w">
                <div class="wv-card-header p-24 d-flex justify-content-between align-items-center border-bottom">
                    <h4 class="m-0 fs-20 fw-600 ls-4 lh-1-5">
                        <?php if (Utils::get_visitor_participation() !== 'Public Visitor' ) : ?>
                        <?php esc_html_e( 'REPRESENTATIVE CREDENTIALS', DS_THEME_TEXTDOMAIN ); ?>
                        <?php else : ?>
                        <?php esc_html_e( 'VISITOR CREDENTIALS', DS_THEME_TEXTDOMAIN ); ?>
                        <?php endif; ?>

                    </h4>
                    <?php if ( Utils::is_admin_verified() ) : ?>
                        <button type="button" class="wv-button wv-button-pill wv-button-sm wv-button-edit edit-toggle"><?php esc_html_e( 'Edit', DS_THEME_TEXTDOMAIN ); ?></button>
                    <?php endif; ?>
                </div>
                <div class="wv-card-body p-24">
                    <div class="row justify-content-between">

                        <!-- Profile picture column -->
                        <div class="col-lg-3 col-edit-lg-6 col-view-lg-3">
                            <div class="wv-input-group">
                                <label class="d-block my-8">Profile picture</label>

                                <!-- preview ➜ view‑only -->
                                <img src="<?php echo esc_url( $profile_picture ); ?>"
                                    alt=""
                                    class="img-fluid wv-company-logo br-8 border border-4 wv-bc-c_20 wv-view-only">

                                <!-- cropper ➜ edit‑only -->
                                <div class="wv-edit-only">
                                    <?php
                                    $field_args = [
                                        'field_name'   => 'wv_user-avatar',
                                        'field_id'     => 'wv_user-avatar-hidden',
                                        'current_url'  => $profile_picture,
                                        'label'        => __('Profile Image', DS_THEME_TEXTDOMAIN),
                                        'max_size_mb'  => 2,
                                        'profile_key'  => 'profile',
                                        'aspect_ratio' => '1:1',
                                        'upload_action'=> 'wv_crop_upload',
                                        'placeholders' => [ 'user-id' => get_current_user_id() ],
                                    ];
                                    $partial_path = DS_THEME_DIR . '/inc/public/views/partials/form-fields/cropper-field.php';
                                    $args = $field_args;
                                    file_exists( $partial_path ) && include $partial_path;
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-lg-offset-3 col-edit-lg-6 col-view-lg-6">
                            <div class="row">
                            <!-- First Name -->
                            <div class="col-12">
                                <div class="wv-input-group">
                                    <label class="d-block my-8" for="wv_firstName"><?php esc_html_e( 'First name', DS_THEME_TEXTDOMAIN ); ?></label>
                                    <input type="text" id="wv_firstName" name="wv_firstName" value="<?php echo esc_attr( $rep_first_name ); ?>" required disabled>
                                </div>
                            </div>
                            <!-- Last Name -->
                            <div class="col-12">
                                <div class="wv-input-group">
                                    <label class="d-block my-8" for="wv_lastName"><?php esc_html_e( 'Last name', DS_THEME_TEXTDOMAIN ); ?></label>
                                    <input type="text" id="wv_lastName" name="wv_lastName" value="<?php echo esc_attr( $rep_last_name ); ?>" required disabled>
                                </div>
                            </div>
                            <?php if (Utils::get_visitor_participation() !== 'Public Visitor' ) : ?>
                            <!-- Nationality -->
                            <div class="col-12">
                                <div class="wv-input-group">
                                    <label class="d-block my-8" for="wv_nationality">
                                        <?php esc_html_e( 'Country of residence', DS_THEME_TEXTDOMAIN ); ?>
                                    </label>

                                    <select class="wv-w-100 form-select"
                                            id="wv_nationality"
                                            name="wv_nationality"
                                            required>
                                        <?php
                                        $countries = include DS_THEME_DIR . '/inc/public/views/partials/countries.php';
                                        $countries = is_array( $countries ) ? $countries : [];

                                        foreach ( $countries as $country ) : ?>
                                            <option value="<?php echo esc_attr( $country ); ?>"
                                                    <?php selected( $company_country, $country ); ?>>
                                                <?php echo esc_html( $country ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Position in the Company -->
                            <div class="col-12">
                                <div class="wv-input-group">
                                    <label class="d-block my-8" for="wv_positionInCompany"><?php esc_html_e( 'Position in the company', DS_THEME_TEXTDOMAIN ); ?></label>
                                    <input type="text" id="wv_positionInCompany" name="wv_positionInCompany" value="<?php echo esc_attr( $rep_position ); ?>" disabled>
                                </div>
                            </div>
                            <?php endif; // End check for participation model ?>
                            <!-- Professional Occupation -->
                            <div class="col-12">
                                <div class="wv-input-group">
                                    <label class="d-block my-8" for="wv_professionalOccupation">
                                        <?php if (Utils::get_visitor_participation() !== 'Public Visitor' ) : ?>
                                            <?php esc_html_e( 'Professional occupation', DS_THEME_TEXTDOMAIN ); ?></label>
                                        <?php else : ?>
                                            <?php esc_html_e( 'Occupation', DS_THEME_TEXTDOMAIN ); ?></label>
                                        <?php endif; ?>
                                    <input type="text" id="wv_professionalOccupation" name="wv_professionalOccupation" value="<?php echo esc_attr( $rep_occupation ); ?>" disabled>
                                </div>
                            </div>
                            <?php if (Utils::get_visitor_participation() !== 'Public Visitor' ) : ?>
                            <!-- Years of Professional Experience -->
                            <div class="col-12">
                                <div class="wv-input-group">
                                    <label class="d-block my-8" for="wv_yearsOfExperience"><?php esc_html_e( 'Years of professional experience', DS_THEME_TEXTDOMAIN ); ?></label>
                                    <input type="text" id="wv_yearsOfExperience" name="wv_yearsOfExperience" value="<?php echo esc_attr( $rep_experience ); ?>" disabled>
                                </div>
                            </div>
                            <?php endif; // End check for participation model ?>
                            <?php if (Utils::get_visitor_participation() === 'Public Visitor') : ?>
                                <!-- Country of Residence -->
                                <div class="col-12">
                                    <div class="wv-input-group">
                                        <label class="d-block my-8" for="wv_countryOfResidence"><?php esc_html_e( 'Country of residence', DS_THEME_TEXTDOMAIN ); ?></label>
                                        <input type="text" id="wv_countryOfResidence" name="wv_countryOfResidence" value="<?php echo esc_attr( get_user_meta( $user_id, 'wv_countryOfResidence', true ) ); ?>" disabled>
                                    </div>
                                </div>
                                <!-- City of Residence -->
                                <div class="col-12">
                                    <div class="wv-input-group">
                                        <label class="d-block my-8" for="wv_cityOfResidence"><?php esc_html_e( 'City of residence', DS_THEME_TEXTDOMAIN ); ?></label>
                                        <input type="text" id="wv_cityOfResidence" name="wv_cityOfResidence" value="<?php echo esc_attr( get_user_meta( $user_id, 'wv_cityOfResidence', true ) ); ?>" disabled>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <!-- Representative E-mail Address -->
                            <div class="col-12">
                                <div class="wv-input-group">
                                    <label class="d-block my-8" for="wv_email"><?php esc_html_e( 'E-mail address', DS_THEME_TEXTDOMAIN ); ?></label>
                                    <input type="email" id="wv_email" name="wv_email" value="<?php echo esc_attr( $rep_email ); ?>" disabled readonly>
                                </div>
                            </div>
                            <?php if (Utils::get_visitor_participation() !== 'Public Visitor' ) : ?>
                            <!-- Contact (Telephone) & Additional Options -->
                            <div class="col-12">
                                <div class="wv-input-group">
                                    <label class="d-block my-8" for="wv_contactTelephone"><?php esc_html_e( 'Contact (telephone number)', DS_THEME_TEXTDOMAIN ); ?></label>
                                    <div class="wv-phone-input">                                              
                                        <input type="tel" name="wv_contactTelephone" id="wv_contactTelephone" value="<?php echo esc_attr( $rep_contact ); ?>" required disabled>
                                        
                                    </div>
                                </div>
                            </div>
                            <?php endif; // End check for participation model ?>
                            </div>
                        
                        </div>

                    </div> <!-- End Row -->
                </div>
            </div> <!-- End Card -->
        </div>
    </div>
</form>