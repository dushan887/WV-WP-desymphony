<?php if (!defined('ABSPATH')) { exit; } ?>

<?php
// Determine step key for session.
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file = basename(__FILE__, '.php'); // e.g. "wv-exhibitor-step-1"
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}

// Retrieve saved data.
$saved_data = $_SESSION["wv_reg_{$step_key}"] ?? [];
?>

<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">FIELD OF WORK</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Wine, Spirits or Food?</h2>
            <p class="mb-24 text-uppercase ls-3">CHOOSE SINGLE OPTION</p>
        </div>
        <div class="row g-12 justify-content-center align-items-stretch">

            <!-- Wine -->
            <div class="col-lg-4">
                <label class="wv-custom-radio">
                    <input
                        type="radio"
                        name="wv_fieldOfWork"
                        value="Wine"
                        required
                        <?php echo (!empty($saved_data['wv_fieldOfWork']) && $saved_data['wv_fieldOfWork'] === 'Wine') ? 'checked' : ''; ?>
                    >
                    <div class="wv-radio-card pt-48 pb-64">
                        <h3 class="h1">Wine</h3>
                        <div class="wv-check my-48">
                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                        </div>                        
                    </div>
                </label>
            </div>

            <!-- Spirits -->
            <div class="col-lg-4">
                <label class="wv-custom-radio">
                    <input
                        type="radio"
                        name="wv_fieldOfWork"
                        value="Spirits"
                        required
                        <?php echo (!empty($saved_data['wv_fieldOfWork']) && $saved_data['wv_fieldOfWork'] === 'Spirits') ? 'checked' : ''; ?>
                    >
                    <div class="wv-radio-card pt-48 pb-64">
                        <h3 class="h1">Spirits</h3>
                        <div class="wv-check my-48">
                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                        </div>                        
                    </div>
                </label>
            </div>

            <!-- Food -->
            <div class="col-lg-4">
                <label class="wv-custom-radio">
                    <input
                        type="radio"
                        name="wv_fieldOfWork"
                        value="Food"
                        required
                        <?php echo (!empty($saved_data['wv_fieldOfWork']) && $saved_data['wv_fieldOfWork'] === 'Food') ? 'checked' : ''; ?>
                    >
                    <div class="wv-radio-card pt-48 pb-64">
                        <h3 class="h1">Food</h3>
                        <div class="wv-check my-48">
                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                        </div>                        
                    </div>
                </label>
            </div>


        </div>
    </div>
</div>
