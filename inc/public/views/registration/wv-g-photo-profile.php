<?php if ( ! defined('ABSPATH') ) { exit; } ?>

<?php
// Determine step key for session.
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file     = basename(__FILE__, '.php'); // e.g. "wv-final-step-upload"
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}

$saved_data = isset($_SESSION["wv_reg_{$step_key}"]) ? $_SESSION["wv_reg_{$step_key}"] : array();
$global_user_data = [];
foreach ($_SESSION as $key => $value) {
    if (strpos($key, 'wv_reg_') === 0 && is_array($value)) {
        $global_user_data = array_merge($global_user_data, $value);
    }
}
$profile_selection = $global_user_data['wv_participationModel'] ?? '';
// If we already have a stored avatar URL, use it for the <img> preview
$profile_picture = isset($saved_data['wv_user-avatar']) ? $saved_data['wv_user-avatar'] : '';
?>

<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">
            <?php if ($profile_selection !== 'Public Visitor') : ?>
            <?php esc_html_e('REPRESENTATIVE PROFILE PICTURE', DS_THEME_TEXTDOMAIN); ?>
            <?php else : ?>
            <?php esc_html_e('PROFILE PICTURE', DS_THEME_TEXTDOMAIN); ?>
            <?php endif; ?>
        </h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <?php if ($profile_selection !== 'Public Visitor') : ?>
                <h2 class="text-white mt-0 mb-12 h1">Representative’s photo</h2>
            <?php else : ?>
                <h2 class="text-white mt-0 mb-12 h1">Profile photo</h2>
            <?php endif; ?>
            <p class="mb-24 text-uppercase ls-3"> Used for the personal badge and profile</p>
        </div>

        <div class="row g-12 justify-content-center align-items-stretch">
            <div class="col-lg-8">
                <?php
                    // Include the partial
                    $field_args = [
                        'field_name'   => 'wv_user-avatar',
                        'field_id'     => 'wv_user-avatar-hidden',
                        'current_url'  => $profile_picture,
                        'label'        => __('Profile Image', DS_THEME_TEXTDOMAIN),
                        'max_size_mb'  => 2,
                        'profile_key'  => 'profile', 
                        'aspect_ratio' => '1:1',
                        'upload_action' => 'wv_crop_upload',
                        'placeholder' => 'svg',
                        'requirements' => '',
                        'placeholders' => [
                        'user-id' => $this->get_temp_folder_name(),
                        ],
                    ];
                    $partial_path = DS_THEME_DIR . '/inc/public/views/partials/form-fields/cropper-field.php';
                    if ( file_exists($partial_path) ) {
                        $args = $field_args; // rename to $args if partial expects $args
                        include $partial_path;
                    } else {
                        echo '<p class="wv-error">Cropper partial missing.</p>';
                    }
                ?>
            </div>
        </div>
    </div>
</div>


