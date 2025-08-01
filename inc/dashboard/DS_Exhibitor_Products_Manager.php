<?php
namespace Desymphony\Dashboard;

use Desymphony\Helpers\DS_Media_Handler;
use Desymphony\Database\DS_Products_Repository; 

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Manages Exhibitor Products (Wine, Rakija, Food, Other).
 * 
 * Now uses DS_Products_Repository for DB queries.
 */
class DS_Exhibitor_Products_Manager {

    public static function init() {
        // AJAX endpoints for Dashboard
        add_action( 'wp_ajax_wv_get_products',            [ __CLASS__, 'get_products' ] );
        add_action( 'wp_ajax_wv_save_product',            [ __CLASS__, 'save_product' ] );
        add_action( 'wp_ajax_wv_delete_product',          [ __CLASS__, 'delete_product' ] );
        add_action( 'wp_ajax_wv_create_empty_product',    [ __CLASS__, 'wv_create_empty_product_callback' ] );
    }

    /**
     * AJAX: Fetch all products belonging to the current logged-in user (exhibitor).
     */
    public static function get_products() {
        check_ajax_referer( 'wv_dashboard_nonce', 'security' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( [ 'message' => __( 'Not authenticated', 'wv-addon' ) ] );
        }

        $user_id = get_current_user_id();

        // Instead of $wpdb->, we do:
        $repo = new DS_Products_Repository();
        $rows = $repo->get_products_by_exhibitor( $user_id );

        if ( $rows === null ) {
            // Possibly check $repo->get_last_error() if needed
            wp_send_json_error( [
                'message' => __( 'DB error', 'wv-addon' ) . ': ' . $repo->get_last_error()
            ] );
        }

        // Convert objects to arrays if you prefer returning an array
        // or just do: `ARRAY_A` in repository methods if you like
        // But let's keep objects for now
        wp_send_json_success( [ 'products' => $rows ] );
    }

    /**
     * AJAX: Insert or update a product (Wine, Rakija, Food, Other).
     */
    public static function save_product() {
        // 1) Verify nonce & authentication
        check_ajax_referer( 'wv_dashboard_nonce', 'security' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error(
                [ 'message' => __( 'Not authenticated', 'wv-addon' ) ],
                401
            );
        }
    
        // 2) Grab & unslash all POST data
        $data       = wp_unslash( $_POST );
        $user_id    = get_current_user_id();
        $product_id = isset( $data['id'] ) ? absint( $data['id'] ) : 0;

        if ( defined('WP_DEBUG') && WP_DEBUG ) {
            error_log( 'WV Save POST: ' . print_r( $data, true ) );
        }
    
        // 3) Validate required fields
        $raw_title = trim( $data['title'] ?? '' );
        if ( '' === $raw_title ) {
            wp_send_json_error(
                [ 'message' => __( 'Title cannot be empty', 'wv-addon' ) ],
                400
            );
        }
    
        // 4) Map table columns to form field names
                /* --- 4) Map front‑end field names to DB columns --- */
        $map = [
            // identity
            'title'                 => 'title',
            'type'                  => 'type',
            'category'              => 'category',
            'variety'               => 'variety',
            'fruit_base'            => 'fruit_base',
            // origin
            'region'                => 'region',
            'country'               => 'country',
            'vintage_year'          => 'vintage_year',
            // composition
            'volume_ml'             => 'volume_ml',
            'alcohol_pct'           => 'alcohol_pct',
            'sugar_pct'             => 'sugar_pct',
            'acidity_pct'           => 'acidity_pct',
            'annual_production_l'   => 'annual_production_l',
            'current_stock_l'       => 'current_stock_l',
            // process
            'aging_process'         => 'aging_process',
            'distillation_method'   => 'distillation_method',
            'special_certification' => 'special_certification',
            'submit_for_trophy'     => 'submit_for_trophy',
            // misc
            'description'           => 'description',
        ];

    
        // 5) Build sanitized $fields for DB
        $fields = [
            'exhibitor_id' => $user_id,
            'date_updated' => current_time( 'mysql' ),
        ];
    
        foreach ( $map as $column => $field_name ) {
            $raw = $data[ $field_name ] ?? '';
    
            switch ( $column ) {
                case 'title':
                    // Title: already validated, now sanitize
                    $fields[ $column ] = sanitize_text_field( $raw );
                    break;
    
                case 'type':
                case 'category':
                case 'fruit_base':
                case 'region':
                case 'bottle_neck':
                case 'annual_production_l':
                case 'aging_process':
                case 'distillation_method':
                case 'special_certification':
                    // simple text inputs / selects
                    $fields[ $column ] = sanitize_text_field( $raw );
                    break;
    
                case 'variety':
                    // multi-select → comma-delimited string
                    if ( is_array( $raw ) ) {
                        $fields[ $column ] = implode( ',', array_map( 'sanitize_text_field', $raw ) );
                    } else {
                        $fields[ $column ] = sanitize_text_field( $raw );
                    }
                    break;
    
                case 'description':
                    // allow basic HTML
                    $fields[ $column ] = wp_kses_post( $raw );
                    break;
    
                case 'year':
                    // YEAR column
                    $fields[ $column ] = $raw !== '' ? absint( $raw ) : null;
                    break;
    
                case 'volume_ml':
                case 'current_stock_l':
                case 'net_weight_g':
                    // integer columns
                    $fields[ $column ] = $raw !== '' ? intval( $raw ) : null;
                    break;
    
                case 'alcohol_pct':
                case 'sugar_pct':
                    // decimal columns
                    $fields[ $column ] = $raw !== '' ? floatval( $raw ) : null;
                    break;

                case 'submit_for_trophy':
                    $fields[ $column ] = ( isset( $raw ) && (int) $raw === 1 ) ? 1 : 0;
                    break;

    
                default:
                    // fallback sanitization
                    $fields[ $column ] = sanitize_text_field( $raw );
                    break;
            }
        }
    
        // 6) Instantiate repository
        $repo = new DS_Products_Repository();
    
        // 7) UPDATE existing product
        if ( $product_id > 0 ) {
            // 7a) enforce ownership or allow admins
            if ( ! current_user_can( 'manage_options' )
                 && ! $repo->user_owns_product( $product_id, $user_id ) ) {
                wp_send_json_error(
                    [ 'message' => __( 'Product not found or not yours.', 'wv-addon' ) ],
                    403
                );
            }
    
            // 7b) perform the update
            $ok = $repo->update_product( $product_id, $user_id, $fields );
            if ( ! $ok ) {
                wp_send_json_error(
                    [ 'message' => __( 'DB update error', 'wv-addon' ) ],
                    500
                );
            }
    
            // 7c) handle image (crop upload, fallback, preserve)
            $image_url = '';
            if ( ! empty( $data['image_data'] ) ) {
                $media = new DS_Media_Handler();
                $res   = $media->process_crop_upload( [
                    'user_id'    => $user_id,
                    'type'       => 'product',
                    'product_id' => $product_id,
                    'image_data' => $data['image_data'],
                ] );
                if ( ! is_wp_error( $res ) && ! empty( $res['final'] ) ) {
                    $image_url = esc_url_raw( $res['final'] );
                }
            }
            $image_url_field = $data['product-image'] ?? $data['wv-product-image'] ?? '';
            if ( empty( $image_url ) && $image_url_field ) {
                $image_url = esc_url_raw( $image_url_field );
            }
            if ( empty( $image_url ) ) {
                $old = $repo->get_product_for_user( $product_id, $user_id );
                if ( $old && ! empty( $old->image_url ) ) {
                    $image_url = $old->image_url;
                }
            }
            if ( $image_url ) {
                $repo->update_product( $product_id, $user_id, [ 'image_url' => $image_url ] );
            }
    
            // 7d) return success
            $updated_row = $repo->get_product_for_user( $product_id, $user_id );
            wp_send_json_success( [
                'message' => __( 'Product updated.', 'wv-addon' ),
                'product' => $updated_row,
            ] );
        }
    
        // 8) INSERT a brand-new product
        $fields['date_created'] = current_time( 'mysql' );
        $fields['image_url']    = '';
        $new_id = $repo->insert_product( $fields );
        if ( ! $new_id ) {
            wp_send_json_error( [ 'message' => __( 'DB insert error', 'wv-addon' ) ], 500 );
        }
    
        // 8a) image handling for new product
        $image_url = '';
        if ( ! empty( $data['image_data'] ) ) {
            $media = new DS_Media_Handler();
            $res   = $media->process_crop_upload( [
                'user_id'    => $user_id,
                'type'       => 'product',
                'product_id' => $new_id,
                'image_data' => $data['image_data'],
            ] );
            if ( ! is_wp_error( $res ) && ! empty( $res['final'] ) ) {
                $image_url = esc_url_raw( $res['final'] );
            }
        }
        $image_url_field = $data['product-image'] ?? $data['wv-product-image'] ?? '';
        if ( empty( $image_url ) && $image_url_field ) {
            $image_url = esc_url_raw( $image_url_field );
        }
        if ( $image_url ) {
            $repo->update_product( $new_id, $user_id, [ 'image_url' => $image_url ] );
        }
    
        // 8b) final success
        $new_row = $repo->get_product_for_user( $new_id, $user_id );
        wp_send_json_success( [
            'message' => __( 'Product added.',   'wv-addon' ),
            'product' => $new_row,
        ] );
    }
    

    /**
     * AJAX: Delete a product (must belong to current user).
     */
    public static function delete_product() {
        check_ajax_referer( 'wv_dashboard_nonce', 'security' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( [ 'message' => __( 'Not authenticated', 'wv-addon' ) ] );
        }

        $id = intval( $_POST['id'] ?? 0 );
        if ( ! $id ) {
            wp_send_json_error( [ 'message' => __( 'Invalid product ID', 'wv-addon' ) ] );
        }

        $user_id = get_current_user_id();

        // Use the repository
        $repo = new DS_Products_Repository();
        // Check ownership
        if ( ! $repo->user_owns_product( $id, $user_id ) ) {
            wp_send_json_error( [ 'message' => __( 'Product not found', 'wv-addon' ) ] );
        }

        // Delete
        $deleted = $repo->delete_product( $id, $user_id );
        if ( ! $deleted ) {
            wp_send_json_error( [ 'message' => __( 'DB delete error', 'wv-addon' ) ] );
        }

        wp_send_json_success( [ 'message' => __( 'Product deleted.', 'wv-addon' ) ] );
    }

    /**
     * AJAX: Create an empty product row (for "Add New" button).
     * Optionally set the 'type' if provided.
     */
    public static function wv_create_empty_product_callback() {
        check_ajax_referer( 'wv_dashboard_nonce', 'security' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error([ 'message' => __( 'Not authenticated', 'wv-addon' ) ]);
        }

        $user_id = get_current_user_id();

        /* ------------- MAX‑20 check ---------------- */
        $repo = new DS_Products_Repository();
        $current_total = count( $repo->get_products_by_exhibitor( $user_id ) );
        if ( $current_total >= 20 ) {
            wp_send_json_error( [
            'message' => __( 'You have reached the 20‑product limit.', 'wv-addon' )
            ] );
        }

        $ptype = isset($_POST['product_type'])
            ? sanitize_text_field($_POST['product_type'])
            : 'wine';

        // Minimal insert
        $fields = [
            'exhibitor_id' => $user_id,
            'title'        => '',
            'image_url'    => '',
            'type'         => $ptype,
            'date_created' => current_time( 'mysql' ),
            'date_updated' => current_time( 'mysql' ),
        ];
        // $repo = new DS_Products_Repository();
        $new_id = $repo->insert_product( $fields );
        if ( ! $new_id ) {
            wp_send_json_error([ 'message' => __( 'DB insert error.', 'wv-addon' ) ]);
        }

        wp_send_json_success([
            'message'    => __( 'Empty product created.', 'wv-addon' ),
            'product_id' => $new_id,
        ]);
    }
}


