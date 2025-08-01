<?php if (!defined('ABSPATH')) { exit; } ?>

<?php
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file = basename(__FILE__, '.php'); // e.g. "wv-exhibitor-step-3"
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}

$saved_data = $_SESSION["wv_reg_{$step_key}"] ?? [];
$field_value = $saved_data['wv_reasonForApplying'] ?? '';
?>

<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">WINE VISION HOSTED BUYERS PROGRAM</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Your reasons for applying</h2>
            <p class="mb-24 text-uppercase ls-3">IN WRITTEN WORDS, UP TO 300 CHARACTERS</p>
        </div>

        <div class="row g-12 justify-content-center align-items-stretch">
            <div class="col-12 my-0">
                <label class="wv-label-block d-block my-0 text-center px-32 py-16">
                    <span>Why are you applying for the program?</span>
                </label>
            </div>
            <div class="col-12 my-0">
                <div class="d-block wv-bg-c_5 p-32 br-8 br-t-0">
                    <div class="row">
                        <div class="col-lg-6">
                            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/Hosted_Buyers_LOGO-1.png" alt="" class="img-fluid d-block mx-auto mb-24" style="max-width: 260px;">
                            <p class="fs-14">The Wine Vision Hosted Buyers Program has been designed to provide financial support to professional buyers. It is structured into four categories, representing different levels of support, each implying different set of obligations. The evaluation of participants applying for the support program is conducted in alternating cycles, each lasting 15 working days. Upon the completion of each cycle, the processed applicants are notified of the evaluation results. In the case of an approved application, the organizer specifies which support category has been assigned. The decision regarding the assigned category is final. In the case of a rejected application, the organizer is not obligated to provide an explanation for the decision.</p>
                        </div>
                        <div class="col-lg-6">
                            <div class="wv-input-group">
                                <textarea
                                    id="wv_reasonForApplying"
                                    name="wv_reasonForApplying"
                                    rows="12"
                                    maxlength="300"
                                    class="border-0"
                                    placeholder=""
                                    required                            
                                ><?php echo esc_textarea($field_value); ?></textarea>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
