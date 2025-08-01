<?php if (!defined('ABSPATH')) { exit; } ?>

<?php
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file     = basename(__FILE__, '.php');
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}

$saved_data = $_SESSION["wv_reg_{$step_key}"] ?? [];

// Helper for shorter code:
function val($key, $default='') {
    global $saved_data;
    return isset($saved_data[$key]) ? esc_attr($saved_data[$key]) : $default;
}
?>

<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">COMPANY CREDENTIALS</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-128">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Your general information</h2>
            <p class="mb-24 text-uppercase ls-3">ALL FIELDS ARE OPTIONAL</p>
        </div>

        <!-- Row Container -->
        <div class="row g-12 justify-content-start align-items-start">

            <!-- ID Registry Number -->
            <div class="col-lg-3">
                <div class="wv-input-group">
                    <label for="wv_company_idRegistryNumber">ID registry number</label>
                    <input
                        type="text"
                        id="wv_company_idRegistryNumber"
                        name="wv_company_idRegistryNumber"
                        value="<?php echo val('wv_company_idRegistryNumber'); ?>"
                        
                    />
                </div>
            </div>

            <!-- VAT Registry Number -->
            <div class="col-lg-3">
                <div class="wv-input-group">
                    <label for="wv_company_vatRegistryNumber">VAT registry number *</label>
                    <input
                        type="text"
                        id="wv_company_vatRegistryNumber"
                        name="wv_company_vatRegistryNumber"
                        value="<?php echo val('wv_company_vatRegistryNumber'); ?>"
                        required
                        
                    />
                </div>
            </div>

            <!-- IBAN -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_company_iban">IBAN</label>
                    <input
                        type="text"
                        id="wv_company_iban"
                        name="wv_company_iban"
                        value="<?php echo val('wv_company_iban'); ?>"
                        
                    />
                </div>
            </div>

            <!-- Foreign Exchange Correspondent Bank -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_company_foreignBank">
                        Foreign exchange correspondent bank
                    </label>
                    <input
                        type="text"
                        id="wv_company_foreignBank"
                        name="wv_company_foreignBank"
                        value="<?php echo val('wv_company_foreignBank'); ?>"
                        
                    />
                </div>
            </div>

            <!-- Domestic Exchange Bank -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_company_domesticBank">Domestic exchange bank</label>
                    <input
                        type="text"
                        id="wv_company_domesticBank"
                        name="wv_company_domesticBank"
                        value="<?php echo val('wv_company_domesticBank'); ?>"
                        
                    />
                </div>
            </div>

            <!-- Foreign Exchange Account Number -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_company_foreignAccountNumber">
                        Foreign exchange account number
                    </label>
                    <input
                        type="text"
                        id="wv_company_foreignAccountNumber"
                        name="wv_company_foreignAccountNumber"
                        value="<?php echo val('wv_company_foreignAccountNumber'); ?>"
                        
                    />
                </div>
            </div>

            <!-- Domestic Exchange Account Number -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_company_domesticAccountNumber">
                        Domestic exchange account number
                    </label>
                    <input
                        type="text"
                        id="wv_company_domesticAccountNumber"
                        name="wv_company_domesticAccountNumber"
                        value="<?php echo val('wv_company_domesticAccountNumber'); ?>"
                        
                    />
                </div>
            </div>

            <!-- Foreign Exchange Swift Code -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_company_foreignSwift">
                        Foreign exchange swift code
                    </label>
                    <input
                        type="text"
                        id="wv_company_foreignSwift"
                        name="wv_company_foreignSwift"
                        value="<?php echo val('wv_company_foreignSwift'); ?>"
                        
                    />
                </div>
            </div>

            <!-- Beneficiary Swift Code -->
            <div class="col-lg-6">
                <div class="wv-input-group">
                    <label for="wv_company_domesticSwift">
                        Beneficiary swift code
                    </label>
                    <input
                        type="text"
                        id="wv_company_domesticSwift"
                        name="wv_company_domesticSwift"
                        value="<?php echo val('wv_company_domesticSwift'); ?>"
                        
                    />
                </div>
            </div>

        </div> <!-- .row -->
    </div> <!-- #wv-step-body -->
</div> <!-- .wv-step -->
