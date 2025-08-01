<?php

/**
 * Desymphony – Admin Users screen + AJAX.
 * File: inc/auth/classes/DS_Admin_User_Table.php
 *
 * Full replacement – 2025‑07‑09
 */

namespace Desymphony\Auth\Classes;

use WP_User_Query;

use Desymphony\Helpers\DS_Utils;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class DS_Admin_User_Table {

    private const CAP = 'manage_wv_addon';

    /** verify‑modal keys (unchanged) */
    private const EDITABLE_META = [
        'wv_admin_verified'=>'checkbox','wv_status'=>'select',
        'wv_ex_stage1_verified'=>'checkbox','wv_ex_stage2_verified'=>'checkbox',
        'has_reserved_stand'=>'checkbox','wv_wvhb_support'=>'select',
    ];

    private const EDITABLE_DEFAULTS = [
        'wv_admin_verified'=>'0','wv_status'=>'Pending',
        'wv_ex_stage1_verified'=>'0','wv_ex_stage2_verified'=>'0',
        'has_reserved_stand'=>'0','wv_wvhb_support'=>'NONE',
    ];

    /** 👁️ view‑only + server‑protected */
    private const READ_ONLY_META = [
        'user_email','role','wv_admin_scope',
        'wv_profile_selection','wv_email',
        'wv_user-logo','wv_user-avatar',
        'wc_last_active','wv_linked_exhib','zqvz_capabilities',
        'zqvz_user_level','last_update',
    ];

    /** ❌ excluded completely from UI & saving */
    private const EXCLUDED_META = [
        'nickname','first_name','last_name','description','rich_editing',
        'syntax_highlighting','comment_shortcuts','admin_color','use_ssl',
        'show_admin_bar_front','locale','dismissed_wp_pointers',
        'session_tokens','terms_conditions',
        // verify‑only flags (edited elsewhere)
        'wv_admin_verified','wv_status','wv_ex_stage1_verified',
        'wv_ex_stage2_verified','has_reserved_stand','wv_wvhb_support',
    ];

    /* ───────── bootstrap ───────── */

    public function __construct() {
        add_shortcode( 'wv_admin_user_table', [ $this, 'render_shortcode' ] );
        // ajax for user details
        add_action( 'wp_ajax_wv_admin_get_user',    [ $this, 'ajax_get_user' ] );
        add_action( 'wp_ajax_wv_admin_save_user',   [ $this, 'ajax_save_user' ] );
        add_action( 'wp_ajax_wv_admin_delete_user', [ $this, 'ajax_delete_user' ] );
        add_action( 'wp_ajax_wv_admin_set_scope',   [ $this, 'ajax_set_scope' ] );
        add_action( 'wp_ajax_wv_admin_get_stands',  [ $this, 'ajax_get_stands' ] );
        // ajax for notifications
        add_action( 'wp_ajax_wv_admin_send_notice', [ $this, 'ajax_send_notice' ] );
        // NEW: ajax for tab content
        add_action( 'wp_ajax_wv_admin_get_exhibitors_table', [ $this, 'ajax_get_exhibitors_table' ] );
        add_action( 'wp_ajax_wv_admin_get_buyers_provisitors_table', [ $this, 'ajax_get_buyers_provisitors_table' ] );
        add_action( 'wp_ajax_wv_admin_get_visitors_table', [ $this, 'ajax_get_visitors_table' ] );
        add_action( 'wp_ajax_wv_admin_get_stands_table', [ $this, 'ajax_get_stands_table' ] );
        // NEW: ajax for exports (handle as direct CSV output)
        add_action( 'wp_ajax_wv_admin_export_exhibitors_csv', [ $this, 'ajax_export_exhibitors_csv' ] );
        add_action( 'wp_ajax_wv_admin_export_buyers_provisitors_csv', [ $this, 'ajax_export_buyers_provisitors_csv' ] );
        add_action( 'wp_ajax_wv_admin_export_visitors_csv', [ $this, 'ajax_export_visitors_csv' ] );
        add_action( 'wp_ajax_wv_admin_export_stands_csv', [ $this, 'ajax_export_stands_csv' ] );
    }

    /* ───────── permission helpers ───────── */

    private function user_can_edit(): bool {
        return current_user_can( self::CAP ) || current_user_can( 'administrator' ) || current_user_can( 'wv_admin' );
    }

    private function user_can_access(): bool {
        return $this->user_can_edit() || current_user_can( 'wv_admin' );
    }

    /* ───────── helpers ───────── */

    private function is_super_editor(): bool {
        // real WP admin OR wv_admin that is allowed to manage every role
        return current_user_can( 'administrator' ) ||
            ( current_user_can( self::CAP ) && DS_Utils::current_admin_scope() === 'all' );
    }

    /* ───────── labels map ───────── */

    private static function labels_map(): array {
        $defs = array_merge(
            DS_Meta_Fields::get_global_fields(),
            DS_Meta_Fields::get_exhibitor_fields(),
            DS_Meta_Fields::get_buyer_visitor_fields()
        );
        $map = [];
        foreach ( $defs as $d ) { $map[ $d['field_slug'] ] = $d['field_question']; }
        $map += [
            'wv_admin_verified'     => __( 'Admin verified', DS_THEME_TEXTDOMAIN ),
            'wv_ex_stage1_verified' => __( 'Stage 1 verified', DS_THEME_TEXTDOMAIN ),
            'wv_ex_stage2_verified' => __( 'Stage 2 verified', DS_THEME_TEXTDOMAIN ),
            'has_reserved_stand'    => __( 'Has reserved stand', DS_THEME_TEXTDOMAIN ),
            'wv_wvhb_support'       => __( 'Hosted-Buyer support', DS_THEME_TEXTDOMAIN ),
        ];
        return $map;
    }

    /* ───────── shortcode output: tabs ───────── */

    public function render_shortcode(): string {
        if ( ! $this->user_can_access() ) { return ''; }
        $this->enqueue_assets();
        ob_start(); ?>
        <!-- global spinner -->
        <span id="globalSpinner"
              class="spinner-border text-primary position-fixed top-50 start-50 translate-middle d-none"
              role="status" aria-hidden="true"></span>
        <div class="container py-24">
            <h2 class="mb-24"><?php _e( 'Admin User Table', DS_THEME_TEXTDOMAIN ); ?></h2>
            <ul class="nav nav-tabs" id="wv-admin-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="exhibitors-tab" data-bs-toggle="tab" data-bs-target="#tab-exhibitors" type="button" role="tab">Exhibitors</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="buyers-provisitors-tab" data-bs-toggle="tab" data-bs-target="#tab-buyers-provisitors" type="button" role="tab">Buyers & ProVisitors</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="visitors-tab" data-bs-toggle="tab" data-bs-target="#tab-visitors" type="button" role="tab">Visitors</button>
                </li>
                <?php if ( current_user_can( 'administrator' ) ) : ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="stands-tab" data-bs-toggle="tab" data-bs-target="#tab-stands" type="button" role="tab">Stands</button>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="tab-content mt-24">
                <div class="tab-pane fade show active" id="tab-exhibitors" role="tabpanel"></div>
                <div class="tab-pane fade" id="tab-buyers-provisitors" role="tabpanel"></div>
                <div class="tab-pane fade" id="tab-visitors" role="tabpanel"></div>
                <?php if ( current_user_can( 'administrator' ) ) : ?>
                    <div class="tab-pane fade" id="tab-stands" role="tabpanel"></div>
                <?php endif; ?>
            </div>
        </div>
        <?php $this->render_modals(); ?>
        <?php
        wp_localize_script(
            'desymphony-admin-users',
            'wvAdminUsers',
            [
                'ajax'     => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'wv_admin_users' ),
                'editable' => self::EDITABLE_META,
            ]
        );
        return ob_get_clean();
    }

    /* ───────── NEW: AJAX for tab content ───────── */

    public function ajax_get_exhibitors_table() {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        wp_send_json_success( $this->render_exhibitors_table() );
    }

    public function ajax_get_buyers_provisitors_table() {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        wp_send_json_success( $this->render_buyers_provisitors_table() );
    }

    public function ajax_get_visitors_table() {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        wp_send_json_success( $this->render_visitors_table() );
    }

    public function ajax_get_stands_table() {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        if ( ! current_user_can( 'administrator' ) ) { wp_send_json_error( 'Access denied' ); }
        wp_send_json_success( $this->render_stands_table() );
    }

    /* ───────── NEW: Render table HTML per tab ───────── */

    private function render_exhibitors_table(): string {
        $users = $this->get_filtered_users( ['exhibitor'] );
        ob_start();
        $this->render_users_table( $users, 'exhibitors', ['ID', 'Full name', 'Company', 'Profile', 'Participation', 'Category', 'Stands', 'Flags', 'Status', 'Actions'] );
        return ob_get_clean();
    }

    private function render_buyers_provisitors_table(): string {
        $users = $this->get_filtered_users( ['buyer'], ['visitor' => 'Company'] );
        ob_start();
        $this->render_users_table( $users, 'buyers_provisitors', ['ID', 'Full name', 'Company', 'Profile', 'Participation', 'Category', 'Flags', 'Status', 'Actions'] );
        return ob_get_clean();
    }

    private function render_visitors_table(): string {
        $users = $this->get_filtered_users( [], ['visitor' => 'Public Visitor'] );
        ob_start();
        $this->render_users_table( $users, 'visitors', ['ID', 'Full name', 'Participation', 'Category', 'Flags', 'Status', 'Actions'] );
        return ob_get_clean();
    }

    private function render_stands_table(): string {
        ob_start(); ?>
        <div id="wv-admin-products-filters" class="d-flex flex-wrap gap-2 mb-3">
            <select id="filter-hall" class="form-select form-select-sm" style="width:auto">
                <option value=""><?php esc_html_e( 'All Halls', DS_THEME_TEXTDOMAIN ); ?></option>
                <?php
                $halls = $this->get_unique_halls();
                foreach ( $halls as $hall ) {
                    echo '<option value="' . esc_attr( $hall ) . '">' . esc_html( $hall ) . '</option>';
                }
                ?>
            </select>
            <select id="filter-status" class="form-select form-select-sm" style="width:auto">
                <option value=""><?php esc_html_e( 'All Status', DS_THEME_TEXTDOMAIN ); ?></option>
                <option value="available"><?php esc_html_e( 'Available', DS_THEME_TEXTDOMAIN ); ?></option>
                <option value="reserved"><?php esc_html_e( 'Reserved', DS_THEME_TEXTDOMAIN ); ?></option>
                <option value="sold"><?php esc_html_e( 'Sold', DS_THEME_TEXTDOMAIN ); ?></option>
            </select>
            <select id="filter-stock" class="form-select form-select-sm" style="width:auto">
                <option value=""><?php esc_html_e( 'All Stock', DS_THEME_TEXTDOMAIN ); ?></option>
                <option value="0"><?php esc_html_e( 'Out of Stock', DS_THEME_TEXTDOMAIN ); ?></option>
                <option value="1"><?php esc_html_e( 'In Stock', DS_THEME_TEXTDOMAIN ); ?></option>
            </select>
        </div>
        <table id="wv-admin-products-table" class="table table-striped w-100 fs-14">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?php _e( 'Product Name', DS_THEME_TEXTDOMAIN ); ?></th>
                    <th><?php _e( 'Hall', DS_THEME_TEXTDOMAIN ); ?></th>
                    <th><?php _e( 'Stand No.', DS_THEME_TEXTDOMAIN ); ?></th>
                    <th><?php _e( 'SKU', DS_THEME_TEXTDOMAIN ); ?></th>
                    <th><?php _e( 'Price', DS_THEME_TEXTDOMAIN ); ?></th>
                    <th><?php _e( 'Stock', DS_THEME_TEXTDOMAIN ); ?></th>
                    <th><?php _e( 'Reservation Name', DS_THEME_TEXTDOMAIN ); ?></th>
                    <th><?php _e( 'Reservation Email', DS_THEME_TEXTDOMAIN ); ?></th>
                    <th><?php _e( 'Reservation User ID', DS_THEME_TEXTDOMAIN ); ?></th>
                    <th><?php _e( 'Stand Status', DS_THEME_TEXTDOMAIN ); ?></th>
                    <th><?php _e( 'Assigned User IDs', DS_THEME_TEXTDOMAIN ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $products = $this->get_products();
                foreach ( $products as $product_post ) :
                    $product = wc_get_product( $product_post->ID );
                    if ( ! $product ) continue;
                    $meta = get_post_meta( $product_post->ID );
                    $fields = [
                        'wv_reservation_name' => '',
                        'wv_reservation_email'=> '',
                        'wv_reservation_user' => '',
                        'wv_stand_status'     => '',
                        'wv_assigned_users'   => '',
                    ];
                    foreach ( $fields as $key => $_ ) {
                        $fields[ $key ] = isset( $meta[ $key ][0] ) ? esc_html( $meta[ $key ][0] ) : '';
                    }
                    $sku = $product->get_sku();
                    $hall = '';
                    $stand_no = '';
                    if ( preg_match( '/^([A-Za-z0-9]+)-(\d+)$/', $sku, $m ) ) {
                        $hall = $m[1];
                        $stand_no = $m[2];
                    }
                    // Stand status badge
                    $status = strtolower( $fields['wv_stand_status'] );
                    $badge = '';
                    if ( $status === 'sold' ) {
                        $badge = '<span class="badge bg-danger">Sold</span>';
                    } elseif ( $status === 'reserved' ) {
                        $badge = '<span class="badge bg-warning text-dark">Reserved</span>';
                    } else {
                        $badge = '<span class="badge bg-success">Available</span>';
                    }
                    ?>
                    <tr>
                        <td><?php echo esc_html( $product->get_id() ); ?></td>
                        <td><?php echo esc_html( $product->get_name() ); ?></td>
                        <td><?php echo esc_html( $hall ); ?></td>
                        <td><?php echo esc_html( $stand_no ); ?></td>
                        <td><?php echo esc_html( $sku ); ?></td>
                        <td><?php echo wc_price( $product->get_price() ); ?></td>
                        <td><?php echo esc_html( $product->get_stock_quantity() ); ?></td>
                        <td><?php echo $fields['wv_reservation_name']; ?></td>
                        <td><?php echo $fields['wv_reservation_email']; ?></td>
                        <td><?php echo $fields['wv_reservation_user']; ?></td>
                        <td><?php echo $badge; ?></td>
                        <td><?php echo $fields['wv_assigned_users']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn btn-outline-primary btn-sm my-24 wv-export" data-tab="stands">
            &#128190; <?php esc_html_e( 'Download CSV', DS_THEME_TEXTDOMAIN ); ?>
        </button>
        <?php
        return ob_get_clean();
    }

    private function render_users_table( array $users, string $tab_id, array $columns ): void { ?>
        <div id="wv-admin-filters-<?php echo esc_attr( $tab_id ); ?>" class="d-flex flex-wrap gap-2 mb-3">
            <?php
            $filterCols = [
                3 => 'All Profiles',
                4 => 'All Participation',
                5 => 'All Categories',
                7 => 'All Flags',
                8 => 'All Status',
            ];
            foreach ( $filterCols as $ix => $lbl ) {
                printf(
                    '<select data-col="%d" class="form-select form-select-sm" style="width:auto"><option value="">%s</option></select>',
                    $ix,
                    esc_html__( $lbl, DS_THEME_TEXTDOMAIN )
                );
            }
            ?>
        </div>
        <table id="wv-admin-users-table-<?php echo esc_attr( $tab_id ); ?>" class="table table-striped w-100 fs-14">
            <thead>
                <tr>
                    <?php foreach ( $columns as $col ) : ?>
                        <th><?php echo esc_html( $col ); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $users as $u ) :
                    $m       = get_user_meta( $u->ID );
                    $company = $m['wv_company_name'][0]       ?? '';
                    $profile = $m['wv_profile_selection'][0]  ?? '';
                    $model   = $m['wv_participationModel'][0] ?? '';
                    $cat     = $m['wv_userCategory'][0]       ?? '';
                    $stands = implode( ' ', $this->stands_for_user( $u, $m ) );    // note the space separator
                    $status  = $m['wv_status'][0]             ?? 'Pending';
                    $row_data = [
                        $u->ID,
                        esc_html( $u->display_name ),
                    ];
                    if ( in_array( 'Company', $columns ) ) $row_data[] = esc_html( $company );
                    if ( in_array( 'Profile', $columns ) ) $row_data[] = esc_html( $profile );
                    if ( in_array( 'Participation', $columns ) ) $row_data[] = esc_html( $model );
                    if ( in_array( 'Category', $columns ) ) $row_data[] = esc_html( $cat );
                    if ( in_array( 'Stands', $columns ) ) $row_data[] = $stands ?: '–';
                    if ( in_array( 'Flags', $columns ) ) $row_data[] = implode( ' ', $this->flags_for_user( $m ) );
                    if ( in_array( 'Status', $columns ) ) $row_data[] = esc_html( $status );
                    ?>
                    <tr data-user="<?php echo esc_attr( $u->ID ); ?>"
                        data-email="<?php echo esc_attr( $u->user_email ); ?>"
                        data-company="<?php echo esc_attr( $company ); ?>">
                        <?php foreach ( $row_data as $cell ) : ?>
                            <td><?php echo $cell; ?></td>
                        <?php endforeach; ?>
                        <td class="text-center">
                            <?php
                            $showStandsBtn = (
                                reset( $u->roles ) === 'exhibitor' &&
                                ( $m['wv_ex_stage1_verified'][0] ?? '0' ) === '1' &&
                                $this->stands_for_user( $u, $m )            /* has at least one purchased stand */
                            );
                            ?>
                            <button type="button" class="btn btn-sm btn-primary wv-view" title="<?php _e( 'Edit', DS_THEME_TEXTDOMAIN ); ?>">
                                <span class="me-1" aria-hidden="true">&#128065;</span> <!-- eye -->
                            </button>
                            <button type="button" class="btn btn-sm btn-success wv-verify" title="<?php _e( 'Verify', DS_THEME_TEXTDOMAIN ); ?>">
                                <span class="me-1" aria-hidden="true">&#10003;</span> <!-- check mark -->
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary wv-notify" title="<?php _e( 'Notify', DS_THEME_TEXTDOMAIN ); ?>">
                                <span class="me-1" aria-hidden="true">&#9993;</span> <!-- envelope -->
                            </button>
                            <?php if ( $showStandsBtn ) : ?>
                                <button type="button" class="btn btn-sm btn-info wv-stands" title="<?php _e( 'Stand overview', DS_THEME_TEXTDOMAIN ); ?>">
                                    <span class="me-1" aria-hidden="true">&#128736;</span>
                                </button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-sm btn-danger wv-disable d-none" title="<?php _e( 'Delete', DS_THEME_TEXTDOMAIN ); ?>">
                                <span class="me-1" aria-hidden="true">&#128465;</span> <!-- trash can -->
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn btn-outline-primary btn-sm my-24 wv-export" data-tab="<?php echo esc_attr( $tab_id ); ?>">
            &#128190; <?php esc_html_e( 'Download CSV', DS_THEME_TEXTDOMAIN ); ?>
        </button>
        <?php
    }

    /* ───────── NEW: Fetch filtered users for tables/exports ───────── */

    private function get_filtered_users( array $roles = [], array $participation_filter = [] ): array {
        $query_args = [
            'role__in' => $roles,
            'fields'   => 'all',
            'number'   => -1,
        ];
        if ( ! empty( $participation_filter ) ) {
            $query_args['meta_query'] = [];
            foreach ( $participation_filter as $role => $model ) {
                $query_args['meta_query'][] = [
                    'relation' => 'AND',
                    ['key' => 'wv_participationModel', 'value' => $model, 'compare' => '='],
                ];
                $query_args['role__in'][] = $role; // Add role if in filter
            }
        }
        $results = ( new WP_User_Query( $query_args ) )->get_results();
        return array_filter( $results, fn( \WP_User $u ) => DS_Utils::can_manage_user( $u ) );
    }

    private function get_products(): array {
        return get_posts( [
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => 'any',
        ] );
    }

    private function get_unique_halls(): array {
        $halls = [];
        $products = $this->get_products();
        foreach ( $products as $product_post ) {
            $product = wc_get_product( $product_post->ID );
            if ( ! $product ) continue;
            $sku = $product->get_sku();
            if ( preg_match( '/^([A-Za-z0-9]+)-/', $sku, $m ) ) {
                $halls[ $m[1] ] = $m[1];
            }
        }
        return array_values( $halls );
    }

    /* ───────── NEW: Exports per tab ───────── */

    public function ajax_export_exhibitors_csv() {
        $this->export_users_csv( 'exhibitors', ['exhibitor'] );
    }

    public function ajax_export_buyers_provisitors_csv() {
        $this->export_users_csv( 'buyers_provisitors', ['buyer'], ['visitor' => 'Company'] );
    }

    public function ajax_export_visitors_csv() {
        $this->export_users_csv( 'visitors', [], ['visitor' => 'Public Visitor'] );
    }

    public function ajax_export_stands_csv() {
        $this->export_stands_csv();
    }

    private function export_users_csv( string $tab_id, array $roles = [], array $participation_filter = [] ) {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        if ( ! $this->user_can_access() ) { wp_die(); }
        // Apply filters from GET params (passed from JS)
        $filter_params = $_GET; // e.g., ?profile=some&participation=other
        $users = $this->get_filtered_users( $roles, $participation_filter );
        // Filter users based on params (simple meta/search simulation)
        if ( ! empty( $filter_params ) ) {
            $users = array_filter( $users, function( $u ) use ( $filter_params ) {
                $m = get_user_meta( $u->ID );
                foreach ( $filter_params as $key => $val ) {
                    if ( isset( $m[$key][0] ) && strpos( $m[$key][0], $val ) === false ) {
                        return false;
                    }
                }
                return true;
            } );
        }
        // Build header with nice labels
        $head = [ 'ID', 'Display Name', 'Email', 'Role', 'Stands' ];
        $rows = [];
        $labels = self::labels_map();
        foreach ( $users as $u ) {
            $meta = array_map( fn($v) => maybe_unserialize($v[0]), get_user_meta( $u->ID ) );
            $head = array_unique( array_merge( $head, array_keys( $meta ) ) );
            $rows[] = [
                'ID'           => $u->ID,
                'Display Name' => $u->display_name,
                'Email'        => $u->user_email,
                'Role'         => reset( $u->roles ),
                'Stands'       => wp_strip_all_tags( implode( ' ', $this->stands_for_user( $u, $meta ) ) ),
                'meta'         => $meta,
            ];
        }
        // Map header to nice labels
        $head = array_map( function( $k ) use ( $labels ) {
            return $labels[$k] ?? ucwords( str_replace( '_', ' ', $k ) );
        }, $head );
        $this->output_csv( $head, $rows, "winevision-{$tab_id}-" . date( 'Y-m-d_H-i-s' ) . '.csv' );
    }

    private function export_stands_csv() {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        if ( ! current_user_can( 'administrator' ) ) { wp_die(); }
        // Apply filters from GET params
        $filter_hall = sanitize_text_field( $_GET['hall'] ?? '' );
        $filter_status = sanitize_text_field( $_GET['status'] ?? '' );
        $filter_stock = sanitize_text_field( $_GET['stock'] ?? '' );
        $products = $this->get_products();
        // Filter products
        $filtered = [];
        foreach ( $products as $p ) {
            $product = wc_get_product( $p->ID );
            $meta = get_post_meta( $p->ID );
            $sku = $product->get_sku();
            $hall = preg_match( '/^([A-Za-z0-9]+)-/', $sku, $m ) ? $m[1] : '';
            $status = strtolower( $meta['wv_stand_status'][0] ?? '' );
            $stock = $product->get_stock_quantity();
            if ( ( $filter_hall && $hall !== $filter_hall ) ||
                 ( $filter_status && $status !== $filter_status ) ||
                 ( $filter_stock !== '' && (string) $stock !== $filter_stock ) ) {
                continue;
            }
            $filtered[] = $p;
        }
        // Build CSV (similar to existing, but with nice labels)
        $head = [ 'ID', 'Product Name', 'Hall', 'Stand No.', 'SKU', 'Price', 'Stock', 'Reservation Name', 'Reservation Email', 'Reservation User ID', 'Stand Status', 'Assigned User IDs' ];
        $rows = [];
        foreach ( $filtered as $product_post ) {
            $product = wc_get_product( $product_post->ID );
            $meta = get_post_meta( $product_post->ID );
            $sku = $product->get_sku();
            $hall = '';
            $stand_no = '';
            if ( preg_match( '/^([A-Za-z0-9]+)-(\d+)$/', $sku, $m ) ) {
                $hall = $m[1];
                $stand_no = $m[2];
            }
            $row = [
                $product->get_id(),
                $product->get_name(),
                $hall,
                $stand_no,
                $sku,
                $product->get_price(),
                $product->get_stock_quantity(),
                $meta['wv_reservation_name'][0] ?? '',
                $meta['wv_reservation_email'][0] ?? '',
                $meta['wv_reservation_user'][0] ?? '',
                $meta['wv_stand_status'][0] ?? '',
                $meta['wv_assigned_users'][0] ?? '',
            ];
            $rows[] = $row;
        }
        $this->output_csv( $head, $rows, 'winevision-stands-' . date( 'Y-m-d_H-i-s' ) . '.csv' );
    }

    private function output_csv( array $head, array $rows, string $fname ) {
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $fname . '"' );
        header( 'Cache-Control: no-cache, no-store, must-revalidate' );
        $out = fopen( 'php://output', 'w' );
        fwrite( $out, "\xEF\xBB\xBF" ); // UTF-8 BOM
        fputcsv( $out, $head );
        foreach ( $rows as $r ) {
            $line = [];
            foreach ( $head as $ix => $h ) {
                $line[] = is_array( $r ) ? ( $r[$ix] ?? '' ) : ( $r['meta'][$h] ?? $r[$h] ?? '' );
            }
            fputcsv( $out, $line );
        }
        fclose( $out );
        wp_die();
    }

    /* ───────── modals markup ───────── */

    private function render_modals(): void { ?>
        <!-- View / Edit / Verify modal -->
        <div class="modal fade" id="wvAdminUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e( 'Close', DS_THEME_TEXTDOMAIN ); ?></button>
                        <button type="button" class="btn btn-primary" id="wv-admin-user-save"><?php _e( 'Save', DS_THEME_TEXTDOMAIN ); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Notify / custom e‑mail modal -->
        <div class="modal fade" id="wvAdminNotifyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php _e( 'Send notification', DS_THEME_TEXTDOMAIN ); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea id="wv-notify-custom" class="form-control d-none" rows="8"
                                  placeholder="<?php esc_attr_e( 'Write custom HTML e‑mail…', DS_THEME_TEXTDOMAIN ); ?>"></textarea>
                        <p class="small text-muted mt-2">
                            <?php _e( 'Compose the HTML body of the e‑mail.', DS_THEME_TEXTDOMAIN ); ?>
                            <br>
                            <span><?php _e( 'This will be sent to', DS_THEME_TEXTDOMAIN ); ?></span>
                            <strong id="wv-notify-email" class="text-primary"></strong>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e( 'Close', DS_THEME_TEXTDOMAIN ); ?></button>
                        <button type="button" class="btn btn-primary" id="wv-send-notify"><?php _e( 'Send', DS_THEME_TEXTDOMAIN ); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stand overview modal -->
        <div class="modal fade" id="wvStandOverviewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" style="min-width:1240px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="modal-title" class="modal-title"><?php _e( 'Stand overview', DS_THEME_TEXTDOMAIN ); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div id="wv-stand-content" class="modal-body wv-bg-c_10"></div>
                    <div class="modal-footer">
                        <button id="ds-stand-download"
                                class="btn btn-success">
                            <?php esc_html_e( 'Download PDF', 'wv-addon' ); ?>
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e( 'Close', DS_THEME_TEXTDOMAIN ); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <script>
        (() => {
            document.getElementById('ds-stand-download').addEventListener('click', async () => {
                const { jsPDF } = window.jspdf;
                const stand = document.getElementById('wv-stand-content');
                const modalDialog = document.querySelector('#wvStandOverviewModal .modal-dialog');
                const modalBody = document.querySelector('#wvStandOverviewModal .modal-body');
                // Store original styles
                const originalStandStyles = {
                    width: stand.style.width,
                    height: stand.style.height,
                    overflow: stand.style.overflow,
                    backgroundColor: stand.style.backgroundColor
                };
                const originalModalDialogStyles = {
                    maxWidth: modalDialog.style.maxWidth,
                    minWidth: modalDialog.style.minWidth
                };
                const originalModalBodyStyles = {
                    overflowY: modalBody.style.overflowY,
                    height: modalBody.style.height
                };
                // Temporarily adjust styles for capture
                modalDialog.style.maxWidth = 'none';
                modalDialog.style.minWidth = '1240px'; // Ensure base width
                modalBody.style.overflowY = 'visible';
                modalBody.style.height = 'auto'; // Let height be determined by content
                stand.style.width = '1240px'; // Or whatever full width you expect
                stand.style.height = 'auto'; // Crucial: allow content to define height
                stand.style.overflow = 'visible'; // Ensure no internal clipping
                stand.style.backgroundColor = 'rgb(231,230,232)'; // Set desired background
                try {
                    // Wait for styles to apply (brief pause) - sometimes necessary
                    await new Promise(resolve => setTimeout(resolve, 50));
                    const canvas = await html2canvas(stand, {
                        scale: 1, // Consider increasing for better quality, e.g., 2 or 3, but increases file size
                        backgroundColor: 'rgb(231,230,232)',
                        useCORS: true,
                    });
                    const imgData = canvas.toDataURL('image/png', 1); // Use 0.9 for slightly smaller file size, 1 is max quality
                    // Calculate PDF dimensions based on canvas
                    const pdfWidth = canvas.width;
                    const pdfHeight = canvas.height;
                    const pdf = new jsPDF('p', 'px', [pdfWidth, pdfHeight]); // Use 'px' for units
                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight, '', 'FAST');
                    // Get the modal title and sanitize it for filename
                    let title = document.getElementById('modal-title').textContent || 'Stand Overview';
                    title = title.replace(/[^\w\d\-]+/g, '_'); // Replace non-alphanumeric with underscores
                    pdf.save(title + '.pdf');
                } catch (err) {
                    console.error('Error generating PDF:', err);
                } finally {
                    // Revert styles to original state
                    Object.assign(stand.style, originalStandStyles);
                    Object.assign(modalDialog.style, originalModalDialogStyles);
                    Object.assign(modalBody.style, originalModalBodyStyles);
                }
            });
        })();
        </script>
    <?php }

    /* ───────── assets ───────── */

    private function enqueue_assets(): void {
        wp_enqueue_style( 'dt-css', 'https://cdn.datatables.net/1.13.10/css/dataTables.bootstrap5.min.css', [], null );
        wp_enqueue_script( 'dt-js',  'https://cdn.datatables.net/1.13.10/js/jquery.dataTables.min.js', [ 'jquery' ], null, true );
        wp_enqueue_script( 'dt-bs',  'https://cdn.datatables.net/1.13.10/js/dataTables.bootstrap5.min.js', [ 'dt-js' ], null, true );
        wp_enqueue_script(
            'html2canvas',
            'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js',
            [],
            null,
            true
        );
        wp_enqueue_script(
            'jspdf',
            'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js',
            [],
            null,
            true
        );
        wp_enqueue_script(
            'desymphony-admin-users',
            get_stylesheet_directory_uri() . '/dist/js/admin-users.js',
            [ 'jquery', 'dt-bs' ],
            filemtime( get_stylesheet_directory() . '/dist/js/admin-users.js' ),
            true
        );
    }

    /* ───────── ajax: get user ───────── */

    public function ajax_get_user(): void {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        $id   = absint( $_POST['user_id'] ?? 0 );
        $user = get_user_by( 'ID', $id );
        if ( ! $user || ! DS_Utils::can_manage_user( $user ) ) {
            wp_die();
        }
        /* Collect all user‑meta once (same as before) */
        $meta = array_map(
            fn ( $v ) => maybe_unserialize( $v[0] ),
            get_user_meta( $id )
        );
        /* Build the subset used in the Verify modal ------------- */
        $role     = reset( $user->roles );
        $editKeys = self::EDITABLE_META;
        if ( $role !== 'exhibitor' ) {
            unset(
                $editKeys['wv_ex_stage1_verified'],
                $editKeys['wv_ex_stage2_verified'],
                $editKeys['has_reserved_stand']
            );
        }
        if ( ! in_array( $role, [ 'buyer', 'visitor' ], true ) ) {
            unset( $editKeys['wv_wvhb_support'] );
        }
        $verify_meta = [];
        foreach ( $editKeys as $k => $_ ) {
            $verify_meta[ $k ] = $meta[ $k ] ?? self::EDITABLE_DEFAULTS[ $k ];
        }
        /* Render the front‑end profile overview *once* and return
         * the HTML string so JS can drop it into the modal body.
         */
        if ( ! function_exists( 'ds_render_profile_overview' ) ) {
            require_once DS_THEME_DIR . '/inc/public/views/partials/profile-overview.php';
        }
        ob_start();
        ds_render_profile_overview( $user->ID, false );   // read‑only in admin
        $profile_html = ob_get_clean();
        wp_send_json_success( [
            'user'        => [
                'ID'           => $user->ID,
                'display_name' => $user->display_name,
                'role'         => $role,
            ],
            'meta'         => $meta,
            'verify_meta'  => $verify_meta,
            'labels'       => self::labels_map(),
            'profile_html' => $profile_html,              // ← NEW
        ] );
    }

    /* ───────── ajax: get stand overview (NEW) ───────── */

    public function ajax_get_stands(): void {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        $id   = absint( $_POST['user_id'] ?? 0 );
        $user = get_user_by( 'ID', $id );
        if ( ! $user || reset( $user->roles ) !== 'exhibitor' ) {
            wp_send_json_error( __( 'Not an exhibitor.', DS_THEME_TEXTDOMAIN ) );
        }
        $meta   = array_map( fn ( $v ) => maybe_unserialize( $v[0] ), get_user_meta( $id ) );
        $stage1 = ( $meta['wv_ex_stage1_verified'] ?? '0' ) === '1';
        if ( ! $stage1 ) {
            wp_send_json_error( __( 'User has not completed Stage 1.', DS_THEME_TEXTDOMAIN ) );
        }
        /* Render the new partial */
        if ( ! function_exists( 'ds_render_stand_overview' ) ) {
            require_once DS_THEME_DIR . '/inc/public/views/partials/stand-overview.php';
        }
        ob_start();
        ds_render_stand_overview( $id );
        $html = ob_get_clean();
        wp_send_json_success( $html );
    }

    /* ───────── ajax: save meta ───────── */

    public function ajax_save_user(): void {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        $id   = absint( $_POST['user_id'] ?? 0 );
        $user = get_user_by( 'ID', $id );
        $pairs = $_POST['meta'] ?? [];
        foreach ( $pairs as $key => $val ) {
            // Skip read‑only keys, but **allow** keys that appear in the verify modal
            if ( in_array( $key, self::READ_ONLY_META, true ) ||
                 ( in_array( $key, self::EXCLUDED_META, true ) &&
                   ! isset( self::EDITABLE_META[ $key ] ) ) ) {
                continue;
            }
            /* basic sanitisation */
            if ( is_array( $val ) ) { $val = array_map( 'sanitize_text_field', $val ); }
            else                    { $val = sanitize_text_field( $val ); }
            if ( $val === 'true' )  { $val = '1'; }
            if ( $val === 'false' ) { $val = '0'; }
            update_user_meta( $id, $key, $val );
        }
        wp_send_json_success( 'Saved.' );
    }

    /* ───────── ajax: delete user ───────── */

    public function ajax_delete_user(): void {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        $id   = absint( $_POST['user_id'] ?? 0 );
        $user = get_user_by( 'ID', $id );
        wp_delete_user( $id );
        wp_send_json_success( 'Deleted.' );
    }

    /* ───────── ajax: set admin scope ───────── */

    public function ajax_set_scope(): void {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        if ( ! $this->is_super_editor() ) { wp_die(); }
        $id    = absint( $_POST['user_id'] ?? 0 );
        $scope = sanitize_text_field( $_POST['scope'] ?? '' );
        if ( ! in_array( $scope, [ 'all', 'exhibitors', 'buyers_visitors' ], true ) ) {
            wp_send_json_error( 'Bad scope.' );
        }
        update_user_meta( $id, 'wv_admin_scope', $scope );
        wp_send_json_success( 'Scope saved.' );
    }

    /* ───────── ajax: send transactional e‑mail ▲ ───────── */

    public function ajax_send_notice(): void {
        check_ajax_referer( 'wv_admin_users', 'nonce' );
        $id       = absint( $_POST['user_id'] ?? 0 );
        $template = sanitize_text_field( $_POST['template'] ?? '' );
        $user = get_user_by( 'ID', $id );
        /* ------ CUSTOM, uses DS_Utils::email_template() */
        if ( $template === 'custom' ) {
            $subject   = wp_strip_all_tags( $_POST['subject'] ?? '' );
            $title     = wp_strip_all_tags( $_POST['title']   ?? '' );
            $html      = wp_kses_post(      $_POST['html']    ?? $_POST['custom_body'] ?? '' );
            $note      = wp_kses_post(      $_POST['note']    ?? '' );
            $btn_text  = sanitize_text_field( $_POST['btn_text'] ?? 'Go to website' );
            $btn_link  = esc_url_raw(        $_POST['btn_link'] ?? home_url('/') );
            if ( $html === '' ) {
                wp_send_json_error( __( 'Empty body.', DS_THEME_TEXTDOMAIN ) );
            }
            /* Wrap user-written HTML in the global e-mail shell */
            [ $subj, $body ] = DS_Utils::email_template(
                $subject ?: get_bloginfo( 'name' ) . ' – notification',
                [
                    'title'        => sprintf( __( 'Dear %s %s', DS_THEME_TEXTDOMAIN ),
                        $user->first_name, $user->last_name ),
                    'bg'           => '#0b051c',
                    'logo_variant' => 'W',
                ],
                [
                    'title'         => $title ?: $subject,
                    'html'          => $html,
                    'note'          => $note,
                    'btn_text'      => $btn_text,
                    'btn_link'      => $btn_link,
                    'btn_bg'        => '#0b051c',
                    'btn_text_color'=> '#ffffff',
                ]
            );
            /* Send with proper headers */
            $headers = [
                'Content-Type: text/html; charset=UTF-8',
                'From: Wine Vision 2025 <no-reply@winevisionfair.com>'
            ];
            wp_mail( $user->user_email, $subj, $body, $headers );
            wp_send_json_success( __( 'E-mail sent.', DS_THEME_TEXTDOMAIN ) );
        }
        /* ------ pre‑defined templates handled elsewhere */
        if ( ! method_exists( $this, 'tpl_' . $template ) ) {
            wp_send_json_error( __( 'Unknown template.', DS_THEME_TEXTDOMAIN ) );
        }
        [$subj, $body] = call_user_func( [ $this, 'tpl_' . $template ], $user );
        wp_mail( $user->user_email, $subj, $body, [ 'Content-Type: text/html; charset=UTF-8' ] );
        wp_send_json_success( __( 'E‑mail sent.', DS_THEME_TEXTDOMAIN ) );
    }

    private function array_flat( $v ) {
        return is_array($v) ? implode(', ', array_map('strval', $v)) : $v;
    }

    /* ───────── helper: stand IDs (sold only) ───────── */

    private function stand_ids_for_user( \WP_User $u ): array {
        if ( reset( $u->roles ) !== 'exhibitor' ) {
            return [];
        }
        return get_posts( [
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                [
                    'key'   => 'wv_stand_status',
                    'value' => 'sold',
                ],
                [
                    'relation' => 'OR',
                    [
                        'key'   => 'wv_reservation_user',
                        'value' => $u->ID,
                    ],
                    [
                        'key'   => 'wv_reservation_email',
                        'value' => $u->user_email,
                    ],
                ],
            ],
        ] );
    }

    /* ───────── helper: stands list (NEW) ───────── */

    private function stands_for_user( \WP_User $u, array $meta ): array {
        /* Only exhibitors have stands */
        if ( reset( $u->roles ) !== 'exhibitor' ) {
            return [];
        }
        $stage1 = ( $meta['wv_ex_stage1_verified'][0] ?? '0' ) === '1';
        $status = $stage1 ? 'sold' : 'reserved';           // what we are looking for
        $badge  = $stage1 ? 'bg-success' : 'bg-primary';   // green | blue
        $q = get_posts( [
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                [
                    'key'   => 'wv_stand_status',
                    'value' => $status,
                ],
                [
                    'relation' => 'OR',
                    [
                        'key'   => 'wv_reservation_user',
                        'value' => $u->ID,
                    ],
                    [
                        'key'   => 'wv_reservation_email',
                        'value' => $u->user_email,
                    ],
                ],
            ],
        ] );
        /* Return an array of ready‑to‑print badge <span>s */
        return array_map(
            fn ( $pid ) => sprintf(
                '<span class="badge %s me-1">%s</span>',
                $badge,
                esc_html( get_the_title( $pid ) )
            ),
            $q
        );
    }

    /* ───────── helper: badges ───────── */

    private function flags_for_user( array $m ): array {
        $b = [];
        if ( ( $m['wv_admin_verified'][0]     ?? '' ) === '1' ) $b[] = '<span class="badge bg-success">Verified</span>';
        if ( ( $m['wv_ex_stage1_verified'][0] ?? '' ) === '1' ) $b[] = '<span class="badge bg-info">Stage 1</span>';
        if ( ( $m['wv_ex_stage2_verified'][0] ?? '' ) === '1' ) $b[] = '<span class="badge bg-info">Stage 2</span>';
        if ( ( $m['has_reserved_stand'][0]    ?? '' ) === '1' ) $b[] = '<span class="badge bg-primary">Stand</span>';
        $hb = $m['wv_wvhb_support'][0] ?? 'NONE';
        if ( $hb && $hb !== 'NONE' && $hb !== 'Not Applyed' ) {
            $b[] = '<span class="badge bg-warning text-dark">'. esc_html( $hb ) .'</span>';
        }
        return $b;
    }

} // class end