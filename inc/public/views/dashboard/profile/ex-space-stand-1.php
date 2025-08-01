<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

use Desymphony\Helpers\DS_Utils as Utils;
use Desymphony\Dashboard\DS_CoEx_Manager;

/* ─────────────────────────────────────────────────────────
 * 1.  DATA SOURCES
 * ───────────────────────────────────────────────────────*/
$stands       = include DS_THEME_DIR . '/inc/public/views/stands/stands.php';
$user_id      = get_current_user_id();
$role         = Utils::get_exhibitor_participation();
$user_halls = ( $role === 'Co-Exhibitor' )
    ? \Desymphony\Woo\DS_Woo_Stand_Cart::get_assigned_stands_by_hall( $user_id )
    : \Desymphony\Woo\DS_Woo_Stand_Cart::get_user_stands_by_hall( $user_id );


$first_hall   = $user_halls ? array_key_first( $user_halls ) : '';
$hall_rows    = $first_hall ? $user_halls[ $first_hall ] : [];
$first_row    = $hall_rows   ? reset( $hall_rows )        : [];
$total_stands = array_sum( array_map( 'count', $user_halls ) );

/* ─────────────────────────────────────────────────────────
 * 2.  BUILD CO‑EXHIBITOR INFO  (for JS dropdown)
 *     structure per entry:
 *     [
 *       id          => (int)   WP‑user‑ID
 *       name        => (string)Full name
 *       is_head     => (bool)  true for main account
 *       accepted    => (bool)  true when invite accepted
 *       assigned_to => (string)Stand "2C/24" or ''
 *     ]
 * ───────────────────────────────────────────────────────*/

/* --- 2a) HEAD EXHIBITOR ---------------------------------------- */
$main_user        = get_userdata( $user_id );
$main_name        = Utils::get_company_name( $user_id );
if ( $main_name === '' ) { $main_name = $main_user->display_name; }

$co_exhibitors = [
   [
      'id'          => $user_id,
      'name'        => $main_name,
      'is_head'     => true,
      'accepted'    => true,
      'assigned_to' => (string) get_user_meta( $user_id, 'ds_assigned_stand_no', true ),
   ]
];

/* --- 2b) ACCEPTED CO‑EXHIBITORS -------------------------------- */
global $wpdb;
$table = $wpdb->prefix . DS_CoEx_Manager::TABLE;

/* pick only *accepted* rows that have a linked WP user */
$rows = $wpdb->get_results(
   $wpdb->prepare(
      "SELECT co_id, stand_code
         FROM {$table}
        WHERE exhibitor_id = %d
         AND status       = 'accepted'
         AND co_id        > 0",
      $user_id
   )
);


foreach ( $rows as $row ) {
   $cid = (int) $row->co_id;
   $u   = get_userdata( $cid );
   if ( ! $u ) { continue; }

   $name = Utils::get_company_name( $cid );
   if ( $name === '' ) { $name = $u->display_name; }

   $co_exhibitors[] = [
      'id'          => $cid,
      'name'        => $name,
      'is_head'     => false,
      'accepted'    => true,
      'assigned_to' => (string) $row->stand_code ?: (function() use ( $cid ) {
            if ( ! class_exists( '\Desymphony\Dashboard\DS_Stand_Assign' ) ) return '';
            $pids = \Desymphony\Dashboard\DS_Stand_Assign::stands_for_user( $cid );
            if ( ! $pids ) return '';

            $pid  = $pids[0];
            $hall = get_post_meta( $pid, 'wv_hall_only', true );
            $no   = get_post_meta( $pid, 'wv_stand_no',  true );
            return ( $hall && $no ) ? "Hall {$hall}/{$no}" : '';
         })(),

   ];
}


/* ─────────────────────────────────────────────────────────
 * 3.  PASS DATA TO JS
 * ───────────────────────────────────────────────────────*/
wp_localize_script(
   'desymphony-stand-assign',
   'wvUserStands',
   [ 'byHall' => $user_halls ]
);

wp_localize_script(
   'desymphony-stand-assign',
   'wvStandUsers',
   $co_exhibitors          // now includes every accepted Co‑Exhibitor
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
   
.wv-current-user.ds-stand .cls-1 {
   fill: var(--wv-g) !important;
}
.wv-current-user.ds-stand .cls-3 {
   fill: var(--wv-w) !important;
   stroke: var(--wv-g) !important;
}
.wv-current-user.ds-stand:hover .cls-3,
.wv-current-user.ds-stand.active .cls-3 {
   fill: var(--wv-g) !important;
   stroke: var(--wv-w) !important;
}
.wv-current-user.ds-stand:hover .cls-1,
.wv-current-user.ds-stand.active .cls-1 {
   fill: var(--wv-w) !important;
}
.wv-not-current-user.ds-stand .cls-3,
.wv-not-current-user.ds-stand .cls-1  {
    fill: var(--wv-w);
    pointer-events: none;
}


<?php if ( Utils::get_exhibitor_field() === 'Wine' ) : ?>
   #wv_hall_1G .ds-stand,
   #wv_hall_4B .ds-stand {
      display: none !important;
   }
<?php elseif ( Utils::get_exhibitor_field() === 'Spirits' ) : ?>
   .hall-svg:not(#wv_hall_1G) .ds-stand{
      display: none !important;
   }
<?php elseif ( Utils::get_exhibitor_field() === 'Food' ) : ?>
   .hall-svg:not(#wv_hall_4B) .ds-stand{
      display: none !important;
   }
<?php endif; ?>
<?php if ( $role === 'Co-Exhibitor' ) : ?>
<?php foreach ($wv_hall_vars as $var): ?>
#<?= esc_html($var) ?>  .cls-1{
    fill: var(--wv-g) !important; 
}
#<?= esc_html($var) ?> .cls-3 {
   fill: var(--wv-w) !important;
   stroke: var(--wv-g) !important;
}
<?php endforeach; ?>
<?php endif; ?>


</style>
<script>
/* ------------------------------------------------------------------
 *  Enrich SVG <g> nodes with data‑attrs (pid / size / no) on load
 * ---------------------------------------------------------------- */
(function ($) {
   $(function () {
      if (typeof wvUserStands === 'undefined' || !wvUserStands.byHall) return;


      Object.entries(wvUserStands.byHall).forEach(([hall, rows]) => {
         rows.forEach(st => {
            const id = '#wv_hall_' + hall + '_' + String(st.no).padStart(2,'0');
            const $g = $(id);
            if ($g.length){
               $g.attr({
                  'data-pid' : st.pid,
                  'data-hall': hall,
                  'data-no'  : st.no,
                  'data-size': st.size,
                  'data-users' : (st.assigned_users || []).join(',')
               });
            }
         });
      });
     
   });

   
})(jQuery);
</script>
<?php
/* ------------------------------------------------------------------
 *  DEBUG OUTPUT – always rendered, but hidden (d-none) with unique ID
 * ---------------------------------------------------------------- */
?>
<div id="wv-debug-output" class="d-none">
   <pre style="background:#222;color:#fff;padding:12px;margin:12px 0;">
      <strong>DEBUG (<?php echo date('Y‑m‑d H:i:s'); ?>)</strong>

<?php
/* 👉 dump anything you need here */
var_dump($user_halls, $co_exhibitors, $stands_available);
?>
   </pre>
</div>

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
                     <div class="col-6 col-lg-3">
                        <div class="wv-input-group">
                           <select id="ds-hall-select">
                              <option value=""><?php esc_html_e( 'Select Hall', 'wv-addon' ); ?></option>
                              <?php foreach ( $user_halls as $hall_slug => $rows ) : ?>
                                <option value="<?php echo esc_attr( $hall_slug ); ?>" 
                                        <?php selected( $hall_slug, $first_hall ); ?>>
                                   Hall <?php echo esc_html( $hall_slug ); ?>
                                </option>
                              <?php endforeach; ?>
                           </select>
                        </div>
                     </div>

                     <div class="col-6 col-lg-3">
                        <div class="wv-input-group">
                           <div class="ds-stand-info-box d-flex w-100 align-items-center justify-content-between br-4">
                              <span class="ds-stand-info-label">Total stands</span>
                              <span class="ds-stand-info-val"><?php echo esc_html( $total_stands ); ?></span>
                           </div>
                        </div>
                     </div>

                     

                     <?php if ( $total_stands <= 1 ) : ?>
                        <div class="col-6 col-lg-3">
                           <div class="wv-input-group">
                              <div class="ds-stand-info-box d-flex w-100 align-items-center justify-content-between br-4 wv-bg-c_70 wv-bc-t">
                                 <span class="ds-stand-info-label wv-color-w">My stand</span>
                                 <span id="my-stand-number" class="ds-stand-info-val wv-color-w wv-bc-w">
                                    <?php
                                    echo $user_halls
                                       ? implode(', ', array_map(
                                          fn($row) => $row['no'],
                                          array_merge(...array_values($user_halls))
                                       ))
                                       : '–';
                                    ?>
                                 </span>
                              </div>
                           </div>
                        </div>

                        <div class="col-6 col-lg-3">
                           <div class="wv-input-group">
                              <div class="ds-stand-info-box ds-stand-info-box-2 d-flex w-100 align-items-center justify-content-between br-4 wv-bg-o wv-bc-t">
                                 <span class="ds-stand-info-label wv-color-w">My stand size</span>
                                 <span id="my-stand-size" class="ds-stand-info-val wv-color-o me-4 white-space-nowrap">
                                    <?php echo esc_html( $first_row['size'] ?? '–' ); ?>m<sup>2</sup>
                                 </span>
                              </div>
                           </div>
                        </div>
                     <?php endif; ?>

                  </div>                  
               </div>
               <div class="row">
                  <div class="col-12">
                     <div class="hall hall-svg-container mb-16 wv-bg-c_10 my-12 br-4 ds-hall-root">
                        <div class="container container-1024 pb-24 d-flex justify-content-center align-items-center">
                           <div id="hall-content" class="w-100">…</div>
                        </div>
                     </div>
                  </div>
               </div>

               <?php if ( $role !== 'Co-Exhibitor' ) : ?>
                  <div id="ds-stand-nav-assign" class="ds-stand-nav">
                     <div class="row g-12">
                        <!-- ① selected stand badge -->
                        <div class="col-12 col-lg-3">
                           <div class="wv-input-group">
                           <div id="selected-stand-box"
                                 class="ds-stand-info-box ds-stand-info-box-2 d-flex w-100 align-items-center justify-content-between br-4 wv-bg-w wv-bc-v d-none">
                              <span class="ds-stand-info-val wv-color-v_dark ms-4 wv-bg-v_20">
                                 <span id="selected-stand-size">0</span>m<sup>2</sup>
                              </span>
                              <span class="ds-stand-info-label wv-color-v_dark">
                                 <strong>Stand <span id="selected-stand-number">–</span></strong> selected
                              </span>
                           </div>
                           </div>
                        </div>

                        <!-- ② invite dropdown (JS fills .dropdown-menu) -->
                        <div class="col-6">
                           <div class="wv-input-group">
                           <div id="ds-assign-select" class="selectBox ds-stand-info-box br-4 wv-bc-v wv-color-v_dark d-none">
                              <div class="selectBox__value fw-600">Select stand user</div>
                              <div class="dropdown-menu"></div>
                           </div>
                           </div>
                        </div>

                        <!-- ③ action buttons -->
                        <div class="col-12 col-lg-3">
                           <div class="wv-input-group">
                           <button id="wv-assign-stand" class="wv-button fw-400 br-4 d-none">Assign stand</button>
                           <button id="wv-share-stand"  class="wv-button wv-button-outline fw-400 br-4 d-none">Share stand</button>
                           <button id="wv-remove-stand" class="wv-button wv-button-red fw-400 br-4 d-none">Free up stand</button>
                           </div>
                        </div>
                     </div>
                  </div>
               <?php endif; ?>

                  
            </div>
         </div>
         <!-- End Card -->
      </div>
      <!-- End Column -->
   </div>
</section>
</div>






