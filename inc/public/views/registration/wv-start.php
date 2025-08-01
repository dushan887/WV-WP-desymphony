<?php if (!defined('ABSPATH')) { exit; } ?>

<?php
// Determine which step key to use in $_SESSION
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file = basename(__FILE__, '.php'); // e.g. "wv-global-step-1"
    // Remove a leading "wv-" if present and convert dashes to underscores.
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}
$saved_data = isset($_SESSION["wv_reg_{$step_key}"]) ? $_SESSION["wv_reg_{$step_key}"] : [];
?>

<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">CHOOSE PROFILE</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">
                <span class="d-none d-lg-inline-block">Exhibitor, Professional Buyer or Visitor?</span>
                <span class="d-lg-none">Exhibitor, Pro-Buyer or Visitor?</span>
            </h2>
            <p class="mb-24 text-uppercase ls-3">CHOOSE SINGLE OPTION</p>
        </div>

        <div class="row g-12 justify-content-center align-items-stretch">
            
            <!-- Exhibitor Option -->
            <div class="col-lg-4">
                <label class="wv-custom-radio">
                    <input type="radio" 
                        name="wv_profileSelection" 
                        value="Exhibitor"
                        <?php echo (isset($saved_data['wv_profileSelection']) && $saved_data['wv_profileSelection'] === 'Exhibitor') ? 'checked' : ''; ?>>
                    <div class="wv-radio-card py-48">
                    <h3 class="h1">Exhibitor</h3>
                        <div class="wv-check my-48">
                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                        </div>
                        <ul class="d-flex d-lg-block flex-wrap justify-content-center">
                            <li class="mx-4">Showcase your products</li>
                            <li class="mx-4">Attend business meetings</li>
                            <li class="mx-4">Trade with buyers</li>
                        </ul>
                    </div>
                </label>
            </div>           
            
            <!-- Buyer Option -->
            <div class="col-lg-4">
                <label class="wv-custom-radio">
                    <input type="radio" 
                        name="wv_profileSelection" 
                        value="Buyer"
                        <?php echo (isset($saved_data['wv_profileSelection']) && $saved_data['wv_profileSelection'] === 'Buyer') ? 'checked' : ''; ?>>
                    <div class="wv-radio-card py-48">
                    <h3 class="h1">Pro-Buyer</h3>
                        <div class="wv-check my-48">
                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                        </div>
                        <ul class="d-flex d-lg-block flex-wrap justify-content-center">
                            <li class="mx-4">Attend business meetings</li>
                            <li class="mx-4">Expand your professional network</li>
                            <li class="mx-4">Trade with exhibitors</li>
                        </ul>
                    </div>
                </label>
            </div>

            <!-- Visitor Option -->
            <div class="col-lg-4">
                <label class="wv-custom-radio">
                    <input type="radio" 
                        name="wv_profileSelection" 
                        value="Visitor"
                        <?php echo (isset($saved_data['wv_profileSelection']) && $saved_data['wv_profileSelection'] === 'Visitor') ? 'checked' : ''; ?>>
                    <div class="wv-radio-card py-48">
                        <h3 class="h1">Visitor</h3>
                        <div class="wv-check my-48">
                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                        </div>
                        <ul class="d-flex d-lg-block flex-wrap justify-content-center">
                            <li class="mx-4">Experience new flavors</li>
                            <li class="mx-4">Promote your company</li>
                            <li class="mx-4">Save favorites</li>
                        </ul>
                    </div>
                </label>
            </div>

        </div>
    </div>
</div>
