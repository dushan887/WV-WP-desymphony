<?php if (!defined('ABSPATH')) { exit; } ?>

<?php
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file = basename(__FILE__, '.php'); // e.g. "wv-exhibitor-step-3"
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}

$saved_data = $_SESSION["wv_reg_{$step_key}"] ?? [];

$global_user_data = [];
foreach ($_SESSION as $key => $value) {
    if (strpos($key, 'wv_reg_') === 0 && is_array($value)) {
        $global_user_data = array_merge($global_user_data, $value);
    }
}
$profile_selection = $global_user_data['wv_participationModel'] ?? '';
$profile = $global_user_data['wv_profileSelection'] ?? '';

?>

<div class="wv-step wv-step-terms" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-24 text-center position-relative">
        <h6 class="my-0 text-uppercase ls-3 fw-600 wv-color-w wv-color-ww">
            <?php if ($profile === 'Exhibitor') : ?>
            RULES UPON PARTICIPATION AT WINE VISION FAIR & THE CONTRACT SPECIAL CONDITIONS
            <?php else : ?>
            2025 RULES OF PARTICIPATION
            <?php endif; ?>
        </h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="position-relative py-24 wv-color-w wv-color-ww" style="padding-inline: 0 !important;">
        <div class="wv-overlay-shadow-top"></div>
        <div class="wv-terms-inner px-12 px-lg-64 mx-12">
            <div class="container py-48 px-0 container-1024">
                <div class="registration-content">
                <!-- Registration content Exhibitors -->
                <?php if ($profile === 'Exhibitor') : ?>
                
                    <h3>EXHIBITORS</h3>
                    <p class="wv-color-w wv-color-ww opacity-75">
                        Companies or individuals renting exhibition space for their product range display are entitled to the registration in the official Fair Catalogue, inscription on fascia panel and the right to use all other Belgrade Fair services. The exhibitor agrees to respect the opening and closing times of the fair event, and in particular that the stand with exhibits will be in operation until the event closes.
                    </p>
                    <hr>
                    <h3>CO-EXHIBITORS</h3>
                    <p class="wv-color-w wv-color-ww opacity-75">
                        If the stand area rented by the Exhibitor is used by another company represented by its own exhibits and staff, such company shall apply as Co-Exhibitor and shall submit a separate Application form and pay the Registration fee for compulsory Catalogue listing. Co-Exhibitors shall have their inscription on the fascia panel alongside Exhibitor’s inscription. If the Exhibitor applies for the Co-Exhibitor’s participation and Fair Catalogue listing, he shall submit Co-Exhibitor’s written approval with the Application form. The Exhibitor has the right to install inscriptions of Co-Exhibitors as stated above on his own stand only if he applied for such Co-Exhibitors to Belgrade Fair. If the Exhibitor does not apply for Co-Exhibitors or submit incomplete data in its Application form, Belgrade Fair has the right to charge the Exhibitor for all the costs related to Registration fees for Co-Exhibitors.
                    </p>
                    <hr>
                    <h3>CONTRACTING</h3>
                    <p class="wv-color-w wv-color-ww opacity-75">
                        The Exhibitor may withdraw the submitted Application for Participation not later than 15 (fifteen) days upon the Application registration in Belgrade Fair Archive Office. If Belgrade Fair does not inform the applicant about its decision, it will be considered that the application for participation has been accepted. Should the Exhibitor withdraw the Application after the afore mentioned date, the Exhibitor shall pay to Belgrade Fair the rent fee for the applied exhibiting space and any other expenses caused by the Exhibitor’s participation cancellation.
                    </p>
                    <p class="wv-color-w wv-color-ww opacity-75 fw-600">
                        The price of the compulsory registration fee includes the following services for each company separately:
                    </p>
                    <ul class="wv-color-w wv-color-ww opacity-75">
                        <li>Publishing of the exhibitor’s data (complete business address, telephone, E-mail, http and representative office address) in the exhibitor’s register - in the official catalog and/or USB and/or on the B2B portal</li>
                        <li>Publishing up to 20 words of text describing Exhibitor’s product range or activity in the Exhibitor Register</li>
                        <li>Publishing of Exhibitor’s registered trade mark</li>
                        <li>A Complimentary copy of the Register</li>
                        <li>Complimentary exhibitor passes - 3 pcs.</li>
                        <li>One exhibitor pass for each co-exhibitor</li>
                        <li>Free pedestrian passes, according to the following key: one pedestrian pass for renting up to 20 sq.m, one more for each additional 10 sq.m of rented exhibition space, up to a total of 35 passes</li>
                        <li>Parking passes for one vehicle for exhibitors up to 20 sq.m rented space including co-exhibitors; for two vehicles from 21 to 50 sq.m, for three vehicles from 51 to 100 sq.m, for five vehicles from 101 to 300 sq.m, seven vehicles over 301 sq.m.</li>
                    </ul>
                    <p class="wv-color-w wv-color-ww opacity-75">
                        Note: exhibitors and co-exhibitors have the option of purchasing additional official passes and parking passes
                    </p>
                    <p class="wv-color-w wv-color-ww opacity-75">
                        The official Catalogue is printed in Serbian and English. Exhibitors are responsible for the accuracy of the data given. The Catalogue Editorial Staff reserves the right to adjust the titles of the products stated in in this Application to those contained in the Catalogue nomenclature.
                    </p>
                    <hr>
                    <h3>EXHIBIT INSURANCE</h3>
                    <p class="wv-color-w wv-color-ww opacity-75">
                        Exhibits and any other Exhibitor’s property shall be insured against theft, damage, etc. at all fair events taking place at Belgrade Fair. The insurance of exhibits and any other Exhibitor’s property shall cover the time period from the good unloading at Belgrade Fair, mounting, the event duration, dismantle, up to the goods loading into a transportation vehicle while leaving Belgrade Fair premises. The Exhibitor may insure his goods through an insurance company in this country or abroad. Should the exhibits or any other Exhibitor’s property not be insured in one of the ways mentioned, the Exhibitor shall be fully responsible for any damage to such exhibits or other property.
                    </p>
                    <p class="wv-color-w wv-color-ww opacity-75 fw-600">
                        Application form has the legal force of a Contract. In case of any disputes, the Contract Parties have agreed to settle such disputes by the Foreign Trade Arbitration with the Serbian Chamber of Commerce in Belgrade.<br>Regulations for Participation at Belgrade Fair Events and the Contract Special Conditions shall be a form part of the Contract.
                    </p>
                    
                <?php elseif ($profile === 'Buyer' || $profile_selection === 'Company' ) : ?>
                    <h3>Personal Information</h3>
                    <p>By submitting your personal information, you have consented to the collection, storage, and use of this data. Your information will be used solely for the purposes of communication, registration, and providing relevant updates. We are committed to protecting your privacy and will not share your data with third parties without your explicit consent, except as required by law. For more details, please review our <a href="https://example.com/privacy-policy" class="fw-600 wv-color-w wv-color-ww" target="_blank">Privacy Policy</a>.</p>
                    <h3>Consent and Evaluation</h3>
                    <p>By creating an account, you have consented to participate in the 2025 Business Meetings Program. Wine Vision by Open Balkan Fair reserves all rights to evaluate, approve, or decline any submitted participant account, including application for the Wine Vision Hosted Buyers Program, based on its assessment. If a participant does not pass the evaluation, Wine Vision by Open Balkan Fair is legally obligated to remove all data provided by the participant during registration and will not retain or utilize it in the future.</p>
                <?php elseif ($profile_selection === 'Public Visitor') : ?>
                    <h3>Personal Information</h3>
                    <p>By submitting your personal information, you have consented to the collection, storage, and use of this data. Your information will be used solely for the purposes of communication, registration, and providing relevant updates. We are committed to protecting your privacy and will not share your data with third parties without your explicit consent, except as required by law. For more details, please review our <a href="https://example.com/privacy-policy" class="fw-600 wv-color-w wv-color-ww" target="_blank">Privacy Policy</a>.</p>
                    <h3>Evaluation</h3>
                    <p>Wine Vision by Open Balkan Fair reserves all rights to evaluate, approve, or decline any submitted account, based on its assessment. If an applicant does not pass the evaluation, Wine Vision by Open Balkan Fair is legally obligated to remove all data provided by the applicant during registration and will not retain or utilize it in the future.</p>
                <?php endif; ?>   
                <hr>
                    <div class="wv-input-group position-relative" style="z-index: 100;">
                        <label class="wv-custom-checkbox wv-custom-checkbox-small mb-128 d-block">
                            <input type="checkbox" id="terms_conditions_final" name="terms_conditions_final" required >
                            <div class="wv-checkbox-card wv-checkbox-card-inline align-items-center justify-content-center w-100">
                                <div class="wv-check"></div>
                                 <?php if ($profile === 'Exhibitor') : ?>                                
                                    <h6 class="wv-color-w wv-color-ww fs-14 fw-400 ps-8">I declare hereby that I am aware of the participation conditions, mentioned in the <a href="https://sajam.rs/wp-content/uploads/pravilnik-2017-ENG.pdf" class="fw-600 wv-color-w wv-color-ww" target="_blank">General Rules of Participation at Belgrade Fair Events</a> and the <a href="https://sajam.rs/wp-content/uploads/Rules_Upon_Participation_at_Wine_Vision_Fair_and_the_Contract_Special_Conditions.pdf" class="fw-600 wv-color-w wv-color-ww" target="_blank">Rules Upon Participation at Wine Vision Fair and the Contract Special Conditions</a> and that I fully accept them.</h6>
                                <?php else : ?>
                                    <h6 class="wv-color-w wv-color-ww fs-14 fw-400 ps-8">I have read these terms and I fully accept them.</h6>
                                <?php endif; ?>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="wv-overlay-shadow-bottom"></div>
    </div>
</div>