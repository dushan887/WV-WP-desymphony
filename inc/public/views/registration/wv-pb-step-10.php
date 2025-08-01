<?php if (!defined('ABSPATH')) { exit; } ?>

<?php
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file     = basename(__FILE__, '.php');
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}

$saved_data = $_SESSION["wv_reg_{$step_key}"] ?? [];

// Helper function for shorter code
function val($key, $default='') {
    global $saved_data;
    return isset($saved_data[$key]) ? esc_attr($saved_data[$key]) : $default;
}
?>

<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">Representative Credentials</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Your representative at the fair</h2>
            <p class="mb-24 text-uppercase ls-3">MARKED FIELDS (*) ARE COMPULSORY</p>
        </div>

        <div class="row g-12 justify-content-start align-items-start">

            <!-- First Name -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_firstName">First name*</label>
                    <input
                        type="text"
                        id="wv_firstName"
                        name="wv_firstName"
                        value="<?php echo val('wv_firstName'); ?>"
                        required
                    />
                </div>
            </div>            

            <!-- Last Name -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_lastName">Last name*</label>
                    <input
                        type="text"
                        id="wv_lastName"
                        name="wv_lastName"
                        value="<?php echo val('wv_lastName'); ?>"
                        required
                    />
                </div>
            </div>

            <!-- Professional Occupation -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_professionalOccupation">Professional occupation</label>
                    <input
                        type="text"
                        id="wv_professionalOccupation"
                        name="wv_professionalOccupation"
                        value="<?php echo val('wv_professionalOccupation'); ?>"
                    />
                </div>
            </div>

            <!-- Years of Professional Experience -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_yearsOfExperience">Years of professional experience</label>
                    <input
                        type="number"
                        id="wv_yearsOfExperience"
                        name="wv_yearsOfExperience"
                        value="<?php echo val('wv_yearsOfExperience'); ?>"
                    />
                </div>
            </div>

            <!-- Nationality -->
             <?php
            // ------------------------------------------------------------------
            //  Country <select>
            // ------------------------------------------------------------------
            $countries = include DS_THEME_DIR . '/inc/public/views/partials/countries.php';
            $countries = is_array( $countries ) ? $countries : [];

            $selected = val( 'wv_nationality' );   
            ?>
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label class="d-block mb-8" for="wv_nationality">
                        Country of residence*
                    </label>

                    <select id="wv_nationality"
                            name="wv_nationality"
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

            <!-- Representative E-mail Address -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_email">E-mail address*</label>
                    <input
                        type="email"
                        id="wv_email"
                        name="wv_email"
                        value="<?php echo val('wv_email'); ?>"
                        required
                    />
                </div>
            </div>

            <!-- Position in the Company -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_positionInCompany">Position in the company</label>
                    <input
                        type="text"
                        id="wv_positionInCompany"
                        name="wv_positionInCompany"
                        value="<?php echo val('wv_positionInCompany'); ?>"
                    />
                </div>
            </div>

            <!-- Contact (Telephone) & Additional Options -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_contactTelephone">Contact (telephone number)*</label>
                    <div class="wv-phone-input">
                        <input
                            type="tel"
                            name="wv_contactTelephone"
                            id="wv_contactTelephone"
                            class="wv-phone-input-field"
                            required
                            value="<?php echo val('wv_contactTelephone'); ?>"
                        >
                        <div class="wv-contact-options">
                            <input
                                type="checkbox"
                                id="wv_exhibitor_rep_whatsapp"
                                name="wv_exhibitor_rep_whatsapp"
                                <?php echo (isset($saved_data['wv_exhibitor_rep_whatsapp']) && $saved_data['wv_exhibitor_rep_whatsapp']) ? 'checked' : ''; ?>
                            >
                            <label for="wv_exhibitor_rep_whatsapp">
                                <span class="wv wv_whatsapp"><span class="path1"></span><span class="path2"></span></span>
                            </label>
                            <input
                                type="checkbox"
                                id="wv_exhibitor_rep_viber"
                                name="wv_exhibitor_rep_viber"
                                <?php echo (isset($saved_data['wv_exhibitor_rep_viber']) && $saved_data['wv_exhibitor_rep_viber']) ? 'checked' : ''; ?>
                            >
                            <label for="wv_exhibitor_rep_viber">
                                <span class="wv wv_viber"><span class="path1"></span><span class="path2"></span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>


        </div> <!-- .row -->
    </div> <!-- #wv-step-body -->
</div> <!-- .wv-step -->
