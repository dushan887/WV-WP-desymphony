<?php
/**
 * Partial: Image Cropper Field
 *
 * Renders a single field for an image upload + cropping UI.
 *
 * Accepts array $args for configuration:
 *
 * Required:
 *  - 'field_name'   => 'wv_user_avatar'    (the <input name=""> for your final hidden field)
 *  - 'field_id'     => 'wv_user_avatar'    (used for IDs in markup—prefer something unique if multiple fields)
 *
 * Optional:
 *  - 'current_url'  => (string) existing image URL (for editing), else empty.
 *  - 'label'        => (string) label to display above the field
 *  - 'placeholder'  => (string) placeholder image URL if no current image
 *  - 'max_size_mb'  => (int)    max upload size in MB, purely informational
 *
 *  - 'profile_key'  => (string) e.g. "profile", "product", "banner"... for your server-side logic
 *  - 'aspect_ratio' => (string) e.g. "1:1", "9:16", "16:9", ...
 *  - 'upload_action'=> (string) AJAX action name for the server handler, default "DS_process_cropped_image"
 *
 * Example usage:
 *   $args = [
 *     'field_name'   => 'wv_user_avatar',
 *     'field_id'     => 'wv_user_avatar',
 *     'current_url'  => $existing_image_url,
 *     'label'        => __('Profile Picture', 'wv-addon'),
 *     'placeholder'  => 'https://placehold.co/300',
 *     'max_size_mb'  => 2,
 *
 *     'profile_key'  => 'profile',
 *     'aspect_ratio' => '1:1',
 *     'upload_action'=> 'DS_process_cropped_image',
 *   ];
 *
 *   include wv-addon/public/views/partials/form-fields/cropper-field.php;
 */

if ( ! defined('ABSPATH') ) {
    exit;
}

// Pull in the arguments
$field_name   = $args['field_name']   ?? 'wv_image_field';
$field_id     = $args['field_id']     ?? $field_name; // if not provided, reuse field_name
$current_url  = $args['current_url']  ?? '';
$label        = $args['label']        ?? __('Upload Image', 'wv-addon');
$max_size_mb  = $args['max_size_mb']  ?? 2;

// Extended config
$profile_key  = $args['profile_key']    ?? 'profile';   // e.g. "product" or "banner"
$aspect_ratio = $args['aspect_ratio']   ?? '1:1';       // "1:1", "9:16", "16:9", ...
$upload_action= $args['upload_action']  ?? 'DS_process_cropped_image';
$requirements = $args['requirements']   ?? 'Frontal, center-aligned image of whole product & label visible on white (or no) background.';

$userIdPlaceholder = $args['placeholders']['user-id'] ?? '';

if ( empty( $args['placeholder'] ) ) {
    if ( preg_match( '/^(\d+):(\d+)$/', $aspect_ratio, $matches ) ) {
        $width  = $matches[1] * 50;
        $height = $matches[2] * 50;
        $placeholder = "https://placehold.co/{$width}x{$height}";
    } else {
        $placeholder = 'https://placehold.co/600';
    }
} else {
    $placeholder = $args['placeholder'];
}

// Derive a unique base for IDs used in this partial, so multiple fields can coexist:
$unique = sanitize_key( $field_id ); // e.g. "wv_user_avatar"

// The displayed filename if $current_url is set
$file_basename = $current_url ? basename($current_url) : '';

// We'll attach these unique IDs to the modal, crop image, etc.
$modal_id       = $unique . '-crop-modal';
$drop_area_id   = $unique . '-drop-area';
$crop_image_id  = $unique . '-crop-image';
$crop_save_id   = $unique . '-crop-save';
$crop_cancel_id = $unique . '-crop-cancel';
$input_file_id  = $unique . '-file-input';
?>

<div class="wv-input-group wv-file-upload-group">

    <!-- Label -->
    <label class="d-none ds-img-lbl" for="<?php echo esc_attr($field_id); ?>">
        <?php echo esc_html($label); ?> <span class="ds-required">*</span>
    </label>

    <!-- Hidden input to store the final cropped URL or path -->
    <input
        type="hidden"
        name="<?php echo esc_attr($field_name); ?>"
        id="<?php echo esc_attr($field_id . '-hidden'); ?>"
        value="<?php echo esc_attr($current_url); ?>"
    />

    <!-- The main container for the crop UI -->

    <div class="wv-file-preview p-32 h-100">
        <div class="wv-file-preview-container h-100" style="background-image: url('<?php echo esc_url( $current_url ? $current_url : $placeholder ); ?>'); background-size: contain !important;">
        </div>
    </div>
    <div
        class="wv-file-upload"
        data-user-id="<?php echo esc_attr($userIdPlaceholder); ?>"
        data-field-id="<?php echo esc_attr($field_id); ?>"
        data-field-name="<?php echo esc_attr($field_name); ?>"
        <?php if ( ! empty($args['placeholders']['user-id']) ) : ?>
            data-user-id="<?php echo esc_attr($args['placeholders']['user-id']); ?>"
        <?php endif; ?>
        data-hidden-input="#<?php echo esc_attr($field_id . '-hidden'); ?>"
        data-profile-key="<?php echo esc_attr($profile_key); ?>"
        data-aspect-ratio="<?php echo esc_attr($aspect_ratio); ?>"
        data-upload-action="<?php echo esc_attr($upload_action); ?>"
        data-modal-id="<?php echo esc_attr($modal_id); ?>"
        data-drop-area-id="<?php echo esc_attr($drop_area_id); ?>"
        data-crop-image-id="<?php echo esc_attr($crop_image_id); ?>"
        data-crop-save-id="<?php echo esc_attr($crop_save_id); ?>"
        data-crop-cancel-id="<?php echo esc_attr($crop_cancel_id); ?>"
        data-placeholder="<?php echo esc_url($placeholder); ?>"
    >

        <!-- 1) The cropping modal (uniquely identified by $modal_id) -->
        
        <?php if ($requirements != '') : ?>
        <div class="wv-crop-header text-center">
            <div class="modal-subtitle fs-14 mb-16">
                    <strong>Requirements: </strong><?php echo esc_html($requirements); ?>
                              
            </div>
        </div><!-- .wv-crop-header -->
        <?php endif; ?>  
        
        <div id="<?php echo esc_attr($modal_id); ?>" class="wv-crop-modal w-100" style="display:none;">
            <div class="wv-crop-container" style=" max-height: 500px; max-width: 100%;">
                
                <img id="<?php echo esc_attr($crop_image_id); ?>" src="" alt="Crop Preview">
            </div>
            <div class="wv-crop-actions text-center mt-16">
                <button
                    type="button"
                    id="<?php echo esc_attr($crop_save_id); ?>"
                    class="wv-button wv-button-default wv-button-pill wv-button-md mb-12"
                >
                    <?php esc_html_e('Save crop', 'wv-addon'); ?>
                </button>
                <button
                    type="button"
                    id="<?php echo esc_attr($crop_cancel_id); ?>"
                    class="wv-button wv-button-default wv-button-pill wv-button-md mb-12"
                >
                    <?php esc_html_e('Cancel', 'wv-addon'); ?>
                </button>
            </div>
        </div><!-- #<?php echo esc_attr($modal_id); ?> -->

        <!-- 2) The drag-drop area (uniquely identified by $drop_area_id) -->
        <div id="<?php echo esc_attr($drop_area_id); ?>" class="wv-file-drop-area w-100">
            <?php if ( $current_url ) : ?>
                <img
                    src="<?php echo esc_url($current_url); ?>"
                    alt="Uploaded Image"
                    class="wv-placeholder-img"
                >
            <?php else : ?>
                <img
                    src="<?php echo esc_url($placeholder); ?>"
                    alt="Upload Placeholder"
                    class="wv-placeholder-img d-none"
                >
                <div class="svg-placeholder row g-0 justify-content-center align-items-center">
                    <div class="col-lg-10">
                        <svg id="ds-auth-placholder" xmlns="http://www.w3.org/2000/svg" width="480" height="210" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 480 210" class="w-100 h-auto"> 
                        <g style="clip-path: url(#clippath);">
                            <path class="st-placeholder" d="M309.962,110.709c0-7.217-5.884-13.051-13.161-13.051s-13.161,5.834-13.161,13.051,5.883,13.05,13.161,13.05,13.161-5.854,13.161-13.05M340,176.249c0,4.778-3.948,8.751-8.826,8.751h-122.804c-1.954,0-3.715-.652-5.186-1.688-2.226-1.575-3.639-4.204-3.639-7.063v-22.263c2.051-.383,4.006-1.363,5.574-2.84l8.013-7.581,4.18,7.753-.135.135v16.044h105.172v-6.929l-33.406-27.54-13.935,14.471-20.651-21.897-2.091-3.609,10.723-2.744c4.18-1.075,7.354-4.452,8.109-8.675.755-4.203-1.026-8.464-4.548-10.9l-36.367-25.142h100.991c4.878,0,8.826,3.915,8.826,8.751v92.966ZM276.747,63.597h-10.238v-10.287h10.238v10.287ZM276.755,42.554h-10.244v-7.396h-7.458v-10.158h17.702v17.554ZM244.427,35.158h-28.735v-10.158h28.735v10.158ZM201.067,35.158h-28.737v-10.158h28.737v10.158ZM157.703,134.654h-17.703v-17.553h10.245v7.395h7.458v10.158ZM157.703,35.158h-7.458v7.396h-10.245v-17.554h17.703v10.158ZM140,53.312h10.244v21.138h-10.244v-21.138ZM140,85.207h10.244v21.136h-10.244v-21.136ZM172.33,124.492h13.122l.542,10.172h-13.664v-10.172ZM193.21,62.325l67.022,46.322-24.779,6.36,12.374,21.24-20.144,11.103-11.658-21.636-18.529,17.509-4.286-80.898ZM480,202V8c0-4.418-3.582-8-8-8H8C3.582,0,0,3.582,0,8v194c0,4.418,3.582,8,8,8h464c4.418,0,8-3.582,8-8"/>
                        </g>
                        </svg>
                    </div>
                </div>
                
            <?php endif; ?>

            <p class="small pt-12">
                <?php esc_html_e('Drag and drop to upload image or', 'wv-addon'); ?>
            </p>

            <button type="button" class="wv-button wv-button-default wv-button-pill wv-button-md wv-upload-btn mb-12">
                <?php esc_html_e('Upload image', 'wv-addon'); ?>
            </button>

            <!-- The actual file input (hidden) -->
            <input
                type="file"
                id="<?php echo esc_attr($input_file_id); ?>"
                class="wv-crop-input"
                accept="image/png, image/jpeg, image/webp"
                hidden
            >
        </div><!-- #<?php echo esc_attr($drop_area_id); ?> -->

        <!-- 3) The file info + remove button -->
        <div class="wv-file-preview">
            <?php if ( $current_url ) : ?>
                <span class="wv-file-name"><?php echo esc_html($file_basename); ?></span>
                <button type="button" class="wv-button wv-button-default wv-button-pill wv-button-md wv-remove-file" style="display:inline;">
                    &#x274C;
                </button>
            <?php else : ?>
                <span class="wv-file-name"><?php esc_html_e('No file selected', 'wv-addon'); ?></span>
                <button type="button" class="wv-button wv-button-default wv-button-pill wv-button-md wv-remove-file" style="display:none;">
                    &#x274C;
                </button>
            <?php endif; ?>
        </div><!-- .wv-file-preview -->

        <small>
            <?php
            /* translators: %d: max size in MB */
            printf(
                esc_html__('Supported files: .jpg .png .webp | Maximum upload file size: %dmb', 'wv-addon'),
                absint($max_size_mb)
            );
            ?>
        </small>

    </div><!-- .wv-file-upload -->
</div><!-- .wv-input-group -->
