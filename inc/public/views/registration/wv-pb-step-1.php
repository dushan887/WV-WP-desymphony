<?php if (!defined('ABSPATH')) { exit; } ?>

<?php
// Determine step key for session.
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file = basename(__FILE__, '.php'); // e.g. "wv-category-step"
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}

// Retrieve saved data for the current step.
$saved_data = $_SESSION["wv_reg_{$step_key}"] ?? [];

// Merge all registration session data to access global fields.
$global_user_data = [];
foreach ($_SESSION as $key => $value) {
    if (strpos($key, 'wv_reg_') === 0 && is_array($value)) {
        $global_user_data = array_merge($global_user_data, $value);
    }
}

// Retrieve the selected profile from the global session data.
$profile_selection = $global_user_data['wv_profileSelection'] ?? '';

// Set the categories based on the profile selection.
switch ($profile_selection) {
    case 'Buyer':
        $categories = [
            'Importer',
            'Wholesaler',
            'Retailer',
            'HORECA',
            'Sales Agent',
            'Chamber of Commerce',
            'Export / Import',
            'Market Trends Analysis',
            'Business Consulting',
            'Sales and Marketing',
            'Industry Promotion',
            'Events Production',
            'Distributor',
            'Culture and Tourism',
            'Catering',
            'Other'
        ];
        break;
    case 'Visitor':
        $categories = [
            'Business and Trading',
            'Industry Expert',
            'Food and Beverage',
            'Media and Influencing',
            'Sales Agent',
            'Chamber of Commerce',
            'Export / Import',
            'Market Trends Analysis',
            'Business Consulting',
            'Sales and Marketing',
            'Industry Promotion',
            'Events Production',
            'Distributor',
            'Culture and Tourism',
            'Catering',
            'Other'
        ];
        break;
    default:
        $categories = []; // or provide a default set or error message as needed.
        break;
}
?>

<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">PROFESSIONAL ACTIVITIES CATEGORY</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Select your category</h2>
            <p class="mb-24 text-uppercase ls-3">CHOOSE SINGLE OPTION</p>
        </div>

        <div class="row justify-content-center align-items-stretch g-12">
            <?php foreach ($categories as $cat):
                $checked = (!empty($saved_data['wv_userCategory']) && $saved_data['wv_userCategory'] === $cat) ? 'checked' : '';
            ?>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <label class="wv-custom-radio">
                    <input
                        type="radio"
                        name="wv_userCategory"
                        value="<?php echo esc_attr($cat); ?>"
                        required
                        <?php echo $checked; ?>
                    >
                    <div class="wv-radio-card wv-radio-card-inline wv-radio-card-inline-4">
                        <div class="wv-check">
                            <span class="wv wv_check-50"><span class="path1"></span><span class="path2"></span></span>
                        </div>
                        <h3 class="h6 ps-8"><?php echo esc_html($cat); ?></h3>
                    </div>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
