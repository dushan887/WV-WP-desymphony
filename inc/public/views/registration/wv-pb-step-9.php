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
        <h6 class="my-0 text-uppercase ls-3 fw-600">Company Credentials</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Your social networks</h2>
            <p class="mb-24 text-uppercase ls-3">ALL FIELDS ARE OPTIONAL</p>
        </div>

        <div class="row g-12 justify-content-start align-items-start">

            <!-- Instagram -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_socInstagram">Instagram</label>
                    <input
                        type="text"
                        id="wv_socInstagram"
                        name="wv_socInstagram"
                        value="<?php echo val('wv_socInstagram'); ?>"
                        required
                    />
                </div>
            </div>
            <!-- LinkedIn -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_socLinkedin">LinkedIn</label>
                    <input
                        type="text"
                        id="wv_socLinkedin"
                        name="wv_socLinkedin"
                        value="<?php echo val('wv_socLinkedin'); ?>"
                        required
                    />
                </div>
            </div>
            <!-- Facebook -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_socFacebook">Facebook</label>
                    <input
                        type="text"
                        id="wv_socFacebook"
                        name="wv_socFacebook"
                        value="<?php echo val('wv_socFacebook'); ?>"
                        required
                    />
                </div>
            </div>
            <!-- X -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_socX">X (ex Twitter)</label>
                    <input
                        type="text"
                        id="wv_socX"
                        name="wv_socX"
                        value="<?php echo val('wv_socX'); ?>"
                        required
                    />
                </div>
            </div>


        </div> <!-- .row -->
    </div> <!-- #wv-step-body -->
</div> <!-- .wv-step -->
