<?php
/**
 * Step 5: Presentation Space
 */
if ( ! defined('ABSPATH') ) { exit; }

$saved_presentation = isset($_SESSION['wv_app_presentation']) ? (array) $_SESSION['wv_app_presentation'] : [];

/*
We'll store something like:
  [
    'hall' => 'festive',
    'day'  => 2,
    'timeslots' => ['12-13','13-14'],
    'equipment' => ['lcd_projector','mic_system'],
  ]
*/

// We define possible halls
$halls = [
    'small'   => 'Small Hall (50 seats)',
    'large'   => 'Large Hall (100 seats)',
    'festive' => 'Festive Hall (150 seats)',
];
// Days
$days = [1,2,3];
// Timeslots
$timeslots = ['11-12','12-13','13-14','14-15','15-16','16-17','17-18','18-19','19-20'];

// Additional equipment
$equip_options = [
    'lcd_projector' => 'LCD Projector',
    'mic_system'    => 'Mic System',
    'whiteboard'    => 'Whiteboard',
];

$chosen_hall      = $saved_presentation['hall'] ?? '';
$chosen_day       = isset($saved_presentation['day']) ? absint($saved_presentation['day']) : 1;
$chosen_timeslots = $saved_presentation['timeslots'] ?? [];
$chosen_equips    = $saved_presentation['equipment'] ?? [];
?>
<h2>Step 5: Presentation Space</h2>
<form method="post">
    <?php wp_nonce_field( 'wv_ex_app_step_5' ); ?>

    <label for="app_presentation_hall">Select Hall:</label>
    <select id="app_presentation_hall" name="app_presentation[hall]">
        <option value="">-- choose --</option>
        <?php foreach ($halls as $k=>$lbl):
            $selected = ($chosen_hall === $k) ? 'selected' : '';
        ?>
        <option value="<?php echo esc_attr($k); ?>" <?php echo $selected; ?>>
            <?php echo esc_html($lbl); ?>
        </option>
        <?php endforeach; ?>
    </select>

    <br/><br/>

    <label for="app_presentation_day">Select Day:</label>
    <select id="app_presentation_day" name="app_presentation[day]">
        <?php foreach ($days as $d):
            $sel = ($d === $chosen_day) ? 'selected' : '';
        ?>
        <option value="<?php echo $d; ?>" <?php echo $sel; ?>>Day <?php echo $d; ?></option>
        <?php endforeach; ?>
    </select>

    <br/><br/>

    <label>Time Slots (You can pick multiple):</label><br/>
    <?php foreach ($timeslots as $ts):
        $checked = in_array($ts,$chosen_timeslots) ? 'checked' : '';
    ?>
        <label style="margin-right:12px;">
            <input type="checkbox" name="app_presentation[timeslots][]" value="<?php echo esc_attr($ts); ?>" <?php echo $checked; ?>>
            <?php echo esc_html($ts); ?>
        </label>
    <?php endforeach; ?>

    <br/><br/>

    <label>Equipment (Optional):</label><br/>
    <?php foreach ($equip_options as $ek=>$elbl):
        $ch = in_array($ek,$chosen_equips) ? 'checked' : '';
    ?>
        <label style="margin-right:12px;">
            <input type="checkbox" name="app_presentation[equipment][]" value="<?php echo esc_attr($ek); ?>" <?php echo $ch; ?>>
            <?php echo esc_html($elbl); ?>
        </label>
    <?php endforeach; ?>

    <br/><br/>

    <div class="wv-form-nav">
        <button type="submit" name="navigation" value="prev">← Back</button>
        <button type="submit" name="navigation" value="next">Next →</button>
    </div>
</form>
