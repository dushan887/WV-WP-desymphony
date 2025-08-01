<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

use Desymphony\Helpers\DS_Utils as Utils;
use Desymphony\Dashboard\DS_CoEx_Manager;



?>

<div class="container container-1024">
<section class="d-block pt-24">
   <div class="row">
      <div class="col-12">
         <div class="wv-card wv-flex-column br-12 wv-bg-w">
            <div class="wv-card-header p-24 d-flex wv-justify-between wv-align-start" style="border-bottom: 2px solid #eee;">
               <h4 class="m-0 fs-20 fw-600 lh-1-5 ls-3"><?php esc_html_e( 'SPECIFICATIONS', 'wv-addon' ); ?></h4>
            </div>
            <div class="wv-card-body p-24">
               <?php
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




