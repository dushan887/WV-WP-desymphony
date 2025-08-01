<?php
/**
 * Step 4: Additional Equipment / Services
 */
if ( ! defined('ABSPATH') ) { exit; }

$saved_equipment = isset($_SESSION['wv_app_equipment']) ? (array) $_SESSION['wv_app_equipment'] : [];

/*
We might store each line item by an ID or slug, 
like [ 'power_20kw'=>2, 'lan_50mb'=>1, 'lcd_tv_55'=>1 ] 
representing quantities
*/

$equipment_list = [
	[ 'id'=>'power_20kw','label'=>'Total Power up to 20kW','price'=>100 ],
	[ 'id'=>'lan_50mb','label'=>'LAN (Up to 50 / 50Mb/s)',  'price'=>80  ],
	[ 'id'=>'lcd_tv_55','label'=>'LCD TV 55"',              'price'=>120 ],
	[ 'id'=>'technician','label'=>'Technician Service (Per 1h)','price'=>50 ],
	[ 'id'=>'water_sink','label'=>'Water Connection with Sink','price'=>100 ],
];
?>
<h2>Step 4: Equipment & Services</h2>

<form method="post">
    <?php wp_nonce_field( 'wv_ex_app_step_4' ); ?>

    <table>
        <thead>
            <tr><th>Item</th><th>Price</th><th>Qty</th></tr>
        </thead>
        <tbody>
        <?php foreach ( $equipment_list as $eq ):
            $eq_id   = $eq['id'];
            $eq_lbl  = $eq['label'];
            $eq_price= $eq['price'];
            $eq_qty  = isset($saved_equipment[$eq_id]) ? absint($saved_equipment[$eq_id]) : 0;
        ?>
            <tr>
                <td><?php echo esc_html($eq_lbl); ?></td>
                <td>€<?php echo esc_html($eq_price); ?></td>
                <td>
                    <input type="number" name="app_equipment[<?php echo esc_attr($eq_id); ?>]" value="<?php echo $eq_qty; ?>" min="0" />
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="wv-form-nav">
        <button type="submit" name="navigation" value="prev">← Back</button>
        <button type="submit" name="navigation" value="next">Next →</button>
    </div>
</form>
