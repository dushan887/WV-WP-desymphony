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
        <h6 class="my-0 text-uppercase ls-3 fw-600">VISITOR CREDENTIALS</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Your general information</h2>
            <p class="mb-24 text-uppercase ls-3">MARKED FIELDS (*) ARE COMPULSORY</p>
        </div>

        <div class="row g-12 justify-content-center align-items-stretch">

            <!-- First Name -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_firstName">First Name*</label>
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
                    <label for="wv_lastName">Last Name*</label>
                    <input
                        type="text"
                        id="wv_lastName"
                        name="wv_lastName"
                        value="<?php echo val('wv_lastName'); ?>"
                        required
                    />
                </div>
            </div>

            <!-- Occupation -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_professionalOccupation">Occupation</label>
                    <input
                        type="text"
                        id="wv_professionalOccupation"
                        name="wv_professionalOccupation"
                        value="<?php echo val('wv_professionalOccupation'); ?>"
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


            <!-- City of Residence -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label class="d-block" for="wv_company_city">City of residence*</label>
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

        </div> <!-- .row -->
    </div> <!-- #wv-step-body -->
</div> <!-- .wv-step -->
