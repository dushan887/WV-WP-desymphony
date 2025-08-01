<?php if (!defined('ABSPATH')) { exit; } ?>

<?php
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file = basename(__FILE__, '.php'); // e.g. "wv-exhibitor-step-4"
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}

$saved_data = $_SESSION["wv_reg_{$step_key}"] ?? [];
$saved_reasons = (!empty($saved_data['wv_reasonsForVisiting']) && is_array($saved_data['wv_reasonsForVisiting']))
    ? $saved_data['wv_reasonsForVisiting']
    : [];

// The meta definition has the same 9 items for "wv_reasonsForVisiting"
$reasons_options = [
    'Creating new business opportunities',
    'Acquiring new suppliers',
    'Expanding professional network',
    'Exploring Balkan market',
    'Tasting Balkan wines, spirits and food',
    'Attending wine masterclasses',
    'Attending food masterclasses',
    'Tasting awarded wines',
    'Tasting awarded spirits',
    'Attending chef competition',
    'Attending hospitality presentations',
    'Last year\'s fair level of quality',
    'High recommendations about the fair',
    'Experiencing good entertainment',
    'Experiencing Belgrade nightlife',
    'None of the Above'
];
?>

<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">PARTICIPATION</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Taking part in WVOB25</h2>
            <p class="mb-24 text-uppercase ls-3">CHOOSE MULTIPLE OPTIONS</p>
        </div>

        <div class="row g-12 justify-content-center align-items-stretch">
            <div class="col-12 my-0">
                <label class="wv-label-block d-block my-0 text-center px-32 py-16">
                    <span>What are your reasons for participating?</span>
                </label>
            </div>
            <div class="col-12 my-0">
                <div class="d-block bg-white p-32 br-8 br-t-0 ds-min-h-350">
                    <div class="wv-inline-checkbox wv-inline-checkbox-2-col wv-inline-checkbox-light d-flex align-items-start flex-wrap g-8 px-48">

                        <?php foreach ($reasons_options as $option) {
                            $checked = in_array($option, $saved_reasons, true) ? 'checked' : '';
                            ?>
                            <label class="wv-custom-checkbox wv-custom-checkbox-small my-4">
                                <input
                                    type="checkbox"
                                    name="wv_reasonsForVisiting[]"
                                    value="<?php echo esc_attr($option); ?>"
                                    <?php echo $checked; ?>
                                >
                                <div class="wv-checkbox-card wv-checkbox-card-inline">
                                    <div class="wv-check"></div>
                                    <h6><?php echo esc_html($option); ?></h6>
                                </div>
                            </label>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
