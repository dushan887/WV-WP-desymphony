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
        <h6 class="my-0 text-uppercase ls-3 fw-600">EXHIBITOR PRODUCTS</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Are you exhibiting products?</h2>
            <p class="mb-24 text-uppercase ls-3">CHOOSE SINGLE OPTION</p>
        </div>
        <div class="row g-12 justify-content-center align-items-stretch">

            <!-- Yes -->
            <div class="col-lg-6">
                <label class="wv-custom-radio">
                    <input
                        type="radio"
                        name="wv_exhibitingProducts"
                        value="Yes"
                        required
                        <?php echo (!empty($saved_data['wv_exhibitingProducts']) && $saved_data['wv_exhibitingProducts'] === 'Yes') ? 'checked' : ''; ?>
                    >
                    <div class="wv-radio-card pt-48 pb-24">
                        <h3 class="h1 mb-12 mb-lg-0">Yes</h3>
                        <div class="wv-check my-48">
                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                        </div>
                        <div class="wv-radio-list-info d-flex fs-12 align-items-center justify-content-between flex-nowrap px-12 py-4 br-4">
                            <span class="fw-600">Upload single product with image and specifications</span>
                            <strong class="ls-5">COMPULSORY</strong>
                        </div>
                    </div>
                </label>
            </div>

            <!-- No -->
            <div class="col-lg-6">
                <label class="wv-custom-radio">
                    <input
                        type="radio"
                        name="wv_exhibitingProducts"
                        value="No"
                        required
                        <?php echo (!empty($saved_data['wv_exhibitingProducts']) && $saved_data['wv_exhibitingProducts'] === 'No') ? 'checked' : ''; ?>
                    >
                    <div class="wv-radio-card pt-48 pb-24">
                        <h3 class="h1 mb-12 mb-lg-0">No</h3>
                        <div class="wv-check my-48">
                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                        </div>
                        <div class="wv-radio-list-info d-flex fs-12 align-items-center justify-content-between flex-nowrap px-12 py-4 br-4">
                            <span class="fw-600">Write a company description (up to 700 characters)</span>
                            <strong class="ls-5">COMPULSORY</strong>
                        </div>
                    </div>
                </label>
            </div>


        </div>
    </div>
</div>
