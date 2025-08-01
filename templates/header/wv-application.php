<?php
$user_id = get_current_user_id();
$wv_ex_stage1_verified = get_user_meta($user_id, 'wv_ex_stage1_verified', true);
$wv_ex_stage2_verified = get_user_meta($user_id, 'wv_ex_stage2_verified', true);

$is_stage1 = ($wv_ex_stage1_verified !== '1' && $wv_ex_stage2_verified !== '1');
?>

<div id="wv-application-bar" class="text-center wv-bg-exhibitor-gradient-dark">
    <div class="d-block px-16 py-16">
        <div class="my-0 text-uppercase ls-3 fw-400 wv-color-w">
            <span>EXHIBITOR APPLICATION FORM</span>
            <span class="fw-600">
                <span class="wv wv_point-m35-f"></span>
                STEP <?php echo $is_stage1 ? '1' : '2'; ?>
            </span>
        </div>
    </div>
    <div class="d-block">
        <div class="wv-progress-bar">
            <div id="wv-step-1" class="wv-progress-pill<?php echo $is_stage1 ? ' active' : ''; ?>"></div>
            <div id="wv-step-2" class="wv-progress-pill<?php echo !$is_stage1 ? ' active' : ''; ?>"></div>
        </div>
    </div>
    <div class="d-block px-16 py-16">
        <h4 class="my-0 fw-500 wv-color-w">
            <?php echo $is_stage1 ? 'Stand rental' : 'Add products'; ?>
        </h4>
    </div>
</div>
<div id="wv-reg-messages"></div>
