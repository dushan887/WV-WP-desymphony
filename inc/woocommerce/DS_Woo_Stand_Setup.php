<?php
namespace Desymphony\Woo;

if (!defined('ABSPATH')) exit;

class DS_Woo_Stand_Setup {

    public static function init() {
        add_action('init', [__CLASS__, 'setup_woocommerce_stands'], 20);
        add_action('add_meta_boxes', [__CLASS__, 'add_stand_meta_box']);
        add_action('save_post_product', [__CLASS__, 'save_stand_meta_box'], 10, 2);
    }

    public static function setup_woocommerce_stands() {
        // Only run if Woo is active
        if (!class_exists('WooCommerce')) {
            return;
        }
        self::register_stand_category();
        self::register_stand_attributes();
    }

    private static function register_stand_category() {
        if (!term_exists('Stand', 'product_cat')) {
            wp_insert_term('Stand', 'product_cat', [
                'description' => 'Exhibition Stand',
                'slug'        => 'stand',
            ]);
        }
    }

    private static function register_stand_attributes() {
        global $wpdb;

        $attributes = [
            'hall'       => 'Hall',
            'stand_size' => 'Stand Size',
            'stand_type' => 'Stand Type',
        ];

        foreach ($attributes as $slug => $label) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT attribute_id FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s",
                $slug
            ));
            if (!$exists) {
                $wpdb->insert(
                    "{$wpdb->prefix}woocommerce_attribute_taxonomies",
                    [
                        'attribute_label'   => $label,
                        'attribute_name'    => $slug,
                        'attribute_type'    => 'select',
                        'attribute_orderby' => 'menu_order',
                        'attribute_public'  => 1,
                    ]
                );
                delete_transient('wc_attribute_taxonomies');
            }
        }
    }

    /**
     * Add Stand meta box for additional fields
     */
    public static function add_stand_meta_box() {
        add_meta_box(
            'wv_stand_meta',
            'Stand Details',
            [__CLASS__, 'render_stand_meta_box'],
            'product',
            'normal',
            'default'
        );
    }

    /**
     * Render the Stand meta box
     */
    public static function render_stand_meta_box($post) {
        // Only show for products in Stand category
        $terms = get_the_terms($post->ID, 'product_cat');
        $in_stand = false;
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                if ($term->slug === 'stand') {
                    $in_stand = true;
                    break;
                }
            }
        }
        if (!$in_stand) return;

        $fields = [
            'wv_stand_no'        => 'Stand No.',
            'wv_hall_stand_no'   => 'Hall / Stand No.',
            'wv_reservation_name'=> 'Reservation Name',
            'wv_reservation_email'=> 'Reservation Email',
            'wv_reservation_user'=> 'Reservation User ID',
            'wv_stand_status'    => 'Stand Status (available, reserved, sold)',
            'wv_assigned_users' => 'Assigned User IDs (comma‑separated)',
        ];

        wp_nonce_field('wv_stand_meta_nonce', 'wv_stand_meta_nonce_field');

        echo '<div class="form-table">';
        foreach ($fields as $meta_key => $label) {
            $value = get_post_meta($post->ID, $meta_key, true);
            echo '<p><label for="' . esc_attr($meta_key) . '"><strong>' . esc_html($label) . ':</strong></label><br>';
            echo '<input type="text" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($value) . '" class="widefat"></p>';
        }
        echo '</div>';
    }

    /**
     * Save meta box data
     */
    public static function save_stand_meta_box($post_id, $post) {
        if (
            !isset($_POST['wv_stand_meta_nonce_field']) ||
            !wp_verify_nonce($_POST['wv_stand_meta_nonce_field'], 'wv_stand_meta_nonce')
        ) {
            return;
        }

        // Only run on products in Stand category
        $terms = get_the_terms($post_id, 'product_cat');
        $in_stand = false;
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                if ($term->slug === 'stand') {
                    $in_stand = true;
                    break;
                }
            }
        }
        if (!$in_stand) return;

        $fields = [
            'wv_stand_no',
            'wv_hall_stand_no',
            'wv_reservation_name',
            'wv_reservation_email',
            'wv_reservation_user',
            'wv_stand_status',
            'wv_assigned_users',
        ];

        foreach ( $fields as $meta_key ) {
            if ( ! isset( $_POST[ $meta_key ] ) ) {
                continue;
            }

            // special handling for the new comma‑separated list
            if ( $meta_key === 'wv_assigned_users' ) {
                $raw = sanitize_text_field( $_POST[ $meta_key ] );
                $ids = array_filter( array_map( 'intval', explode( ',', $raw ) ) );
                update_post_meta( $post_id, $meta_key, $ids );
                continue;
            }

            // default: plain text field
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $meta_key ] ) );
        }
    }
}
