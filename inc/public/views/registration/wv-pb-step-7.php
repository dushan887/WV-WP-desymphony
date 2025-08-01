<?php if (!defined('ABSPATH')) { exit; } ?>

<?php
// Determine the current step key.
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file     = basename(__FILE__, '.php');
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}

// Data saved for the current step.
$saved_data = $_SESSION["wv_reg_{$step_key}"] ?? [];

// Merge all registration session data to access global fields (like category).
$global_user_data = [];
foreach ($_SESSION as $key => $value) {
    if (strpos($key, 'wv_reg_') === 0 && is_array($value)) {
        $global_user_data = array_merge($global_user_data, $value);
    }
}
$selected_profile = $global_user_data['wv_profileSelection'] ?? '';
$selected_category = $global_user_data['wv_userCategory'] ?? '';

// Helper function for current step values.
function val($key, $default = '') {
    global $saved_data;
    return isset($saved_data[$key]) ? esc_attr($saved_data[$key]) : $default;
}
?>

<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">COMPANY CREDENTIALS</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Your general information</h2>
            <p class="mb-24 text-uppercase ls-3">MARKED FIELDS (*) ARE COMPULSORY</p>
        </div>

        <div class="row g-12 justify-content-start align-items-start">
            
            <!-- Company Full Name -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label class="d-block mb-8" for="wv_company_name">Official company name in English*</label>
                    <input
                        class="wv-w-100"
                        type="text"
                        id="wv_company_name"
                        name="wv_company_name"
                        value="<?php echo val('wv_company_name'); ?>"
                        required
                    />
                </div>
            </div>

            <!-- P.O.B. / Area / Municipality / Region -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label class="d-block mb-8" for="wv_company_pobRegion">P.O.B. / area / municipality / region</label>
                    <input
                        class="wv-w-100"
                        type="text"
                        id="wv_company_pobRegion"
                        name="wv_company_pobRegion"
                        value="<?php echo val('wv_company_pobRegion'); ?>"
                        required
                    />
                </div>
            </div>

            <?php
            // ------------------------------------------------------------------
            //  Country <select>
            // ------------------------------------------------------------------
            $countries = include DS_THEME_DIR . '/inc/public/views/partials/countries.php';
            $countries = is_array( $countries ) ? $countries : [];

            $selected = val( 'wv_company_country' );   // value saved in session (helper fn)
            ?>

            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label class="d-block mb-8" for="wv_company_country">
                        Country of residence*
                    </label>

                    <select id="wv_company_country"
                            name="wv_company_country"
                            class="wv-w-100"
                            required>
                        <option value=""></option>

                        <?php foreach ( $countries as $country ) : ?>
                            <option value="<?php echo esc_attr( $country ); ?>"
                                    <?php selected( $selected, $country ); ?>>
                                <?php echo esc_html( $country ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>


            <!-- E-mail Address -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label class="d-block mb-8" for="wv_company_email">E-mail address*</label>
                    <input
                        class="wv-w-100"
                        type="email"
                        id="wv_company_email"
                        name="wv_company_email"
                        value="<?php echo val('wv_company_email'); ?>"
                        required
                    />
                </div>
            </div>

            <!-- City of Residence -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label class="d-block mb-8" for="wv_company_city">City of residence*</label>
                    <input
                        class="wv-w-100"
                        type="text"
                        id="wv_company_city"
                        name="wv_company_city"
                        value="<?php echo val('wv_company_city'); ?>"
                        required
                    />
                </div>
            </div>

            <!-- Website -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label class="d-block mb-8" for="wv_company_website">Website</label>
                    <input
                        class="wv-w-100"
                        type="text"
                        id="wv_company_website"
                        name="wv_company_website"
                        value="<?php echo val('wv_company_website'); ?>"
                    />
                </div>
            </div>

            <!-- Address (Street & Number) -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label class="d-block mb-8" for="wv_company_address">Address (street and number)</label>
                    <input
                        class="wv-w-100"
                        type="text"
                        id="wv_company_address"
                        name="wv_company_address"
                        value="<?php echo val('wv_company_address'); ?>"
                        required
                    />
                </div>
            </div>

            <!-- Contact (Telephone) -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_company_phone">Contact (telephone number)*</label>
                    <div class="wv-phone-input">
                        <input
                            type="tel"
                            name="wv_company_phone"
                            id="wv_company_phone"
                            class="wv-phone-input-field"
                            required
                            value="<?php echo val('wv_company_phone'); ?>"
                        >
                    </div>
                </div>
            </div>

            <div class="col-lg-6 offset-lg-3 pt-8">
                <div class="wv-input-group">
                    <label class="wv-custom-radio">
                        <input
                            type="checkbox"
                            id="wv_governmentSupport"
                            name="wv_governmentSupport"
                             <?php echo val('wv_governmentSupport') ? 'checked' : ''; ?>
                        >
                        <div class="wv-radio-card wv-radio-card-inline wv-radio-card-inline-4">
                            <div class="wv-check">
                                <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                            </div>
                            <div class="ps-8 text-start">
                                <h3 class="h6">Apply for Hosted Buyers Program</h3>
                            </div>
                        </div>
                    </label>                    
                </div>
            </div>
            
            
        </div> <!-- .row -->
    </div> <!-- #wv-step-body -->
</div> <!-- .wv-step -->
