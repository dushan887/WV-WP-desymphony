<?php
/**
 * Step 6: Review & Submit
 */
if ( ! defined('ABSPATH') ) { exit; }

// Grab everything
$app_products     = $_SESSION['wv_app_products']     ?? [];
$app_ads          = $_SESSION['wv_app_ads']          ?? [];
$app_stand        = $_SESSION['wv_app_stand']        ?? '';
$app_equipment    = $_SESSION['wv_app_equipment']    ?? [];
$app_presentation = $_SESSION['wv_app_presentation'] ?? [];

?>
<h2>Step 6: Review & Submit</h2>
<p>Below is a summary of what you chose. If everything looks correct, click "Finish". Otherwise, go back and make changes.</p>

<form method="post">
    <?php wp_nonce_field( 'wv_ex_app_step_6' ); ?>

    <!-- SHOW PRODUCTS -->
    <h3>Products</h3>
    <?php if ( empty($app_products) ): ?>
        <p>No products selected.</p>
    <?php else: ?>
        <ul>
        <?php foreach ($app_products as $pid):
            // Optionally fetch product title from DB
            // For now, just show ID
        ?>
            <li>Product ID: <?php echo esc_html($pid); ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- ADS -->
    <h3>Advertisements</h3>
    <?php if ( empty($app_ads) ): ?>
        <p>No ads selected.</p>
    <?php else: ?>
        <ul>
        <?php foreach ($app_ads as $ad_slug=>$qty):
            if (!$qty) continue;
            echo '<li>' . esc_html($ad_slug) . ' x ' . intval($qty) . '</li>';
        endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- STAND -->
    <h3>Stand</h3>
    <?php if ( $app_stand ): ?>
        <p>You chose stand: <?php echo esc_html($app_stand); ?></p>
    <?php else: ?>
        <p>No stand selected (maybe co-exhibitor or skipped).</p>
    <?php endif; ?>

    <!-- EQUIPMENT -->
    <h3>Equipment</h3>
    <?php if ( empty($app_equipment) ): ?>
        <p>No additional equipment chosen.</p>
    <?php else: ?>
        <ul>
        <?php foreach ($app_equipment as $eq_slug=>$qty):
            if (!$qty) continue;
            echo '<li>' . esc_html($eq_slug) . ' x ' . intval($qty) . '</li>';
        endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- PRESENTATION -->
    <h3>Presentation Space</h3>
    <?php if ( empty($app_presentation) ): ?>
        <p>No presentation space selected.</p>
    <?php else: 
        // e.g. show the user’s chosen hall, day, timeslots, equipment
        $hall       = $app_presentation['hall'] ?? '';
        $day        = $app_presentation['day'] ?? '';
        $timeslots  = $app_presentation['timeslots'] ?? [];
        $equips     = $app_presentation['equipment'] ?? [];
    ?>
        <p>Hall: <?php echo esc_html($hall); ?>, Day <?php echo esc_html($day); ?></p>
        <p>Timeslots: <?php echo implode(', ', array_map('esc_html', $timeslots)); ?></p>
        <p>Equipment: <?php echo implode(', ', array_map('esc_html', $equips)); ?></p>
    <?php endif; ?>

    <br/>

    <div class="wv-form-nav">
        <button type="submit" name="navigation" value="prev">← Back</button>
        <button type="submit" name="navigation" value="next">Finish</button>
    </div>
</form>
