<?php
$stands = require __DIR__ . '/../../stands/stands.php';
$img_base = '/wp-content/themes/desymphony/src/images/stands/';
?>

<?php foreach ($stands as $standKey => $stand): ?>
  <?php
    $hallString = implode('|', $stand['halls']);
    $modalId = 'dsStandModal-' . esc_attr($standKey);
  ?>
  <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content mt-64">

        <div class="modal-header p-0 m-0">
          <div class="card-header w-100 d-flex align-items-center justify-content-between flex-wrap py-24 px-24">
              <div>
                  <?php if ($standKey === 'custom'): ?>
                        <span class="h3 fw-600">Custom stand</span>
                  <?php else: ?>                    
                    <span class="h3 fw-600"><?= esc_html($stand['size']) ?>m<sup>2</sup> Stand</span> 
                    <span class="h3 fw-600 d-none"><?= esc_html(implode(', ', $stand['halls'])) ?></span>
                  <?php endif; ?>
              </div>
              <ul class="nav nav-pills gap-12" id="tab-<?= $standKey ?>" role="tablist">
                  <?php if ($standKey !== 'custom'): ?>
                  <li class="nav-item d-none d-lg-block" role="presentation">
                      <button class="wv-button wv-button-c br-32 wv-button-md wv-button--outline nav-link active" id="equipment-<?= $standKey ?>-tab" data-bs-toggle="pill" data-bs-target="#equipment-<?= $standKey ?>" type="button" role="tab">
                          <span class="d-none d-lg-block">Model overview</span>
                          <span class="d-block d-lg-none">Model</span>
                      </button>
                  </li>
                  <li class="nav-item d-none d-lg-block" role="presentation">
                      <button class="wv-button wv-button-c br-32 wv-button-md wv-button--outline nav-link" id="branding-<?= $standKey ?>-tab" data-bs-toggle="pill" data-bs-target="#branding-<?= $standKey ?>" type="button" role="tab">
                          <span class="d-none d-lg-block">Branding positions</span>
                          <span class="d-block d-lg-none">Branding</span>
                      </button>
                  </li>
                  <li class="nav-item d-block d-lg-none" role="presentation">
                      <button class="wv-button wv-button-c br-32 wv-button-sm wv-button--outline px-12 py-8 nav-link active" id="equipment-<?= $standKey ?>-tab" data-bs-toggle="pill" data-bs-target="#equipment-<?= $standKey ?>" type="button" role="tab">
                          <span class="d-none d-lg-block">Model overview</span>
                          <span class="d-block d-lg-none">Model</span>
                      </button>
                  </li>
                  <li class="nav-item d-block d-lg-none" role="presentation">
                      <button class="wv-button wv-button-c br-32 wv-button-sm wv-button--outline px-12 py-8 nav-link" id="branding-<?= $standKey ?>-tab" data-bs-toggle="pill" data-bs-target="#branding-<?= $standKey ?>" type="button" role="tab">
                          <span class="d-none d-lg-block">Branding positions</span>
                          <span class="d-block d-lg-none">Branding</span>
                      </button>
                  </li>
                  <?php endif; ?>
                  <li class="nav-item">
                      <button type="button" class="wv-button wv-icon-button fs-32 br-32 wv-button-c" data-bs-dismiss="modal" aria-label="Close"><span class="wv wv_x-70-o"></span></button>
                  </li>
              </ul>
          </div>
          
        </div>

        <div class="modal-body"> 
              <div class="tab-content" id="tabContent-<?= $standKey ?>">
                  <!-- Tab 1: Equipment -->
                  <?php if ($standKey === 'custom'): ?>
                    <div class="tab-pane fade show active" id="equipment-<?= $standKey ?>" role="tabpanel">
                        <div class="row">
                            <!-- Slider -->
                            <div class="col-lg-6 mb-4 mb-lg-0">                               
                                <?php if (!empty($stand['blueprint_img'])): ?>                                                
                                    <img src="<?= $img_base . $stand['blueprint_img'] ?>" class="img-fluid d-block br-4" alt="Blueprint">
                                <?php endif; ?>
                            </div>
                            <!-- Included Equipment -->
                            <div class="col-lg-6">
                                    <?php if (!empty($stand['description'])): ?>                                         
                                        <div class="d-block br-4 mb-12 p-24 wv-bg-c_5">                                               
                                            <p class="my-0"><?= $stand['description'] ?></p>                                        
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($stand['note'])): ?>
                                        <div class="d-block border wv-bc-c_5 wv-bg-c_5 py-12 ps-40 pe-12 position-relative text-start br-4">
                                            <span class="wv wv_info position-absolute top-0 start-0 mt-8 ms-8 fs-24"><span class="path1"></span><span class="path2"></span></span>
                                            <p class="fs-14 m-0"><?= $stand['note'] ?></p>
                                        </div>
                                    <?php endif; ?>
                            </div>
                        </div>
                    </div>
                  <?php else: ?>
                  <div class="tab-pane fade show active" id="equipment-<?= $standKey ?>" role="tabpanel">
                      <div class="row">
                          <!-- Slider -->
                          <div class="col-lg-8 mb-4 mb-lg-0">
                              <?php if (!empty($stand['slider_imgs'])): ?>
                                  <div id="carousel-<?= $standKey ?>" class="carousel slide" data-bs-ride="carousel">
                                      <?php if (count($stand['slider_imgs']) > 1): ?>
                                          <div class="carousel-indicators">
                                              <?php foreach ($stand['slider_imgs'] as $idx => $img): ?>
                                                  <button type="button"
                                                          data-bs-target="#carousel-<?= $standKey ?>"
                                                          data-bs-slide-to="<?= $idx ?>"
                                                          class="<?= $idx === 0 ? 'active' : '' ?>"
                                                          aria-current="<?= $idx === 0 ? 'true' : 'false' ?>"
                                                          aria-label="Slide <?= $idx + 1 ?>"></button>
                                              <?php endforeach; ?>
                                          </div>
                                      <?php endif; ?>
                                      <div class="carousel-inner rounded shadow">
                                          <?php foreach ($stand['slider_imgs'] as $idx => $img): ?>
                                              <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                                                  <div class="d-block ratio ratio-4x3" 
                                                      style="background: url('<?= $img_base . $img ?>') center center / cover no-repeat;">
                                                  </div>
                                              </div>

                                          <?php endforeach; ?>
                                      </div>
                                      <!-- No arrows needed for bullets-only navigation -->
                                  </div>
                              <?php endif; ?>

                          </div>
                          <!-- Included Equipment -->
                          <div class="col-lg-4">
                              <div class="wv-input-group mb-12">
                                  <div class="ds-stand-info-box d-flex w-100 align-items-center justify-content-between br-4 wv-bg-c_90 wv-bc-t">
                                      <span class="ds-stand-info-label lh-1 py-12 wv-color-w fw-600 ls-2"><?php esc_html_e('EQUIPMENT INCLUDED', 'wv-addon'); ?></span>
                                  </div>
                              </div>
                              <?php foreach ($stand['included'] as $item): ?>
                                  <div class="wv-input-group mb-8">
                                      <div class="ds-stand-info-box d-flex w-100 align-items-center justify-content-between br-4 wv-bg-c_10 wv-bc-t ">
                                          <span class="ds-stand-info-label lh-1 py-12 wv-color-c_95">
                                              <?= esc_html($item['label']) ?>
                                              <?php if ($item['value']): ?>
                                                  <span class="fw-600"><?= esc_html($item['value']) ?></span>
                                              <?php endif; ?>
                                          </span>
                                          <?php if ($item['qty']): ?>
                                              <span class="ds-stand-info-no wv-bg-g wv-color-w"><?= (int)$item['qty'] ?></span>
                                          <?php endif; ?>
                                      </div>
                                  </div>
                              <?php endforeach; ?>

                          </div>
                      </div>
                  </div>

                  <?php endif; ?>
                  <?php if ($standKey !== 'custom'): ?>
                  <!-- Tab 2: Branding -->
                  <div class="tab-pane fade" id="branding-<?= $standKey ?>" role="tabpanel">
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
                  </div> <!-- /tab 2 -->
                  <?php endif; ?>
              </div>
        </div>

     </div><!-- /modal-body -->

      </div><!-- /modal-content -->
    </div><!-- /modal-dialog -->
  </div><!-- /modal -->
<?php endforeach; ?>
    