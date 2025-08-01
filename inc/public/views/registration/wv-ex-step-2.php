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
        <h6 class="my-0 text-uppercase ls-3 fw-600">PARTICIPATION MODEL</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">You are applying as:</h2>
            <p class="mb-24 text-uppercase ls-3">CHOOSE SINGLE OPTION</p>
        </div>
        <div class="row g-12 justify-content-center align-items-stretch">

            <!-- Solo Exhibitor -->
            <div class="col-lg-6">
                <label class="wv-custom-radio">
                    <input
                        type="radio"
                        name="wv_participationModel"
                        value="Solo Exhibitor"
                        required
                        <?php echo (!empty($saved_data['wv_participationModel']) && $saved_data['wv_participationModel'] === 'Solo Exhibitor') ? 'checked' : ''; ?>
                    >
                    <div class="wv-radio-card pt-48 pb-24">
                        <h3 class="h1">Solo Exhibitor</h3>
                        <p class="fs-12 ls-6 text-uppercase fw-600 mt-12">SINGLE STAND RENTAL</p>
                        <div class="wv-check my-48">
                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                        </div>
                        <div class="wv-radio-list-info d-flex fs-12 align-items-center justify-content-between flex-nowrap px-12 py-4 mb-4 br-4">
                            <span>Share 24m<sup>2</sup> or 49m<sup>2</sup> stand with co-exhibitors you invite</span>
                            <strong class="ls-5">OPTIONAL</strong>
                        </div>
                        <div class="wv-radio-list-info d-flex fs-12 align-items-center justify-content-between flex-nowrap px-12 py-4 br-4">
                            <span class="fw-600">Online payment of all exhibiting expenses</span>
                            <strong class="ls-5">COMPULSORY</strong>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Head Exhibitor -->
            <div class="col-lg-6">
                <label class="wv-custom-radio">
                    <input
                        type="radio"
                        name="wv_participationModel"
                        value="Head Exhibitor"
                        required
                        <?php echo (!empty($saved_data['wv_participationModel']) && $saved_data['wv_participationModel'] === 'Head Exhibitor') ? 'checked' : ''; ?>
                    >
                    <div class="wv-radio-card pt-48 pb-24">
                        <h3 class="h1">Head Exhibitor</h3>
                        <p class="fs-12 ls-6 text-uppercase fw-600 mt-12">MULTIPLE STANDS RENTAL  <span class="fw-700">• UPON REQUEST</span></p>
                        <div class="wv-check my-48">
                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                        </div>
                        <div class="wv-radio-list-info d-flex fs-12 align-items-center justify-content-between flex-nowrap px-12 py-4 mb-4 br-4">
                            <span>Invite members to register and assign stands</span>
                            <strong class="ls-5">COMPULSORY</strong>
                        </div>
                        <div class="wv-radio-list-info d-flex fs-12 align-items-center justify-content-between flex-nowrap px-12 py-4 br-4">
                            <span class="fw-600">Online payment of all exhibiting expenses</span>
                            <strong class="ls-5">COMPULSORY</strong>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Co-Exhibitor -->
            <div class="col-lg-6 d-none">
                <label class="wv-custom-radio">
                    <input
                        type="radio"
                        name="wv_participationModel"
                        value="Co-Exhibitor"
                        required
                        <?php echo (!empty($saved_data['wv_participationModel']) && $saved_data['wv_participationModel'] === 'Co-Exhibitor') ? 'checked' : ''; ?>
                    >
                    <div class="wv-radio-card pt-24 pb-24">
                        <h3 class="h1">Co-Exhibitor</h3>
                    </div>
                </label>
            </div>

        </div>
    </div>
</div>
