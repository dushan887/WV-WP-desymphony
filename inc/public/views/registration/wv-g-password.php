<?php if (!defined('ABSPATH')) { exit; } ?>

<?php
// Determine step key for session.
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file     = basename(__FILE__, '.php'); // e.g. "wv-final-step-password"
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}
$saved_data = isset($_SESSION["wv_reg_{$step_key}"]) ? $_SESSION["wv_reg_{$step_key}"] : [];

// For the terms checkbox, stored data should be boolean.
$terms_checked = (isset($saved_data['terms_conditions']) && $saved_data['terms_conditions']) ? 'checked' : '';

$global_user_data = [];
foreach ($_SESSION as $key => $value) {
    if (strpos($key, 'wv_reg_') === 0 && is_array($value)) {
        $global_user_data = array_merge($global_user_data, $value);
    }
}
$profile_selection = $global_user_data['wv_participationModel'] ?? '';
$role = $global_user_data['wv_profileSelection'] ?? '';

// Helper function for easier access.
function val($key, $default = '') {
    global $saved_data;
    return isset($saved_data[$key]) ? esc_attr($saved_data[$key]) : $default;
}
?>
<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">
    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">ACCOUNT PASSWORD</h6>
    </div>
    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="row g-12 justify-content-center align-items-stretch">
            <div class="col-lg-8">
                <div class="wv-block wv-bg-step py-24 py-lg-32 px-24 px-lg-64 br-16 wv-shadow-sm wv-light-form">
                    <div class="row">
                        <!-- Password Field -->
                        <div class="col-12 py-16" style="border-bottom: 1px solid #fff">
                            <div class="wv-input-group">
                                <label for="wv_user_password" class="wv-color-w wv-color-ww">Password*</label>
                                <div class="wv-password-wrapper">
                                    <input type="password" id="wv_user_password" name="wv_user_password" required value="<?php echo val('wv_user_password'); ?>">
                                    <span class="wv-toggle-password wv wv_show" data-target="wv_user_password" aria-label="Show/Hide password"></span>

                                </div>
                                <small class="wv-color-w wv-color-ww">Minimum 10 characters with capital letters and numbers</small>
                            </div>
                        </div>
                        <!-- Confirm Password Field -->
                        <div class="col-12 py-16" <?php echo $role === 'Exhibitor' ? 'style="border-bottom: 1px solid #fff"' : ''; ?>>
                            <div class="wv-input-group">
                                <label for="wv_password_confirm" class="wv-color-w wv-color-ww">Confirm password*</label>
                                <div class="wv-password-wrapper">
                                    <input type="password" id="wv_password_confirm" name="wv_password_confirm" required value="<?php echo val('wv_password_confirm'); ?>">
                                    <span class="wv-toggle-password wv wv_show" data-target="wv_password_confirm" aria-label="Show/Hide password"></span>
                                </div>
                              
                                <small class="wv-color-w wv-color-ww">Re-type your password</small>
                                
                            </div>
                        </div>
                        <!-- Terms & Conditions -->
                        <div class="col-12 mt-12">                            
                            <?php if ($role === 'Exhibitor' ) :  ?>
                            <div class="wv-input-group">  
                                <p class="fs-12 wv-color-w wv-color-ww">
                                    By submitting your personal information, you have consented to the collection, storage, and use of this data. Your information will be used solely for the purposes of communication, registration, and providing relevant updates. We are committed to protecting your privacy and will not share your data with third parties without your explicit consent, except as required by law. For more details, please review our Privacy Policy. By creating an account, you have consented to participate in the 2025 Business Meetings Program. Wine Vision by Open Balkan Fair reserves all rights to evaluate, approve, or decline any submitted participant account, based on its assessment. If a participant does not pass the evaluation, Wine Vision by Open Balkan Fair is legally obligated to remove all data provided by the participant during registration and will not retain or utilize it in the future.
                                </p> 
                            </div>
                            <label class="wv-custom-checkbox wv-custom-checkbox-small h-auto">
                                <input type="checkbox" id="terms_conditions" name="terms_conditions" required <?php echo $terms_checked; ?>>
                                <div class="wv-checkbox-card wv-checkbox-card-inline align-items-center justify-content-center w-100">
                                    <div class="wv-check"></div>
                                    <h6 class="wv-color-w wv-color-ww fs-14">I have read these terms and I fully accept them.</h6>
                                </div>
                            </label>
                            <?php else: ?>
                            <label class="wv-custom-checkbox wv-custom-checkbox-small h-auto d-none">
                                <input type="checkbox" id="terms_conditions" name="terms_conditions" required checked>
                                <div class="wv-checkbox-card wv-checkbox-card-inline align-items-center justify-content-center w-100">
                                    <div class="wv-check"></div>
                                    <h6 class="wv-color-w wv-color-ww fs-14">I have read these terms and I fully accept them.</h6>
                                </div>
                            </label>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
