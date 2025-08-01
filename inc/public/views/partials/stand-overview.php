<?php
use Desymphony\Helpers\DS_Utils as Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ds_render_stand_overview' ) ) :

function ds_render_stand_overview( int $uid ): void {  

    /* ─────────────────────────────────────────────────────────
    * 1.  DATA SOURCES
    * ───────────────────────────────────────────────────────*/
    
    $user_halls   = \Desymphony\Woo\DS_Woo_Stand_Cart::get_user_stands_by_hall( $uid );

    $first_hall   = $user_halls ? array_key_first( $user_halls ) : '';
    $hall_rows    = $first_hall ? $user_halls[ $first_hall ] : [];
    $first_row    = $hall_rows   ? reset( $hall_rows )        : [];
    $total_stands = array_sum( array_map( 'count', $user_halls ) );

    
    /* ─────────────────────────────────────────────────────────
    * 2.  BUILD CO‑EXHIBITOR INFO  (for JS dropdown)
    *     expected keys per entry:
    *       id, name, is_head (bool), accepted (bool), assigned_to (stand‑no or '')
    * ───────────────────────────────────────────────────────*/

    /* --- 2a) HEAD EXHIBITOR (the main account) ------------------------*/
    $main_user = get_userdata( $uid );
    $main_name = trim( $main_user->first_name . ' ' . $main_user->last_name );
    if ( $main_name === '' ) $main_name = $main_user->display_name;
    

    /* The head exhibitor may already have a stand “assigned to” him
    via user‑meta or similar – adjust if you store this differently. */
    $main_assigned_to = get_user_meta( $uid, 'ds_assigned_stand_no', true ); // '' if none

    $co_exhibitors   = [
        [
            'id'          => $uid,
            'name'        => $main_name,
            'is_head'     => true,
            'accepted'    => true,          // head exhibitor is always accepted
            'assigned_to' => (string) $main_assigned_to,
        ]
    ];

    /* --- 2b)  CO‑EXHIBITORS (invited users) ---------------------------*/
    /**
     * Example storage assumption:
     *   • parent user (head exhibitor) has user‑meta 'ds_co_exhibitors' = [ childID1, childID2, … ]
     *   • each child user has meta 'ds_invite_status'    = 'accepted' or 'pending'
     *                                     'ds_assigned_stand_no' = '29' (plain number) OR ''
     *
     * Adapt these two helper functions to your real data model.
     */
    $invited_ids = (array) get_user_meta( $uid, 'ds_co_exhibitors', true );

    foreach ( $invited_ids as $cid ) {
        $cid = absint( $cid );
        if ( ! $cid ) continue;

        $cuser       = get_userdata( $cid );
        if ( ! $cuser ) continue;

        $cname       = trim( $cuser->first_name . ' ' . $cuser->last_name );
        if ( $cname === '' ) $cname = $cuser->display_name;

        $accepted    = ( get_user_meta( $cid, 'ds_invite_status', true ) === 'accepted' );
        $assigned_no = (string) get_user_meta( $cid, 'ds_assigned_stand_no', true ); // '' if none

        $co_exhibitors[] = [
            'id'          => $cid,
            'name'        => $cname,
            'is_head'     => false,
            'accepted'    => $accepted,
            'assigned_to' => $assigned_no,
        ];
    }

    /* ─────────────────────────────────────────────────────────
    * 3.  PASS DATA TO JS
    * ───────────────────────────────────────────────────────*/
    wp_localize_script(
        'desymphony-stand-assign',        // script handle added in DS_Enqueue
        'wvUserStands',
        [ 'byHall' => $user_halls ]
    );

    wp_localize_script(
        'desymphony-stand-assign',
        'wvStandUsers',
        $co_exhibitors                    // now always present & populated
    );

    /* ------------------------------------------------------------------ */
    /* 4.  Build “available” arrays                                       */
    /* ------------------------------------------------------------------ */

    /** ── gather what the user actually owns ─────────────────────────── */
    $size_set       = [];     // unique sizes only
    $size_hall_hash = [];     // unique size–hall combos

    foreach ( $user_halls as $hall_slug_raw => $rows ) {
        $hall_slug = (string) $hall_slug_raw;
        foreach ( $rows as $row ) {
            $raw = strtolower( trim( $row['size'] ?? '' ) );
            $size_key = preg_match( '/^\d+$/', $raw ) ? $raw . 'm2' : 'custom';
            $size_set[ $size_key ] = true;
            if ( $size_key === 'custom' ) {
                $size_hall_hash['custom'] = true;
            } else {
                $size_hall_hash[ "{$size_key}_Hall_{$hall_slug}" ] = true;
            }
        }
    }

    $canonical_sizes   = [ '9m2','12m2','24m2','49m2','custom' ];
    $stands_available  = array_values( array_intersect( $canonical_sizes, array_keys( $size_set ) ) );
    $size_hall_combos  = [];
    foreach ( $canonical_sizes as $s ) {
        if ( $s === 'custom' ) {
            if ( isset( $size_hall_hash['custom'] ) ) $size_hall_combos[] = 'custom';
            continue;
        }
        $m = preg_grep( '/^' . preg_quote( $s, '/' ) . '_Hall_/i', array_keys( $size_hall_hash ) );
        sort( $m, SORT_NATURAL | SORT_FLAG_CASE );
        $size_hall_combos = array_merge( $size_hall_combos, $m );
    }

    /* ------------------------------------------------------------------ */
    /* 5.  Auxiliary numbers                                              */
    /* ------------------------------------------------------------------ */
    $first_hall   = $user_halls ? array_key_first( (array) $user_halls ) : '';
    $total_stands = array_sum( array_map( 'count', $user_halls ) );


    /* ------------------------------------------------------------------ */
    /* 6.  (rest of the original markup follows … nothing removed)        */
    /* ------------------------------------------------------------------ */

    $wv_hall_vars = [];
    foreach ($user_halls as $hall => $rows) {
    foreach ($rows as $row) {
        $stand_no = str_pad($row['no'], 2, '0', STR_PAD_LEFT);
        $var_name = 'wv_hall_' . $hall . '_' . $stand_no;
        $wv_hall_vars[] = $var_name;
    }
    }
    // Example: $wv_hall_vars now contains ['wv_hall_3_02', 'wv_hall_3_26', 'wv_hall_3_27']


    ?>
    <style>   
   .ds-stand .cls-3,
   .ds-stand .cls-1  {
        fill: var(--wv-c_10);
        pointer-events: none;
    }

    <?php foreach ($wv_hall_vars as $var): ?>
    #<?= esc_html($var) ?>  .cls-3{
        fill: var(--wv-g) !important; 
    }
    #<?= esc_html($var) ?> .cls-1 {
    fill: var(--wv-w) !important;
    }

    <?php endforeach; ?>
    .ds-stand * {
        pointer-events: none;
    }
    #hall-content .wv-bg-w.wv-section-box-shadow {
        box-shadow: none !important;
        margin-bottom: 24px !important;
    }
    #hall-content .py-48.wb-bg-c_10 {
        display: none !important
    }
    </style>

    <div class="container container-1024">
        <section class="d-block pt-24">
            <div class="row">
                <div class="col-12">
                    <div class="wv-card wv-flex-column br-12 wv-bg-w">
                    <div class="wv-card-header p-24 d-flex wv-justify-between wv-align-start" style="border-bottom: 2px solid #eee;">
                        <h4 class="m-0 fs-20 fw-600 lh-1-5 ls-3"><?php esc_html_e( 'OVERVIEW', 'wv-addon' ); ?></h4>
                    </div>
                    <div class="wv-card-body p-24">
                        <div class="ds-stand-nav">
                            <div class="row g-12">

                                <div class="col-6 col-lg-4">
                                <div class="wv-input-group">
                                    <div class="ds-stand-info-box d-flex w-100 align-items-center justify-content-between br-4">
                                        <span class="ds-stand-info-label">Total stands</span>
                                        <span class="ds-stand-info-val"><?php echo esc_html( $total_stands ); ?></span>
                                    </div>
                                </div>
                                </div>

                                <div class="col-6 col-lg-4">
                                <div class="wv-input-group">
                                    <div class="ds-stand-info-box d-flex w-100 align-items-center justify-content-between br-4 wv-bg-c_70 wv-bc-t">
                                        <span class="ds-stand-info-label wv-color-w">My stand</span>
                                        <span id="my-stand-number" class="ds-stand-info-val wv-color-w wv-bc-w">
                                            <?php
                                            echo $user_halls
                                                ? reset(array_merge(...array_values($user_halls)))['no']
                                                : '–';
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                </div>

                                <div class="col-6 col-lg-4">
                                <div class="wv-input-group">
                                    <div class="ds-stand-info-box ds-stand-info-box-2 d-flex w-100 align-items-center justify-content-between br-4 wv-bg-o wv-bc-t">
                                        <span class="ds-stand-info-label wv-color-w">My stand size</span>
                                        <span id="my-stand-size" class="ds-stand-info-val wv-color-o me-4 white-space-nowrap">
                                            <?php echo esc_html( $first_row['size'] ?? '–' ); ?>m<sup>2</sup>
                                        </span>
                                    </div>
                                </div>
                                </div>

                            </div>                  
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="hall hall-svg-container mb-16 wv-bg-c_10 my-12 br-4 ds-hall-root">
                                    <div id="hall-content" class="w-100">
                                        <?php
                                            /* --------------------------------------------------
                                            * Render *all* halls that this user actually owns.
                                            * ------------------------------------------------*/
                                            $hall_order = require DS_THEME_DIR . '/inc/public/views/halls/halls-order.php';
                                            $halls_map  = \Desymphony\Woo\DS_Woo_Stand_Map::get_map();

                                            foreach ( $hall_order as $slug ) {
                                                if ( empty( $user_halls[ $slug ] ) ) {
                                                    continue;                       // no stands in this hall
                                                }

                                                /* --- variables expected by hall-template.php ------------- */
                                                $current_slug       = $slug;
                                                $current_hall_label = 'Hall ' . $slug;

                                                /* SVG markup */
                                                $svg_file = DS_THEME_DIR . "/inc/public/views/halls/hall-{$slug}.svg";
                                                $hall_svg = file_exists( $svg_file ) ? file_get_contents( $svg_file ) : '';

                                                /* Stands data for this hall */
                                                $stands = $halls_map[ $slug ] ?? [];

                                                /* We show halls stacked, so prev/next nav is irrelevant; use dummies. */
                                                $prev_hall_slug = $next_hall_slug = '';

                                                /* Render exactly the same partial used on the public map */
                                                require DS_THEME_DIR . '/templates/fair-map/partials/hall-template.php';
                                            }
                                        ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                        
                    </div>
                    </div>
                    <!-- End Card -->
                </div>
                <!-- End Column -->
            </div>
        </section>


        <section class="d-block pt-24">
            <div class="row">
                <div class="col-12">
                    <div class="wv-card wv-flex-column br-12 wv-bg-w">
                    <div class="wv-card-header p-24 d-flex wv-justify-between wv-align-start" style="border-bottom: 2px solid #eee;">
                        <h4 class="m-0 fs-20 fw-600 lh-1-5 ls-3"><?php esc_html_e( 'SPECIFICATIONS', 'wv-addon' ); ?></h4>
                    </div>
                    <div class="wv-card-body p-24">
                        <?php
                            $stands       = include DS_THEME_DIR . '/inc/public/views/stands/stands.php';
                            $spec_map = [];
                            foreach ($stands as $key => $spec) {
                                if ($key === 'custom') {
                                $spec_map['custom'] = $spec;
                                continue;
                                }
                                if (!preg_match('/^(\d+)m2/', $key, $m)) {
                                continue;
                                }
                                $size_key = $m[1] . 'm2';
                                foreach ($spec['halls'] as $label) {
                                $slug = trim(str_ireplace('Hall', '', $label));
                                $spec_map["{$size_key}_Hall_{$slug}"] = $spec;
                                }
                            }
                            
                            $combo_specs = [];
                            foreach ($size_hall_combos as $c) {
                                if (isset($spec_map[$c])) {
                                $combo_specs[$c] = $spec_map[$c];
                                }
                            }
                            
                            foreach ($combo_specs as $combo => $stand):
                                [$size_label, $hall_label] = array_pad(explode('_Hall_', $combo, 2), 2, '');
                            ?>
                        <div class="ds-stand-nav mb-24" data-combo="<?php echo esc_attr($combo); ?>">
                            <?php 
                                // Include the stand profile partial and pass the stand data
                                $standKey = $combo; // Use the combo as the stand key
                                $stand = $stand;    // The stand data from $combo_specs
                                $img_base = '/wp-content/themes/desymphony/src/images/stands/';
                                $hallString = implode('|', $stand['halls']);
                                $modalId = 'dsStandModal-' . esc_attr($standKey);
                                
                                ?>
                            <?php if ($standKey === 'custom'): ?>
                            <div class="row g-12">
                                <div class="col-lg-6 mb-12 mb-lg-0">
                                <div class="d-block br-8 wv-bg-c_10 overflow-hidden">
                                    <?php if (!empty($stand['blueprint_img'])): ?>                                                
                                    <img src="<?= $img_base . $stand['blueprint_img'] ?>" class="img-fluid d-block" alt="Blueprint">
                                    <?php endif; ?>
                                </div>
                                <div class="row g-12">
                                    <?php foreach ($stand['branding_labels'] as $lbl): ?>
                                    <div class="col pt-12">
                                        <div class="wv-input-group">
                                            <div class="ds-stand-info-box ds-stand-info-box-2 d-flex w-100 align-items-center justify-content-between br-4 fs-12 <?php echo esc_html($lbl[2]); ?> wv-bc-t">
                                            <span class="ds-stand-info-label lh-1 py-12 wv-color-w w-auto"><?php echo esc_html($lbl[0]); ?></span>
                                            <span class="ds-stand-info-label lh-1 py-12 wv-color-w w-auto"><?php echo esc_html($lbl[1]); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                </div>
                                <div class="col-lg-6">
                                <div class="d-block br-4 mb-12 p-24 wv-bg-c_5">
                                    <p class="my-0">With the custom stand option, an exhibitor secures dedicated raw space, offering unparalleled creative freedom. Exhibitors have the opportunity to contract with Belgrade Fair’s architectural experts and production team, which can provide a turnkey service for constructing a unique and fully customized exhibition stand, designed to maximize their brand’s presence and ensure outstanding performance at the 2025 fair.</p>
                                </div>
                                <div class="d-block border wv-bc-c_5 wv-bg-c_5 py-12 ps-40 pe-12 position-relative text-start br-4">
                                    <span class="wv wv_info position-absolute top-0 start-0 mt-8 ms-8 fs-24"><span class="path1"></span><span class="path2"></span></span>
                                    <p class="fs-14 m-0"><strong>Important:</strong> The custom stand option exclusively implies the purchase of a designated size raw space. Upon purchasing a custom stand, a representative from the Belgrade Fair Production Department team will contact you as soon as possible.</p>
                                </div>
                                </div>
                            </div>
                            <?php else : ?>
                            <div class="row g-12">
                                <!-- Slider -->
                                <div class="col-lg-6 mb-4 mb-lg-0">
                                <div class="row g-12">
                                    <div class="col-6">
                                        <div class="wv-input-group">
                                            <div class="ds-stand-info-box ds-stand-info-box-2 d-flex w-100 align-items-center justify-content-between br-4 wv-bg-<?= $stand['size'] ?>m2 wv-bc-t">
                                            <span class="ds-stand-info-label wv-color-w">Stand size</span>
                                            <span class="ds-stand-info-val wv-color-<?= $stand['size'] ?>m2 me-4 white-space-nowrap">
                                            <?= $stand['size'] ?>m<sup>2</sup>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="wv-input-group">
                                            <div class="ds-stand-info-box d-flex w-100 align-items-center justify-content-between br-4">
                                            <span class="ds-stand-info-label">Total stands</span>
                                            <?php
                                            // Count the number of stands the user owns for this size/hall combo
                                            $count = 0;
                                            if (preg_match('/^(\d+m2)_Hall_(.+)$/i', $combo, $matches)) {
                                                $size = $matches[1];
                                                $hall = $matches[2];
                                                if (isset($user_halls[$hall])) {
                                                    foreach ($user_halls[$hall] as $row) {
                                                        $row_size = preg_match('/^\d+$/', strtolower(trim($row['size'] ?? ''))) ? $row['size'] . 'm2' : 'custom';
                                                        if ($row_size === $size) {
                                                        $count++;
                                                        }
                                                    }
                                                }
                                            } elseif ($combo === 'custom') {
                                                // Count custom stands across all halls
                                                foreach ($user_halls as $rows) {
                                                    foreach ($rows as $row) {
                                                        $row_size = preg_match('/^\d+$/', strtolower(trim($row['size'] ?? ''))) ? $row['size'] . 'm2' : 'custom';
                                                        if ($row_size === 'custom') {
                                                        $count++;
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                            <span class="ds-stand-info-val"><?= (int)$count ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 pt-12">
                                        <?php if (!empty($stand['slider_imgs'])): ?>
                                        <div class="d-block ratio ratio-4x3 rounded shadow"
                                            style="background: url('<?= $img_base . $stand['slider_imgs'][0] ?>') center center / cover no-repeat;">
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                </div>
                                <!-- Included Equipment -->
                                <div class="col-lg-6">
                                <div class="wv-input-group mb-12">
                                    <div class="ds-stand-info-box d-flex w-100 align-items-center justify-content-between br-4 wv-bg-c_90 wv-bc-t">
                                        <span class="ds-stand-info-label lh-1 py-12 wv-color-w fw-600 ls-2"><?php esc_html_e('EQUIPMENT INCLUDED', 'wv-addon'); ?></span>
                                    </div>
                                </div>
                                <?php foreach ($stand['included'] as $item): ?>
                                <div class="wv-input-group mb-8">
                                    <div class="ds-stand-info-box d-flex w-100 align-items-center justify-content-between br-4 wv-bg-c_10 wv-bc-t ">
                                        <span class="ds-stand-info-label lh-1 py-8 wv-color-c_95 fs-14">
                                        <?= esc_html($item['label']) ?>
                                        <?php if ($item['value']): ?>
                                        <span class="fw-600"><?= esc_html($item['value']) ?></span>
                                        <?php endif; ?>
                                        </span>
                                        <?php if ($item['qty']): ?>
                                        <span class="ds-stand-info-no wv-bg-w wv-color-c"><?= (int)$item['qty'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="row pt-12 g-12">
                                <!-- Slider -->
                                <div class="col-lg-6 mb-4 mb-lg-0">    
                                <?php if (!empty($stand['blueprint_img'])): ?>
                                <div class="d-block br-8 wv-bg-c_10 overflow-hidden">
                                    <img src="<?= $img_base . $stand['blueprint_img'] ?>" class="img-fluid d-block" alt="Blueprint">
                                </div>
                                <?php endif; ?>

                                <div class="row g-12">
                                        <?php foreach ($stand['branding_labels'] as $lbl): ?>
                                            <div class="col pt-12">
                                            <div class="wv-input-group">
                                                <div class="ds-stand-info-box ds-stand-info-box-2 d-flex w-100 align-items-center justify-content-between br-4 fs-12 <?php echo esc_html($lbl[2]); ?> wv-bc-t">
                                                    <span class="ds-stand-info-label lh-1 py-12 wv-color-w w-auto"><?php echo esc_html($lbl[0]); ?></span>
                                                    <span class="ds-stand-info-label lh-1 py-12 wv-color-w w-auto"><?php echo esc_html($lbl[1]); ?></span>
                                                </div> 
                                            </div>
                                            </div>
                                        <?php endforeach; ?>
                                            
                                            
                                </div>
                                
                                </div>
                                <!-- Included Equipment -->
                                <div class="col-lg-6">
                                    <div class="d-block br-8 overflow-hidden">
                                            <?php if (!empty($stand['branding_img'])): ?>
                                            <img src="<?= $img_base . $stand['branding_img'] ?>" class="img-fluid d-block br-8" alt="Branding">
                                            <?php endif; ?>
                                    </div>

                                    <div class="row g-12">
                                            <div class="col-12 pt-12">
                                            <div class="wv-input-group">
                                                <div class="ds-stand-info-box ds-stand-info-box-2 d-flex w-100 align-items-center justify-content-between br-4 wv-bg-c_10 wv-bc-t fs-12">
                                                        <span class="ds-stand-info-label lh-1 py-8 w-auto fw-600 d-flex align-items-center"><span class="wv wv_info fs-20 me-4"><span class="path1"></span><span class="path2"></span></span> Important </span>
                                                        <span class="ds-stand-info-label lh-1 py-12 w-auto lh-1">Print-ready logo provided by exhibitor • measures in cm</span>
                                                </div>
                                            </div>
                                            </div>
                                    </div>

                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    </div>
                    <!-- End Card -->
                </div>
                <!-- End Column -->
            </div>
        </section>
        </div>



    <!-- Stand overview for the user -->
    <?php if ( Utils::get_exhibitor_participation($uid) !== 'Co-Exhibitor' ) : ?>
    <div class="container container-1024">
    <section class="d-block pt-24">
    <div class="row">
        <div class="col-12">
            <div class="wv-card wv-flex-column br-12 wv-bg-w">
                <div class="wv-card-header p-24 d-flex wv-justify-between wv-align-start" style="border-bottom: 2px solid #eee;">
                <h4 class="m-0 fs-20 fw-600 lh-1-5 ls-3"><?php esc_html_e( 'RECEIPT', 'wv-addon' ); ?></h4>
                </div>

                <div class="wv-card-body p-24">
                <?php echo \Desymphony\Woo\DS_Woo_Stand_Cart::get_user_receipt_html( $uid ); ?>

                </div>

            </div>
            <!-- End Card -->
        </div>
        <!-- End Column -->
    </div>
    </section>
    </div>
    <?php endif;  ?>

<?php } // --- end of ds_render_stand_overview
endif; // if function exists