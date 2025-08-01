<?php
namespace Desymphony\Woo;

defined('ABSPATH') || exit;

/**
 * DS_Woo_Stand_Addons
 *
 * Returns a list of possible "Add-On" items for a given stand size.
 * You have exactly 2 groups:
 * - Group A: for sizes 9, 12, 24, 49  (3 items)
 * - Group B: for any other size       (full list)
 */
class DS_Woo_Stand_Addons {

    /**
     * Return array of add-ons for the given stand size.
     * Example return: [
     *    ['slug'=>'chair','label'=>'Extra Chair','price'=>30],
     *    ['slug'=>'table','label'=>'Extra Table','price'=>50],
     * ]
     */
    public static function get_addons_for_size($stand_size) {
        // Convert something like "12" or "12m2" -> 12
        // (If stand_size is just "12" already, no problem.)
        $size_val = intval(preg_replace('/\D/', '', $stand_size));

        // Group A: 9,12,24,49 => 3 items
        $addons_group_a = [
            ['slug'=>'table',   'label'=>'Additional Table',  'price'=>30, 'unit'=>'Pcs'],
            ['slug'=>'chair',   'label'=>'Additional Chair',  'price'=>20, 'unit'=>'Pcs'],
            ['slug'=>'lcd_screen',  'label'=>'Additional LCD Screen','price'=>100, 'unit'=>'Pcs'],
        ];

        // Group B: everything else => bigger list
        $addons_group_b = [
            ['slug' => 'water-connection-with-sink',              'label' => 'Water Connection with Sink',                'price' => 70.00,  'unit' => 'Pcs'],
            ['slug' => 'water-connection-without-sink',           'label' => 'Water Connection without Sink',             'price' => 70.00,  'unit' => 'Pcs'],
            ['slug' => 'additional-connection-at-the-booth',      'label' => 'Additional Connection at the Booth',        'price' => 30.00,  'unit' => 'Pcs'],
            ['slug' => 'flow-boiler',                             'label' => 'Flow Boiler',                               'price' => 25.00,  'unit' => 'Pcs'],
            ['slug' => 'kitchen-water-connection',                'label' => 'Kitchen / Water Connection',                'price' => 130.00, 'unit' => 'Pcs'],
            ['slug' => 'kitchen-water-connection-hot-plate',      'label' => 'Kitchen / Water Connection / Hot Plate',    'price' => 150.00, 'unit' => 'Pcs'],
            ['slug' => 'coffee-machine-96-capsules',              'label' => 'Coffee Machine / 96 Capsules',              'price' => 100.00, 'unit' => 'Pcs'],
            ['slug' => 'additional-96-capsules',                  'label' => 'Additional 96 Capsules',                   'price' => 90.00,  'unit' => 'Pcs'],
            ['slug' => 'refrigerator',                            'label' => 'Refrigerator',                              'price' => 90.00,  'unit' => 'Pcs'],
            ['slug' => 'total-power-up-to-10kw',                  'label' => 'Total Power up to 10kW',                    'price' => 124.00, 'unit' => 'Pcs'],
            ['slug' => 'total-power-up-to-20kw',                  'label' => 'Total Power up to 20kW',                    'price' => 193.00, 'unit' => 'Pcs'],
            ['slug' => 'special-lighting-connection-up-to-10kw',  'label' => 'Special Lighting Connection Up to 10kW',   'price' => 166.00, 'unit' => 'Pcs'],
            ['slug' => 'special-lighting-connection-up-to-20kw',  'label' => 'Special Lighting Connection Up to 20kW',   'price' => 276.00, 'unit' => 'Pcs'],
            ['slug' => 'switchboard',                             'label' => 'Switchboard',                               'price' => 50.00,  'unit' => 'Pcs'],
            ['slug' => 'lan-up-to-50mbs',                         'label' => 'LAN Up to 50Mb/s',                          'price' => 250.00, 'unit' => 'Pcs'],
            ['slug' => 'lcd-tv-55-65',                            'label' => 'LCD TV 55″ / 65″',                         'price' => 220.00, 'unit' => 'Pcs'],
            ['slug' => 'stand-safeguarding',                      'label' => 'Stand Safeguarding',                        'price' => 8.00,   'unit' => '1h'],
            ['slug' => 'technician-service',                      'label' => 'Technician Service',                        'price' => 30.00,  'unit' => '1h'],
            ['slug' => 'mutual-costs-compulsory-fee',             'label' => 'Mutual Costs Compulsory Fee',               'price' => 4.50,   'unit' => 'm²'],
        ];


        // Decide if size is in [9,12,24,49] => group A, else group B
        if (in_array($size_val, [9,12,24,49], true)) {
            return $addons_group_a;
        } else {
            return $addons_group_b;
        }
    }

}
