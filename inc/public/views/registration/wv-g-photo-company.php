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

// If we already have a stored avatar URL, use it for the <img> preview
$profile_picture = isset($saved_data['wv_user-logo']) ? $saved_data['wv_user-logo'] : '';
?>

<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">
            
            <?php esc_html_e('COMPANY LOGO PICTURE', 'wv-addon'); ?>
        </h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Your company logo</h2>
            <p class="mb-24 text-uppercase ls-3">Shown on your stand listing and event materials</p>
        </div>

        <div class="row g-12 justify-content-center align-items-stretch">
            <div class="col-lg-8">
                <?php
                    // Include the partial
                   $field_args = [
                    'field_name'   => 'wv_user-logo',
                    'field_id'     => 'wv_user-logo-hidden',
                    'current_url'  => $profile_picture,
                    'label'        => __('Company Logo', 'wv-addon'),
                    'max_size_mb'  => 2,
                    'profile_key'  => 'company_logo',
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


