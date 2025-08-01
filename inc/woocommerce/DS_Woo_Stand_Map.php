<?php
namespace Desymphony\Woo;

if (!defined('ABSPATH')) exit;

class DS_Woo_Stand_Map
{
    /**
     * Get stands map for **one** hall. No more loading all halls at once.
     */
    public static function get_map_for_hall($hallSlug) {
        // 1) Build a meta_query to find products where wv_hall_stand_no starts with e.g. "4A/"
        //    We use 'REGEXP' for '^4A/' so it matches "4A/anything"
        $args = [
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => ['stand'],
                ],
            ],
            'meta_query' => [
                [
                    'key'     => 'wv_hall_stand_no',
                    'value'   => '^' . $hallSlug . '/',
                    'compare' => 'REGEXP',
                ]
            ],
        ];

        $products = get_posts($args);
        // If no products found for that hall, just return an empty array
        if (!$products) {
            return [$hallSlug => []];
        }

        // 2) Build the map array **only** for that hall
        $map = [];
        foreach ($products as $product_id) {
            $full_stand  = get_post_meta($product_id, 'wv_hall_stand_no', true); // e.g. "4A/55"
            $hall    = '';
            $stand_no = '';
            if (strpos($full_stand, '/') !== false) {
                list($hall, $stand_no) = explode('/', $full_stand, 2);
            }
            if (!$hall || !$stand_no) {
                continue;
            }

            // read standard meta
            $status      = get_post_meta($product_id, 'wv_stand_status', true);
            $label       = get_post_meta($product_id, 'wv_reservation_name', true);
            $res_email   = get_post_meta($product_id, 'wv_reservation_email', true);
            if (!$label && $res_email) {
                $label = $res_email;
            }

            // ID used in your SVG
            $id = "wv_hall_{$hall}_" . str_pad($stand_no, 2, '0', STR_PAD_LEFT);

            // get size (like in your original code)
            $stand_size = '';
            $product = wc_get_product($product_id);
            // Try global attribute first
            $terms = wp_get_post_terms($product_id, 'pa_stand_size');
            if (!empty($terms) && !is_wp_error($terms)) {
                $stand_size = $terms[0]->name;
            }
            // fallback: custom attribute
            if (empty($stand_size) && $product) {
                $attributes = $product->get_attributes();
                foreach ($attributes as $key => $attribute) {
                    if (
                        $key === 'pa_stand-size' ||
                        $key === 'stand_size' ||
                        $key === 'pa_stand_size'
                    ) {
                        $data = $attribute->get_data();
                        if (!empty($data['value'])) {
                            $stand_size = $data['value'];
                        } elseif (method_exists($attribute, 'get_options')) {
                            $opts = $attribute->get_options();
                            if (is_array($opts)) $stand_size = implode(', ', $opts);
                            else $stand_size = $opts;
                        }
                    }
                }
            }

            /* NEW – who is already assigned to this stand */
            $assigned_users = get_post_meta( $product_id, 'wv_assigned_users', true );
            $assigned_users = is_array( $assigned_users )
                ? array_map( 'intval', $assigned_users )
                : [];
            
            /* NEW – ID of the direct purchaser (old logic in ajax loader expects it) */
            $reservation_user = (int) get_post_meta( $product_id, 'wv_reservation_user', true );

            // If sold, find purchaser
            $purchased_by = '';
            $purchased_email = '';
            $purchased_user_id = '';
            if (strtolower($status) === 'sold') {
                $order_data = self::get_last_order_for_product($product_id);
                if ($order_data) {
                    $purchased_by = $order_data['name'];
                    $purchased_email = $order_data['email'];
                    $purchased_user_id = $order_data['user_id'];
                }
            }

            // Put it in the map
            $map[$hall][] = [
                'id'                => $id,
                'stand'             => "{$hall}/{$stand_no}",
                'label'             => $label,
                'status'            => $status,
                'product_id'        => $product_id,
                'stand_size'        => $stand_size,
                'purchased_by'      => $purchased_by,
                'purchased_email'   => $purchased_email,
                'purchased_user_id' => $purchased_user_id,
                'assigned_users'      => $assigned_users,
                'wv_reservation_user' => $reservation_user,
            ];
        }

        // 3) Sort stands inside the hall
        if (!empty($map[$hallSlug])) {
            usort($map[$hallSlug], function($a, $b) {
                return strnatcasecmp($a['stand'], $b['stand']);
            });
        } else {
            // if no stands found for that slug, ensure we still return an empty array
            $map[$hallSlug] = $map[$hallSlug] ?? [];
        }

        return $map;
    }

    /**
     * (Optional) The original get_map() for all halls, if you still need it.
     */
    public static function get_map() {
        // ... your existing code ...
    }

    /**
     * Helper function
     */
    public static function get_last_order_for_product($product_id) {
        // same as your existing code
        $orders = wc_get_orders([
            'limit'        => 1,
            'orderby'      => 'date_completed',
            'order'        => 'DESC',
            'status'       => ['completed', 'processing'],
            'type'         => 'shop_order',
            'return'       => 'objects',
            'product_id'   => $product_id,
        ]);

        if (!empty($orders)) {
            $order = $orders[0];
            $user_id = $order->get_customer_id();
            $name = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());
            $email = $order->get_billing_email();

            return [
                'user_id' => $user_id,
                'name'    => $name,
                'email'   => $email,
            ];
        }
        return null;
    }
}
