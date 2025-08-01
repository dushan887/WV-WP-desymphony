<?php if (!defined('ABSPATH')) { exit; } ?>

<?php
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file = basename(__FILE__, '.php'); // e.g. "wv-exhibitor-step-3"
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}

$saved_data = $_SESSION["wv_reg_{$step_key}"] ?? [];
$field_value = $saved_data['wv_userCategoryOtherDescription'] ?? '';
?>

<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">PROFESSIONAL ACTIVITIES CATEGORY</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Describe your category</h2>
            <p class="mb-24 text-uppercase ls-3">IN WRITTEN WORDS, UP TO 200 CHARACTERS</p>
        </div>

        <div class="row g-12 justify-content-center align-items-stretch">
            <div class="col-12 my-0">
                <label class="wv-label-block d-block my-0 text-center px-32 py-16">
                    <span>Describe your company's professional activities</span>
                </label>
            </div>
            <div class="col-12 my-0">
                <div class="d-block bg-white p-32 br-8 br-t-0 ds-min-h-350">
                    <div class="wv-input-group">
                        <textarea
                            id="wv_userCategoryOtherDescription"
                            name="wv_userCategoryOtherDescription"
                            rows="8"
                            maxlength="200"
                            class="border-0"
                        ><?php echo esc_textarea($field_value); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
