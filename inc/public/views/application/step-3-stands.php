<?php
/**
 * Step 3: Stand Selection (only if not Co-Exhibitor)
 */
if ( ! defined('ABSPATH') ) { exit; }

$saved_stand = isset($_SESSION['wv_app_stand']) ? sanitize_text_field($_SESSION['wv_app_stand']) : '';

// A list of stands or a custom approach with an SVG
$stands = [
	'A1'=>[ 'hall'=>'A','stand_no'=>1,'day'=>1,'price'=>500 ],
	'A2'=>[ 'hall'=>'A','stand_no'=>2,'day'=>1,'price'=>500 ],
	'B10'=>[ 'hall'=>'B','stand_no'=>10,'day'=>2,'price'=>700 ],
	// ...
];
?>
<h2>Step 3: Stand Selection</h2>
<p>Please select an available stand. (Example table-based approach)</p>

<form method="post">
    <?php wp_nonce_field( 'wv_ex_app_step_3' ); ?>

    <ul>
    <?php foreach ( $stands as $key => $st ):
        $label = 'Hall ' . $st['hall'] . ' / Stand# ' . $st['stand_no'] . ' / Day ' . $st['day'] . ' (€'.$st['price'].')';
        $checked = ($saved_stand === $key) ? 'checked' : '';
    ?>
        <li>
            <label>
                <input type="radio" name="app_stand" value="<?php echo esc_attr($key); ?>" <?php echo $checked; ?> />
                <?php echo esc_html($label); ?>
            </label>
        </li>
    <?php endforeach; ?>
    </ul>

    <div class="wv-form-nav">
        <button type="submit" name="navigation" value="prev">← Back</button>
        <button type="submit" name="navigation" value="next">Next →</button>
    </div>
</form>
