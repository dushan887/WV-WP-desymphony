<?php
/**
 * Renders the front‑end profile overview – COMPANY INFORMATION block.
 *
 * @param int  $uid       User‑ID whose data is shown.
 * @param bool $editable  When TRUE the inputs will be editable (not used yet).
 */
function ds_render_profile_overview( int $uid, bool $editable = false ): void {

    // -----------------------------------------------------------------
    // Compact poly‑fill for PHP < 8
    // -----------------------------------------------------------------
    if ( ! function_exists( 'str_starts_with' ) ) {
        function str_starts_with( string $haystack, string $needle ): bool {
            return $needle !== '' && substr( $haystack, 0, strlen( $needle ) ) === $needle;
        }
    }

    
    /* ============================================================
    *  BLOCK 0 – USER INFO  (READ‑ONLY)
    * ============================================================
    */

    /* ──  meta pulled once  ───────────────────────────────────── */
    
    $field_of_work        = get_user_meta( $uid, 'wv_fieldOfWork',           true );
    $participation_model  = get_user_meta( $uid, 'wv_participationModel',    true );
    $user_category        = get_user_meta( $uid, 'wv_userCategory',          true );
    $user_cat_other       = get_user_meta( $uid, 'wv_userCategoryOtherDescription', true );
    $exhibiting_products  = get_user_meta( $uid, 'wv_exhibitingProducts',    true );

    $reasons_visiting     = (array) get_user_meta( $uid, 'wv_reasonsForVisiting',     true );
    $other_reasons        = get_user_meta( $uid, 'wv_otherReasonsForVisiting',        true );
    $points_interest      = (array) get_user_meta( $uid, 'wv_pointsOfInterest',       true );
    $reason_applying      = get_user_meta( $uid, 'wv_reasonForApplying',              true );

    /* ──  card markup  ───────────────────────────────────────── */
    ?>
    <div class="wv-form-section">
        <div class="row">
            <div class="col-12">
                <div class="wv-card wv-flex-column br-12 wv-bg-w">
                    <div class="wv-card-header p-24 border-bottom">
                        <h4 class="m-0 fs-20 fw-600 ls-4 lh-1-5">
                            <?php esc_html_e( 'USER INFORMATION', DS_THEME_TEXTDOMAIN ); ?>
                        </h4>
                    </div>

                    <div class="wv-card-body p-24">
                        <div class="row gy-12">

                            <?php if ( \Desymphony\Helpers\DS_Utils::is_exhibitor($uid) ) : ?>

                                <?php
                                $items = [
                                    __( 'Wine, Spirits or Food?', DS_THEME_TEXTDOMAIN )     => $field_of_work,
                                    __( 'Applying as',            DS_THEME_TEXTDOMAIN )     => $participation_model,
                                    __( 'User category',          DS_THEME_TEXTDOMAIN )     => $user_category,
                                    __( 'Exhibiting products',    DS_THEME_TEXTDOMAIN )     => $exhibiting_products,
                                ];
                                foreach ( $items as $label => $value ) : ?>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="wv-input-group">
                                            <label class="d-block my-4 small text-muted"><?php echo esc_html( $label ); ?></label>
                                            <span class="d-block fw-500"><?php echo esc_html( $value ?: '—' ); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php if ( $user_cat_other ) : ?>
                                    <div class="col-12">
                                        <div class="wv-input-group">
                                            <label class="d-block my-4 small text-muted">
                                                <?php esc_html_e( 'Category description (Other)', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <p class="mb-0"><?php echo nl2br( esc_html( $user_cat_other ) ); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            <?php else : /* Buyer / Visitor — all fields read‑only */ ?>

                                <?php
                                $items = [
                                    __( 'Applying as',        DS_THEME_TEXTDOMAIN ) => $participation_model,
                                    __( 'User category',      DS_THEME_TEXTDOMAIN ) => $user_category,
                                ];
                                foreach ( $items as $label => $value ) : ?>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="wv-input-group">
                                            <label class="d-block my-4 small text-muted"><?php echo esc_html( $label ); ?></label>
                                            <span class="d-block fw-500"><?php echo esc_html( $value ?: '—' ); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php if ( $user_cat_other ) : ?>
                                    <div class="col-12">
                                        <div class="wv-input-group">
                                            <label class="d-block my-4 small text-muted">
                                                <?php esc_html_e( 'Category description (Other)', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <p class="mb-0"><?php echo nl2br( esc_html( $user_cat_other ) ); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="col-12">
                                    <div class="wv-input-group">
                                        <label class="d-block my-4 small text-muted">
                                            <?php esc_html_e( 'Reasons for visiting', DS_THEME_TEXTDOMAIN ); ?>
                                        </label>
                                        <span class="d-block fw-500"><?php
                                            echo $reasons_visiting
                                                ? esc_html( implode( ', ', $reasons_visiting ) )
                                                : '—';
                                        ?></span>
                                    </div>
                                </div>

                                <?php if ( $other_reasons ) : ?>
                                    <div class="col-12">
                                        <div class="wv-input-group">
                                            <label class="d-block my-4 small text-muted">
                                                <?php esc_html_e( 'Other reasons', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <p class="mb-0"><?php echo nl2br( esc_html( $other_reasons ) ); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $points_interest ) : ?>
                                    <div class="col-12">
                                        <div class="wv-input-group">
                                            <label class="d-block my-4 small text-muted">
                                                <?php esc_html_e( 'Points of interest', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <span class="d-block fw-500"><?php
                                                echo esc_html( implode( ', ', $points_interest ) );
                                            ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $reason_applying ) : ?>
                                    <div class="col-12">
                                        <div class="wv-input-group">
                                            <label class="d-block my-4 small text-muted">
                                                <?php esc_html_e( 'Reason for applying', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <p class="mb-0"><?php echo nl2br( esc_html( $reason_applying ) ); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            <?php endif; ?>

                        </div><!-- /.row -->
                    </div><!-- /.card‑body -->
                </div><!-- /.card -->
            </div>
        </div>
    </div>

    <?php
	/* ------------------------------------------------------------------
	 * minimal data needed for this block
	 * -----------------------------------------------------------------*/
	$profile_logo        = get_user_meta( $uid, 'wv_user-logo', true );
	if ( ! empty( $profile_logo ) ) {
		$profile_logo = str_replace( '-200.jpg', '-400.jpg', $profile_logo );
	}

	$company_description = get_user_meta( $uid, 'wv_companyDescription', true );
	?>
	<?php if ( \Desymphony\Helpers\DS_Utils::get_visitor_participation($uid) !== 'Public Visitor' ) : ?>
	<!-- COMPANY INFORMATION Section -->
	<form id="wv-company-information-form" class="" method="post">
		<div class="row mt-12">
			<div class="col-12">
				<div class="wv-card wv-flex-column br-12 wv-bg-w">
					<div class="wv-card-header p-24 d-flex justify-content-between align-items-center border-bottom">
						<h4 class="m-0 fs-20 fw-600 ls-4 lh-1-5">
							<?php esc_html_e( 'COMPANY INFORMATION', DS_THEME_TEXTDOMAIN ); ?>
						</h4>
						<?php if ( \Desymphony\Helpers\DS_Utils::is_admin_verified($uid) ) : ?>
							<button type="button"
									class="wv-button wv-button-pill wv-button-sm wv-button-edit">
								<?php esc_html_e( 'Edit', DS_THEME_TEXTDOMAIN ); ?>
							</button>
						<?php endif; ?>
					</div>

					<div class="wv-card-body p-24">
						<div class="row">
							<!-- Company logo -->
							<div class="col-lg-3 col-edit-lg-6 col-view-lg-3">
								<div class="wv-input-group">
									<label class="d-block my-8">
										<?php esc_html_e( 'Company logo', DS_THEME_TEXTDOMAIN ); ?>
									</label>

									<img src="<?php echo esc_url( $profile_logo ); ?>"
										 alt=""
										 class="img-fluid wv-company-logo br-8 border border-4 wv-bc-c_20 wv-view-only">
								</div>
							</div>

							<!-- Company description -->
							<div class="col-lg-9 col-edit-lg-6 col-view-lg-9">
								<div class="wv-input-group">
									<label class="d-block my-8" for="wv_companyDescription">
										<?php esc_html_e( 'Company description', DS_THEME_TEXTDOMAIN ); ?>
									</label>
									<textarea id="wv_companyDescription"
											  name="wv_companyDescription"
											  rows="12"
											  maxlength="700"
											  <?php echo $editable ? '' : ''; ?>>
										<?php echo esc_textarea( $company_description ); ?>
									</textarea>
								</div>
							</div>
						</div><!-- /.row -->
					</div><!-- /.card‑body -->
				</div><!-- /.wv‑card -->
			</div><!-- /.col‑12 -->
		</div><!-- /.row -->
	</form>
	<?php endif; // visitor ≠ Public Visitor ?>

    <?php                                        
    /* ============================================================
    *  BLOCK 2 – COMPANY CREDENTIALS
    * ============================================================
    */
    if ( \Desymphony\Helpers\DS_Utils::get_visitor_participation($uid) !== 'Public Visitor' ) :

        /* ──  meta pulled for this block ───────────────────────── */
        $company_name                    = get_user_meta( $uid, 'wv_company_name',               true );
        $company_pobRegion               = get_user_meta( $uid, 'wv_company_pobRegion',          true );
        $company_country                 = get_user_meta( $uid, 'wv_company_country',            true );
        $company_email                   = get_user_meta( $uid, 'wv_company_email',              true );
        $company_city                    = get_user_meta( $uid, 'wv_company_city',               true );
        $company_website                 = get_user_meta( $uid, 'wv_company_website',            true );
        $company_address                 = get_user_meta( $uid, 'wv_company_address',            true );
        $annual_production               = get_user_meta( $uid, 'wv_annualProductionLiters',          true );
        $current_stock                   = get_user_meta( $uid, 'wv_currentStockLiters',              true );
        $company_id_registry_number      = get_user_meta( $uid, 'wv_company_idRegistryNumber',   true );
        $company_vat_registry_number     = get_user_meta( $uid, 'wv_company_vatRegistryNumber',  true );
        $company_iban                    = get_user_meta( $uid, 'wv_company_iban',               true );
        $company_foreign_bank            = get_user_meta( $uid, 'wv_company_foreignBank',        true );
        $company_domestic_bank           = get_user_meta( $uid, 'wv_company_domesticBank',       true );
        $company_foreign_account_number  = get_user_meta( $uid, 'wv_company_foreignAccountNumber', true );
        $company_domestic_account_number = get_user_meta( $uid, 'wv_company_domesticAccountNumber', true );
        $company_foreign_swift           = get_user_meta( $uid, 'wv_company_foreignSwift',       true );
        $company_domestic_swift          = get_user_meta( $uid, 'wv_company_domesticSwift',      true );

        ?>
        <!-- COMPANY CREDENTIALS -->
        <form id="wv-company-credentials-form" class="" method="post">
            <div class="row mt-12">
                <div class="col-12">
                    <div class="wv-card wv-flex-column br-12 wv-bg-w">
                        <div class="wv-card-header p-24 d-flex justify-content-between align-items-center border-bottom">
                            <h4 class="m-0 fs-20 fw-600 ls-4 lh-1-5">
                                <?php esc_html_e( 'COMPANY CREDENTIALS', DS_THEME_TEXTDOMAIN ); ?>
                            </h4>
                            <?php if ( \Desymphony\Helpers\DS_Utils::is_admin_verified($uid) ) : ?>
                                <button type="button"
                                        class="wv-button wv-button-pill wv-button-sm wv-button-edit">
                                    <?php esc_html_e( 'Edit', DS_THEME_TEXTDOMAIN ); ?>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="wv-card-body p-24">
                            <div class="row">
                                <!-- Official name -->
                                <div class="col-lg-6">
                                    <div class="wv-input-group">
                                        <label class="d-block my-8" for="wv_company_name">
                                            <?php esc_html_e( 'Official company name in English', DS_THEME_TEXTDOMAIN ); ?>
                                        </label>
                                        <input class="wv-w-100" type="text"
                                            id="wv_company_name" name="wv_company_name"
                                            value="<?php echo esc_attr( $company_name ); ?>"
                                            required <?php echo $editable ? '' : ''; ?> >
                                    </div>
                                </div>

                                <!-- P.O.B / Region -->
                                <div class="col-lg-6">
                                    <div class="wv-input-group">
                                        <label class="d-block my-8" for="wv_company_pobRegion">
                                            <?php esc_html_e( 'P.O.B. / area / municipality / region', DS_THEME_TEXTDOMAIN ); ?>
                                        </label>
                                        <input class="wv-w-100" type="text"
                                            id="wv_company_pobRegion" name="wv_company_pobRegion"
                                            value="<?php echo esc_attr( $company_pobRegion ); ?>"
                                            required <?php echo $editable ? '' : ''; ?> >
                                    </div>
                                </div>

                                <!-- Country -->
                                <div class="col-lg-6">
                                    <div class="wv-input-group">
                                        <label class="d-block my-8" for="wv_company_country">
                                            <?php esc_html_e( 'Country of residence', DS_THEME_TEXTDOMAIN ); ?>
                                        </label>

                                        <select class="wv-w-100 form-select"
                                                id="wv_company_country" name="wv_company_country"
                                                <?php echo $editable ? '' : ''; ?> >
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

                                <!-- Email -->
                                <div class="col-lg-6">
                                    <div class="wv-input-group">
                                        <label class="d-block my-8" for="wv_company_email">
                                            <?php esc_html_e( 'E-mail address', DS_THEME_TEXTDOMAIN ); ?>
                                        </label>
                                        <input class="wv-w-100" type="email"
                                            id="wv_company_email" name="wv_company_email"
                                            value="<?php echo esc_attr( $company_email ); ?>"
                                            required <?php echo $editable ? '' : ''; ?> >
                                    </div>
                                </div>

                                <!-- City -->
                                <div class="col-lg-6">
                                    <div class="wv-input-group">
                                        <label class="d-block my-8" for="wv_company_city">
                                            <?php esc_html_e( 'City of residence', DS_THEME_TEXTDOMAIN ); ?>
                                        </label>
                                        <input class="wv-w-100" type="text"
                                            id="wv_company_city" name="wv_company_city"
                                            value="<?php echo esc_attr( $company_city ); ?>"
                                            required <?php echo $editable ? '' : ''; ?> >
                                    </div>
                                </div>

                                <!-- Website -->
                                <div class="col-lg-6">
                                    <div class="wv-input-group">
                                        <label class="d-block my-8" for="wv_company_website">
                                            <?php esc_html_e( 'Website', DS_THEME_TEXTDOMAIN ); ?>
                                        </label>
                                        <input class="wv-w-100" type="text"
                                            id="wv_company_website" name="wv_company_website"
                                            value="<?php echo esc_attr( $company_website ); ?>"
                                            <?php echo $editable ? '' : ''; ?> >
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="col-lg-6">
                                    <div class="wv-input-group">
                                        <label class="d-block my-8" for="wv_company_address">
                                            <?php esc_html_e( 'Address (street and number)', DS_THEME_TEXTDOMAIN ); ?>
                                        </label>
                                        <input class="wv-w-100" type="text"
                                            id="wv_company_address" name="wv_company_address"
                                            value="<?php echo esc_attr( $company_address ); ?>"
                                            required <?php echo $editable ? '' : ''; ?> >
                                    </div>
                                </div>

                                <?php
                                /* ---------------------------------------------
                                *  Winemaker specific: production & stock
                                * ------------------------------------------ */
                                if ( \Desymphony\Helpers\DS_Utils::is_exhibitor($uid) ) : ?>
                                    <div class="col-lg-3">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_annualProductionLiters">
                                                <?php esc_html_e( 'Annual production', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <?php
                                            $production_ranges = [
                                                '0-10000'       => '1 – 10 000',
                                                '10000-50000'   => '10 000 – 50 000',
                                                '50000-250000'  => '50 000 – 250 000',
                                                '250000-500000' => '250 000 – 500 000',
                                                '500000+'       => '500 000+',
                                            ]; ?>
                                            <select class="wv-w-100 form-select"
                                                    id="wv_annualProductionLiters"
                                                    name="wv_annualProductionLiters"
                                                    <?php echo $editable ? '' : ''; ?> >
                                                <?php foreach ( $production_ranges as $val => $lbl ) : ?>
                                                    <option value="<?php echo esc_attr( $val ); ?>"
                                                            <?php selected( $annual_production, $val ); ?>>
                                                        <?php echo esc_html( $lbl ); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_currentStockLiters">
                                                <?php esc_html_e( 'Currently in stock', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input class="wv-w-100" type="text"
                                                id="wv_currentStockLiters" name="wv_currentStockLiters"
                                                value="<?php echo esc_attr( $current_stock ); ?>"
                                                required <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>
                                <?php endif; /* Winemaker extras */ ?>
                                <?php
                                /* Buyer / Visitor only ------------------------------------------------ */
                                if ( \Desymphony\Helpers\DS_Utils::is_buyer($uid) ||
                                    \Desymphony\Helpers\DS_Utils::is_visitor($uid) ) :

                                    $government_support = get_user_meta( $uid, 'wv_governmentSupport', true );
                                ?>
                                    <div class="col-12 mt-12">
                                        <div class="form-check">
                                            <label class="wv-input-group" >
                                                <?php esc_html_e( 'Government Support Program', DS_THEME_TEXTDOMAIN ); ?>
                                                <?php echo ($government_support == '1') ? esc_html__('Yes', DS_THEME_TEXTDOMAIN) : esc_html__('No', DS_THEME_TEXTDOMAIN); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div><!-- /.row -->

                            <?php
                            /* ---------------------------------------------
                            *  Financial fields (Exhibitors only)
                            * ------------------------------------------ */
                            if ( \Desymphony\Helpers\DS_Utils::is_exhibitor($uid) ) : ?>

                                <div class="row mt-24">
                                    <div class="col-lg-3">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_company_idRegistryNumber">
                                                <?php esc_html_e( 'ID registry number', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input type="text"
                                                id="wv_company_idRegistryNumber" name="wv_company_idRegistryNumber"
                                                value="<?php echo esc_attr( $company_id_registry_number ); ?>"
                                                required <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_company_vatRegistryNumber">
                                                <?php esc_html_e( 'VAT registry number', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input type="text"
                                                id="wv_company_vatRegistryNumber" name="wv_company_vatRegistryNumber"
                                                value="<?php echo esc_attr( $company_vat_registry_number ); ?>"
                                                required <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_company_iban">
                                                <?php esc_html_e( 'IBAN', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input type="text"
                                                id="wv_company_iban" name="wv_company_iban"
                                                value="<?php echo esc_attr( $company_iban ); ?>"
                                                required <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_company_foreignBank">
                                                <?php esc_html_e( 'Foreign exchange correspondent bank', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input type="text"
                                                id="wv_company_foreignBank" name="wv_company_foreignBank"
                                                value="<?php echo esc_attr( $company_foreign_bank ); ?>"
                                                required <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_company_domesticBank">
                                                <?php esc_html_e( 'Domestic exchange bank', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input type="text"
                                                id="wv_company_domesticBank" name="wv_company_domesticBank"
                                                value="<?php echo esc_attr( $company_domestic_bank ); ?>"
                                                required <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_company_foreignAccountNumber">
                                                <?php esc_html_e( 'Foreign exchange account number', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input type="text"
                                                id="wv_company_foreignAccountNumber" name="wv_company_foreignAccountNumber"
                                                value="<?php echo esc_attr( $company_foreign_account_number ); ?>"
                                                required <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_company_domesticAccountNumber">
                                                <?php esc_html_e( 'Domestic exchange account number', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input type="text"
                                                id="wv_company_domesticAccountNumber" name="wv_company_domesticAccountNumber"
                                                value="<?php echo esc_attr( $company_domestic_account_number ); ?>"
                                                required <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_company_foreignSwift">
                                                <?php esc_html_e( 'Foreign exchange swift code', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input type="text"
                                                id="wv_company_foreignSwift" name="wv_company_foreignSwift"
                                                value="<?php echo esc_attr( $company_foreign_swift ); ?>"
                                                required <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_company_domesticSwift">
                                                <?php esc_html_e( 'Beneficiary swift code', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input type="text"
                                                id="wv_company_domesticSwift" name="wv_company_domesticSwift"
                                                value="<?php echo esc_attr( $company_domestic_swift ); ?>"
                                                required <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>
                                </div><!-- /.row -->
                            <?php endif; /* exhibitor financials */ ?>

                            <!-- Social links -->
                            <div class="row mt-24">
                                <?php
                                $social_fields = [
                                    'wv_socInstagram' => __( 'Instagram Profile', DS_THEME_TEXTDOMAIN ),
                                    'wv_socLinkedin'  => __( 'LinkedIn Profile',  DS_THEME_TEXTDOMAIN ),
                                    'wv_socFacebook'  => __( 'Facebook Profile',  DS_THEME_TEXTDOMAIN ),
                                    'wv_socX'         => __( 'X Profile',         DS_THEME_TEXTDOMAIN ),
                                ];
                                foreach ( $social_fields as $meta_key => $label ) :
                                    $val = get_user_meta( $uid, $meta_key, true ); ?>
                                    <div class="col-lg-6">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="<?php echo esc_attr( $meta_key ); ?>">
                                                <?php echo esc_html( $label ); ?>
                                            </label>
                                            <input type="text"
                                                id="<?php echo esc_attr( $meta_key ); ?>"
                                                name="<?php echo esc_attr( $meta_key ); ?>"
                                                value="<?php echo esc_attr( $val ); ?>"
                                                <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div><!-- /.row -->
                        </div><!-- /.card‑body -->
                    </div><!-- /.wv‑card -->
                </div><!-- /.col‑12 -->
            </div><!-- /.row -->
        </form>
    <?php
    endif; // Public Visitor guard

    /* ============================================================
    *  BLOCK 3 – REPRESENTATIVE / VISITOR CREDENTIALS
    * ============================================================
    */

    # ---- meta for this block ------------------------------------
    $profile_picture = get_user_meta( $uid, 'wv_user-avatar', true );
    if ( $profile_picture ) {
        $profile_picture = str_replace( '-200.jpg', '-400.jpg', $profile_picture );
    }

    $rep_first_name  = get_user_meta( $uid, 'wv_firstName',            true );
    $rep_last_name   = get_user_meta( $uid, 'wv_lastName',             true );
    $rep_nationality = get_user_meta( $uid, 'wv_nationality',          true );
    $rep_email       = get_user_meta( $uid, 'wv_email',                true );
    $rep_occupation  = get_user_meta( $uid, 'wv_professionalOccupation', true );
    $rep_experience  = get_user_meta( $uid, 'wv_yearsOfExperience',    true );
    $rep_position    = get_user_meta( $uid, 'wv_positionInCompany',    true );
    $rep_contact     = get_user_meta( $uid, 'wv_contactTelephone',     true );

    # used for nationality <select>
    $company_country = get_user_meta( $uid, 'wv_company_country',      true );

    ?>
    <!-- REPRESENTATIVE / VISITOR CREDENTIALS -->
    <form id="wv-representative-credentials-form" class="" method="post">
        <div class="row mt-12">
            <div class="col-12">
                <div class="wv-card wv-flex-column br-12 wv-bg-w">
                    <div class="wv-card-header p-24 d-flex justify-content-between align-items-center border-bottom">
                        <h4 class="m-0 fs-20 fw-600 ls-4 lh-1-5">
                            <?php
                            if ( \Desymphony\Helpers\DS_Utils::get_visitor_participation($uid) !== 'Public Visitor' ) {
                                esc_html_e( 'REPRESENTATIVE CREDENTIALS', DS_THEME_TEXTDOMAIN );
                            } else {
                                esc_html_e( 'VISITOR CREDENTIALS', DS_THEME_TEXTDOMAIN );
                            }
                            ?>
                        </h4>
                        
                    </div>

                    <div class="wv-card-body p-24">
                        <div class="row justify-content-between">

                            <!-- Profile picture -->
                            <div class="col-lg-3">
                                <div class="wv-input-group">
                                    <label class="d-block my-8">
                                        <?php esc_html_e( 'Profile picture', DS_THEME_TEXTDOMAIN ); ?>
                                    </label>

                                    <!-- preview (view‑only) -->
                                    <img src="<?php echo esc_url( $profile_picture ); ?>"
                                        alt=""
                                        class="img-fluid wv-company-logo br-8 border border-4 wv-bc-c_20 wv-view-only">
                                </div>
                            </div><!-- /picture -->

                            <div class="col-lg-6 col-lg-offset-3">
                                <div class="row">

                                    <!-- First name -->
                                    <div class="col-12">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_firstName">
                                                <?php esc_html_e( 'First name', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input type="text" id="wv_firstName" name="wv_firstName"
                                                value="<?php echo esc_attr( $rep_first_name ); ?>"
                                                required <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>

                                    <!-- Last name -->
                                    <div class="col-12">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_lastName">
                                                <?php esc_html_e( 'Last name', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input type="text" id="wv_lastName" name="wv_lastName"
                                                value="<?php echo esc_attr( $rep_last_name ); ?>"
                                                required <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>

                                    <?php if ( \Desymphony\Helpers\DS_Utils::get_visitor_participation($uid) !== 'Public Visitor' ) : ?>
                                        <!-- Nationality -->
                                        <div class="col-12">
                                            <div class="wv-input-group">
                                                <label class="d-block my-8" for="wv_nationality">
                                                    <?php esc_html_e( 'Country of residence', DS_THEME_TEXTDOMAIN ); ?>
                                                </label>
                                                <select class="wv-w-100 form-select"
                                                        id="wv_nationality" name="wv_nationality"
                                                        <?php echo $editable ? '' : ''; ?> >
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

                                        <!-- Position -->
                                        <div class="col-12">
                                            <div class="wv-input-group">
                                                <label class="d-block my-8" for="wv_positionInCompany">
                                                    <?php esc_html_e( 'Position in the company', DS_THEME_TEXTDOMAIN ); ?>
                                                </label>
                                                <input type="text" id="wv_positionInCompany" name="wv_positionInCompany"
                                                    value="<?php echo esc_attr( $rep_position ); ?>"
                                                    <?php echo $editable ? '' : ''; ?> >
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Occupation -->
                                    <div class="col-12">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_professionalOccupation">
                                                <?php
                                                echo \Desymphony\Helpers\DS_Utils::get_visitor_participation($uid) !== 'Public Visitor'
                                                    ? esc_html__( 'Professional occupation', DS_THEME_TEXTDOMAIN )
                                                    : esc_html__( 'Occupation', DS_THEME_TEXTDOMAIN );
                                                ?>
                                            </label>
                                            <input type="text" id="wv_professionalOccupation" name="wv_professionalOccupation"
                                                value="<?php echo esc_attr( $rep_occupation ); ?>"
                                                <?php echo $editable ? '' : ''; ?> >
                                        </div>
                                    </div>

                                    <?php if ( \Desymphony\Helpers\DS_Utils::get_visitor_participation($uid) !== 'Public Visitor' ) : ?>
                                        <!-- Years of experience -->
                                        <div class="col-12">
                                            <div class="wv-input-group">
                                                <label class="d-block my-8" for="wv_yearsOfExperience">
                                                    <?php esc_html_e( 'Years of professional experience', DS_THEME_TEXTDOMAIN ); ?>
                                                </label>
                                                <input type="text" id="wv_yearsOfExperience" name="wv_yearsOfExperience"
                                                    value="<?php echo esc_attr( $rep_experience ); ?>"
                                                    <?php echo $editable ? '' : ''; ?> >
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ( \Desymphony\Helpers\DS_Utils::get_visitor_participation($uid) === 'Public Visitor' ) : ?>
                                        <!-- Country / City for public visitor -->
                                        <?php
                                        $pv_country = get_user_meta( $uid, 'wv_countryOfResidence', true );
                                        $pv_city    = get_user_meta( $uid, 'wv_cityOfResidence',   true ); ?>
                                        <div class="col-12">
                                            <div class="wv-input-group">
                                                <label class="d-block my-8" for="wv_countryOfResidence">
                                                    <?php esc_html_e( 'Country of residence', DS_THEME_TEXTDOMAIN ); ?>
                                                </label>
                                                <input type="text" id="wv_countryOfResidence" name="wv_countryOfResidence"
                                                    value="<?php echo esc_attr( $pv_country ); ?>"
                                                    <?php echo $editable ? '' : ''; ?> >
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="wv-input-group">
                                                <label class="d-block my-8" for="wv_cityOfResidence">
                                                    <?php esc_html_e( 'City of residence', DS_THEME_TEXTDOMAIN ); ?>
                                                </label>
                                                <input type="text" id="wv_cityOfResidence" name="wv_cityOfResidence"
                                                    value="<?php echo esc_attr( $pv_city ); ?>"
                                                    <?php echo $editable ? '' : ''; ?> >
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Email -->
                                    <div class="col-12">
                                        <div class="wv-input-group">
                                            <label class="d-block my-8" for="wv_email">
                                                <?php esc_html_e( 'E-mail address', DS_THEME_TEXTDOMAIN ); ?>
                                            </label>
                                            <input type="email" id="wv_email" name="wv_email"
                                                value="<?php echo esc_attr( $rep_email ); ?>"
                                                disabled readonly>
                                        </div>
                                    </div>

                                    <?php if ( \Desymphony\Helpers\DS_Utils::get_visitor_participation($uid) !== 'Public Visitor' ) : ?>
                                        <!-- Telephone -->
                                        <div class="col-12">
                                            <div class="wv-input-group">
                                                <label class="d-block my-8" for="wv_contactTelephone">
                                                    <?php esc_html_e( 'Contact (telephone number)', DS_THEME_TEXTDOMAIN ); ?>
                                                </label>
                                                <input type="tel" id="wv_contactTelephone" name="wv_contactTelephone"
                                                    value="<?php echo esc_attr( $rep_contact ); ?>"
                                                    required <?php echo $editable ? '' : ''; ?> >
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php
                                    /* pull the two extra flags – keep this near the other rep_* meta */
                                    $rep_whatsapp = get_user_meta( $uid, 'wv_exhibitor_rep_whatsapp', true );
                                    $rep_viber    = get_user_meta( $uid, 'wv_exhibitor_rep_viber',    true );
                                    ?>

                                    <?php if ( \Desymphony\Helpers\DS_Utils::get_visitor_participation($uid) !== 'Public Visitor' ) : ?>
                                        <!-- Telephone + WhatsApp / Viber -->
                                        <div class="col-12">
                                            <div class="wv-input-group">
                                                <label class="form-check-label" for="wv_exhibitor_rep_whatsapp">
                                                    <?php esc_html_e( 'WhatsApp', DS_THEME_TEXTDOMAIN ); ?>:
                                                    <?php echo ($rep_whatsapp == '1') ? esc_html__('Yes', DS_THEME_TEXTDOMAIN) : esc_html__('No', DS_THEME_TEXTDOMAIN); ?>
                                                </label>
                                            </div>
                                            <div class="wv-input-group">
                                                <label class="form-check-label" for="wv_exhibitor_rep_viber">
                                                    <?php esc_html_e( 'Viber', DS_THEME_TEXTDOMAIN ); ?>:
                                                    <?php echo ($rep_viber == '1') ? esc_html__('Yes', DS_THEME_TEXTDOMAIN) : esc_html__('No', DS_THEME_TEXTDOMAIN); ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endif; ?>


                                </div><!-- /.row inner -->
                            </div><!-- /.col‑6 -->

                        </div><!-- /.row -->
                    </div><!-- /.card‑body -->
                </div><!-- /.wv‑card -->
            </div><!-- /.col‑12 -->
        </div><!-- /.row -->
    </form>
    <?php
    /* ============================================================
    *  BLOCK 4 – WOOCOMMERCE / SYSTEM META
    * ============================================================
    */

    # ---- pull everything once -----------------------------------
    $all_meta      = get_user_meta( $uid );
    $wc_last_raw   = $all_meta['wc_last_active'][0] ?? '';
    $last_upd_raw  = $all_meta['last_update'][0]    ?? '';

    $wc_last_active = $wc_last_raw ? date_i18n( 'F j, Y H:i', (int) $wc_last_raw ) : '';
    $last_update    = $last_upd_raw ? date_i18n( 'F j, Y H:i', (int) $last_upd_raw ) : '';

    $billing = [];
    $shipping = [];
    foreach ( $all_meta as $key => $val_arr ) {
        $val = maybe_unserialize( $val_arr[0] ?? '' );
        if ( str_starts_with( $key, 'billing_' ) ) {
            $billing[ $key ] = $val;
        } elseif ( str_starts_with( $key, 'shipping_' ) ) {
            $shipping[ $key ] = $val;
        }
    }

    /* helper to print one generic control */
    $ctl = static function ( string $slug, string $value, bool $editable ): string {
        $label = ucwords( str_replace( [ 'billing_', 'shipping_', '_' ], [ '', '', ' ' ], $slug ) );
        $disabled = $editable ? '' : ' disabled';
        return
            '<div class="col-md-6 mb-12">'.
                '<div class="wv-input-group">'.
                    '<label class="d-block my-4">'. esc_html( $label ) .'</label>'.
                    '<input type="text" class="wv-w-100" name="'. esc_attr( $slug ) .
                    '" value="'. esc_attr( $value ) .'"'.$disabled.'>'.
                '</div>'.
            '</div>';
    };

    ?>
    <!-- WOO META -->
    <form id="wv-woocommerce-meta-form" class="" method="post">
        <div class="row mt-12">
            <div class="col-12">
                <div class="wv-card wv-flex-column br-12 wv-bg-w">
                    <div class="wv-card-header p-24 d-flex justify-content-between align-items-center border-bottom">
                        <h4 class="m-0 fs-20 fw-600 ls-4 lh-1-5">
                            <?php esc_html_e( 'WooCommerce & System', DS_THEME_TEXTDOMAIN ); ?>
                        </h4>
                        
                    </div>

                    <div class="wv-card-body p-24">

                        <!-- activity row -->
                        <div class="row mb-24">
                            <div class="col-md-6">
                                <div class="wv-input-group">
                                    <label class="d-block my-4">
                                        <?php esc_html_e( 'WooCommerce – Last active', DS_THEME_TEXTDOMAIN ); ?>
                                    </label>
                                    <input type="text" class="wv-w-100"
                                        value="<?php echo esc_attr( $wc_last_active ); ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="wv-input-group">
                                    <label class="d-block my-4">
                                        <?php esc_html_e( 'Profile last updated', DS_THEME_TEXTDOMAIN ); ?>
                                    </label>
                                    <input type="text" class="wv-w-100"
                                        value="<?php echo esc_attr( $last_update ); ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <!-- billing + shipping -->
                        <h6 class="text-uppercase text-secondary mt-8 mb-12">
                            <?php esc_html_e( 'Billing address', DS_THEME_TEXTDOMAIN ); ?>
                        </h6>
                        <div class="row">
                            <?php foreach ( $billing as $k => $v ) echo $ctl( $k, $v, $editable ); ?>
                        </div>

                        <h6 class="text-uppercase text-secondary mt-24 mb-12">
                            <?php esc_html_e( 'Shipping address', DS_THEME_TEXTDOMAIN ); ?>
                        </h6>
                        <div class="row">
                            <?php foreach ( $shipping as $k => $v ) echo $ctl( $k, $v, $editable ); ?>
                        </div>

                    </div><!-- /.card‑body -->
                </div><!-- /.card -->
            </div><!-- /.col‑12 -->
        </div><!-- /.row -->
    </form>



<?php
} // ds_render_profile_overview()
